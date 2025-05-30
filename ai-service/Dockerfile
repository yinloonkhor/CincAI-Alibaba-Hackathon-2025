FROM python:3.10-slim

WORKDIR /app

# Copy code
COPY ./ /app
COPY requirements.txt .

# Install dependencies
RUN pip install --upgrade pip
RUN pip install --no-cache-dir -r requirements.txt

# Ensure ChromaDB persistence directory exists
RUN mkdir -p /app/chroma_store

# Expose FastAPI port
EXPOSE 8000

# Run the FastAPI app
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
