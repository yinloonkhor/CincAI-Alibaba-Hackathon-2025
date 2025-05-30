from typing import List, Optional

from pydantic import BaseModel

class ChatRequest(BaseModel):
    user_id: int
    message: str

class ChatResponse(BaseModel):
    answer: str

class Message(BaseModel):
    role: str
    text: str

class Item(BaseModel):
    description: str
    quantity: int
    unit_price: float
    total_price: float
    expense_category: str
    payment_method: Optional[str] = None
    tax_category: Optional[str] = None

class AnalyzeResponse(BaseModel):
    vendor_name: str
    total_amount: float
    date: str
    currency: str
    items: List[Item]
    payment_method: Optional[str] = None
    vendor_address: Optional[str] = ""
    is_deductible: Optional[bool] = False

class TaxSuggestion(BaseModel):
    title: str
    description: str

class TaxSuggestionResponse(BaseModel):
    suggestions: List[TaxSuggestion]
