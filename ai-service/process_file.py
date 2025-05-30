import os
import re
import base64
import json
from openai import OpenAI
from dotenv import load_dotenv
from currency import check_currency_and_convert
from prompt import IMAGE_EXTRACT_PROMPT, PDF_EXTRACT_PROMPT
from langchain_community.document_loaders import PyPDFLoader

load_dotenv()

client = OpenAI(
    api_key=os.getenv("DASHSCOPE_API_KEY"),
    base_url="https://dashscope-intl.aliyuncs.com/compatible-mode/v1"
)

# Image extraction
def encode_image(image_path: str):
    """Convert image to base64 string for OpenAI API."""
    with open(image_path, "rb") as image_file:
        return base64.b64encode(image_file.read()).decode("utf-8")

def extract_from_image(image_path: str):
    base64_image = encode_image(image_path)

    response = client.chat.completions.create(
        model="qwen2.5-vl-3b-instruct",
        messages=[
            {
                "role": "system",
                "content": IMAGE_EXTRACT_PROMPT
            },
            {
                "role": "user",
                "content": [
                    {
                        "type": "image_url",
                        "image_url": {
                            "url": f"data:image/jpeg;base64,{base64_image}"
                        }
                    }
                ]
            },
        ],
        temperature=0,
        response_format={"type": "json_object"}
    )

    # Extract content within curly braces
    json_result = response.choices[0].message.content
    result = json.loads(json_result)
    result = check_currency_and_convert(result)
    return result

# PDF extraction
# https://python.langchain.com/docs/how_to/document_loader_pdf/#vector-search-over-pdfs
def load_pdf(pdf_path: str):
    """Load text from a PDF using PyPDFLoader."""
    loader = PyPDFLoader(pdf_path)
    documents = loader.load()
    # Combine text from all pages
    text = "\n".join(doc.page_content for doc in documents)
    return text

def clean_text(text: str):
    """Clean extracted text to remove noise (e.g., headers, footers)."""
    # Remove common header/footer patterns (customize as needed)
    text = re.sub(r"\n\s*\n", "\n", text)  # Remove excessive newlines
    text = text.strip()
    return text

def extract_from_pdf(pdf_path: str):
    raw_text = load_pdf(pdf_path)
    cleaned_text = clean_text(raw_text)

    response = client.chat.completions.create(
        model="qwen2.5-32b-instruct",
        messages=[
            {
                "role": "system",
                "content": PDF_EXTRACT_PROMPT
            },
            {
                "role": "user",
                "content": [
                    {
                        "type": "text",
                        "text": cleaned_text
                    }
                ]
            },
        ],
        temperature=0,
        response_format={"type": "json_object"}
    )

    # Extract content within curly braces
    json_result = response.choices[0].message.content
    result = json.loads(json_result)
    result = check_currency_and_convert(result)
    return result