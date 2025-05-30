from pydantic import BaseModel

class ChatRequest(BaseModel):
    # user_id: int
    message: str

class ChatResponse(BaseModel):
    answer: str

class Message(BaseModel):
    role: str
    text: str