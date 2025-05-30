import os
from contextlib import asynccontextmanager
from fastapi import FastAPI, Depends, UploadFile, File, HTTPException
from process_file import extract_from_pdf, extract_from_image
from sqlalchemy.orm import Session
from db import get_db
from models import ChatbotLog, FAQ, UserPreference
from schema import ChatRequest, ChatResponse, Message, AnalyzeResponse, TaxSuggestionResponse
from prompt import build_prompt
from qwen import call_qwen
from vectorstore import get_top_faqs, init_faq_index_from_db
from typing import AsyncGenerator
from vectorstore import faq_collection
from calculation import TaxCalculator

@asynccontextmanager
async def lifespan(_: FastAPI) -> AsyncGenerator[None, None]:
    # Only initialize FAQ index if it's empty
    if len(faq_collection.get()['ids']) == 0:
        with next(get_db()) as db:
            faqs = db.query(FAQ).all()
            init_faq_index_from_db(faqs)
    else:
        print("[Startup] FAQ embeddings already exist in ChromaDB.")
    yield

app = FastAPI(lifespan=lifespan)

@app.post("/chat", response_model=ChatResponse)
async def chat(request: ChatRequest, db: Session = Depends(get_db)):
    user_id = request.user_id
    user_input = request.message

    # Load user chat history from DB
    db_history = db.query(ChatbotLog).filter_by(user_id=user_id).order_by(ChatbotLog.created_at.desc()).limit(10).all()
    history = []
    for log in reversed(db_history):
        history.append(Message(role="user", text=log.user_message))
        history.append(Message(role="assistant", text=log.bot_response))

    # Retrieve top 3 matching FAQs with fallback
    try:
        faqs = get_top_faqs(user_input, top_k=3)
    except Exception as e:
        faqs = []
        print(f"[Error] get_top_faqs failed: {e}")

    # Fetch profession from user_profile_preferences
    profession_pref = (
        db.query(UserPreference)
        .filter_by(user_id=user_id, question="profession")
        .first()
    )
    profession = profession_pref.answer if profession_pref else ""
    if profession:
        profession = f"Freelance {profession}"

    # Build context-aware prompt with FAQs
    prompt = build_prompt(history, user_input, faqs, profession)

    # Get response from Qwen with fallback
    try:
        answer = call_qwen(prompt)
    except Exception as e:
        print(f"[Error] call_qwen failed: {e}")
        answer = "Sorry, something went wrong while generating a response."

    # Save to DB
    log = ChatbotLog(user_id=user_id, user_message=user_input, bot_response=answer)
    db.add(log)
    db.commit()

    return ChatResponse(answer=answer)


@app.post("/analyze", response_model=AnalyzeResponse)
async def analyze_receipt(file: UploadFile = File(...)):
    extension = file.filename.split('.')[-1].lower()
    contents = await file.read()

    with open(file.filename, "wb") as f:
        f.write(contents)

    try:
        if extension == "pdf":
            result = extract_from_pdf(file.filename)
        else:
            result = extract_from_image(file.filename)
    except Exception as e:
        print(f"[Error] Extraction failed: {e}")
        os.remove(file.filename)
        raise RuntimeError("Failed to process the uploaded file. Please check the file format and content.")

    os.remove(file.filename)

    try:
        calculator = TaxCalculator()
        calculator.add_items(result)
    except Exception as e:
        print(f"[Error] Tax categorization failed: {e}")
        raise RuntimeError("Tax categorization failed. Please try again.")

    return result


@app.get("/tax-suggestions", response_model=TaxSuggestionResponse)
async def tax_suggestions():
    try:
        calculator = TaxCalculator()
        suggestions = calculator.get_tax_deduction_suggestions()
    except Exception as e:
        print(f"[Error] Suggestion generation failed: {e}")
        raise HTTPException(status_code=500, detail="Tax suggestion generation failed.")

    return suggestions
