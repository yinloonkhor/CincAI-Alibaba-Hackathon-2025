from sentence_transformers import SentenceTransformer
from models import FAQ
import numpy as np
from typing import List
import chromadb

# Embedder
embedder = SentenceTransformer('all-MiniLM-L6-v2')

chroma_client = chromadb.PersistentClient(path="./chroma_store") # change to HttpClient for production deployment

# Collection name
COLLECTION_NAME = "faq_collection"
faq_collection = chroma_client.get_or_create_collection(COLLECTION_NAME)

def embed_text(text: str) -> np.ndarray:
    return embedder.encode(text, convert_to_numpy=True)

def embed_batch(texts: List[str]) -> List[np.ndarray]:
    return embedder.encode(texts, convert_to_numpy=True).tolist()

def init_faq_index_from_db(faqs: List[FAQ]):
    """Initialize index with verified FAQ data."""
    existing_ids = set(faq_collection.get()['ids'])
    new_faqs = [faq for faq in faqs if str(faq.id) not in existing_ids]

    if not new_faqs:
        print("[ChromaDB] Index already initialized or up to date.")
        return

    documents = [f"{faq.question} → {faq.answer}" for faq in new_faqs]
    embeddings = embed_batch([faq.question for faq in new_faqs])
    ids = [str(faq.id) for faq in new_faqs]
    metadatas = [{"faq_id": faq.id, "question": faq.question, "answer": faq.answer} for faq in new_faqs]

    faq_collection.add(
        documents=documents,
        embeddings=embeddings,
        metadatas=metadatas,
        ids=ids
    )
    chroma_client.persist()
    print(f"[ChromaDB] Indexed {len(new_faqs)} new FAQ entries.")

def get_top_faqs(query: str, top_k=3) -> List[FAQ]:
    query_vector = embed_text(query).tolist()
    results = faq_collection.query(
        query_embeddings=[query_vector],
        n_results=top_k
    )

    if not results.get("metadatas") or not results["metadatas"][0]:
        return []

    metadatas = results["metadatas"][0]
    return [
        FAQ(
            id=meta["faq_id"],
            question=meta["question"],
            answer=meta["answer"],
            category="",
            created_at=None
        )
        for meta in metadatas
    ]
