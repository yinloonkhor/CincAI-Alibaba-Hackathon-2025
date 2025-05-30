import os
from openai import OpenAI
from dotenv import load_dotenv

load_dotenv()

client = OpenAI(
    api_key=os.getenv("DASHSCOPE_API_KEY"),
    base_url="https://dashscope-intl.aliyuncs.com/compatible-mode/v1"
)

def call_qwen(prompt: str) -> str:
    try:
        response = client.chat.completions.create(
            model="qwen-max",
            messages=[
                {
                    "role": "system",
                    "content": (
                        "You are a professional assistant specializing in tax filing for freelancers. "
                        "Use the user's profession, chat history, relevant FAQs, and current input to answer clearly and concisely. "
                        "Provide a detailed, step-by-step guideline, but do not mention the FAQ topic itself. "
                        "Display all monetary values in MYR (Malaysian Ringgit). "
                        "Format your response for chat: use short paragraphs or bullet points when helpful."
                    )
                },
                {
                    "role": "user",
                    "content": prompt
                }
            ],
            temperature=0.3
        )
        return response.choices[0].message.content
    except Exception as e:
        print(f"[Qwen API Error] {str(e)}")
        return "Sorry, I couldn't generate a response right now. Please try again later."
