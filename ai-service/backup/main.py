# from contextlib import asynccontextmanager
# from fastapi import FastAPI, Depends
# from sqlalchemy.orm import Session
# from db import get_db
# from models import UserChatLog, FAQQuestion, UserProfilePreference
# from schema import ChatRequest, ChatResponse, Message
# from prompt import build_prompt
# from qwen import call_qwen
# from vectorstore import get_top_faqs, init_faq_index_from_db
# from typing import AsyncGenerator
# from vectorstore import faq_collection
#
# @asynccontextmanager
# def lifespan(_: FastAPI) -> AsyncGenerator[None, None]:
#     # Only initialize FAQ index if it's empty
#     if len(faq_collection.get()['ids']) == 0:
#         with next(get_db()) as db:
#             faqs = db.query(FAQQuestion).all()
#             init_faq_index_from_db(faqs)
#     else:
#         print("[Startup] FAQ embeddings already exist in ChromaDB.")
#     yield
#
# app = FastAPI(lifespan=lifespan)
#
# @app.post("/chat", response_model=ChatResponse)
# def chat(request: ChatRequest, db: Session = Depends(get_db)):
#     user_id = request.user_id
#     user_input = request.message
#
#     # Load user chat history from DB
#     db_history = db.query(UserChatLog).filter_by(user_id=user_id).order_by(UserChatLog.created_at.desc()).limit(10).all()
#     history = []
#     for log in reversed(db_history):
#         history.append(Message(role="user", text=log.question))
#         history.append(Message(role="assistant", text=log.answer))
#
#     # Retrieve top 3 matching FAQs with fallback
#     try:
#         faqs = get_top_faqs(user_input, top_k=3)
#     except Exception as e:
#         faqs = []
#         print(f"[Error] get_top_faqs failed: {e}")
#
#     # Fetch profession from user_profile_preferences
#     profession_pref = (
#         db.query(UserProfilePreference)
#         .filter_by(user_id=user_id, question="profession")
#         .first()
#     )
#     profession = profession_pref.answer if profession_pref else ""
#     if profession:
#         profession = f"Freelance {profession}"
#
#     # Build context-aware prompt with FAQs
#     prompt = build_prompt(history, user_input, faqs, profession)
#
#     # Get response from Qwen with fallback
#     try:
#         answer = call_qwen(prompt)
#     except Exception as e:
#         print(f"[Error] call_qwen failed: {e}")
#         answer = "Sorry, something went wrong while generating a response."
#
#     # Save to DB (intent placeholder left for future use)
#     log = UserChatLog(user_id=user_id, question=user_input, answer=answer, intent="")
#     db.add(log)
#     db.commit()
#
#     return ChatResponse(answer=answer)


from fastapi import Body
from fastapi import FastAPI
from models import FAQQuestion
from schema import ChatRequest, ChatResponse, Message
from prompt import build_prompt
from qwen import call_qwen
from vectorstore import get_top_faqs

app = FastAPI()

@app.get("/")
def root():
    return {"message": "FastAPI chatbot is running. Visit /docs to try it."}

@app.post("/chat", response_model=ChatResponse)
def chat(request: ChatRequest = Body(...)):
    # user_id = request.user_id
    user_input = request.message

    # 👤 Static test history
    history = [
        Message(role="user", text="What is the tax rate for freelancers?"),
        Message(role="assistant", text="The tax rate varies based on your annual income."),
    ]

    # 📚 Static test FAQs
    sample_faqs = [
        FAQQuestion(id=1, question="How do I file taxes as a freelancer?", answer="You can use e-filing via LHDN.", tags="", created_at=None),
        FAQQuestion(id=2, question="Can I deduct rent?", answer="Only if it's part of your home office.", tags="", created_at=None),
        FAQQuestion(id=3, question="What expenses are tax deductible?", answer="Internet, phone, travel if work-related.", tags="", created_at=None),
    ]

    profession = "web developer"

    # 🔎 Semantic retrieval (mocked using top 3)
    faqs = sample_faqs[:3]

    # 🧠 Build prompt
    prompt = build_prompt(history, user_input, faqs, profession)

    # 🤖 Get answer
    answer = call_qwen(prompt)

    return ChatResponse(answer=answer)
