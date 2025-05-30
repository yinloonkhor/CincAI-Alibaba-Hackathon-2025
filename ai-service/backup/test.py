# test_vectorstore_chroma.py

from vectorstore import embed_text, get_top_faqs, init_faq_index_from_db
from models import FAQQuestion

# Sample FAQ data
sample_faqs = [
    FAQQuestion(id=1, question="How do I file taxes as a freelancer?", answer="You can use the LHDN e-Filing system.", tags="", created_at=None),
    FAQQuestion(id=2, question="What expenses are deductible?", answer="Internet, phone, and travel expenses are deductible.", tags="", created_at=None),
    FAQQuestion(id=3, question="Can I deduct rent?", answer="Only if it's used for a home office.", tags="", created_at=None),
]

# Initialize ChromaDB with FAQ entries
init_faq_index_from_db(sample_faqs)

# Sample user query
query = "Can I claim my internet and rent for tax deduction?"

# Retrieve top matches
results = get_top_faqs(query, top_k=2)

# Display output
print("\n🔍 Top Matching FAQs:\n")
for i, faq in enumerate(results, 1):
    print(f"{i}. {faq.question} → {faq.answer}")
