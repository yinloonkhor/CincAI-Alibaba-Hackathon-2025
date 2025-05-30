from typing import List
from models import FAQQuestion
from schema import Message

def build_prompt(history: List[Message], user_input: str, faqs: List[FAQQuestion], profession: str) -> str:
    prompt_sections = []

    if profession:
        prompt_sections.append(f"### User Profession:\n{profession}")

    if history:
        chat_lines = ["### Chat History:"]
        for message in history:
            chat_lines.append(f"{message.role.capitalize()}: {message.text}")
        prompt_sections.append("\n".join(chat_lines))

    if faqs:
        faq_lines = ["### Relevant FAQs:"]
        for i, faq in enumerate(faqs, 1):
            faq_lines.append(
                f"{i}. {faq.question} → {faq.answer}"
            )
        prompt_sections.append("\n".join(faq_lines))

    prompt_sections.append(f"### Current User Input:\n{user_input}\n\n### Assistant:")

    return "\n\n".join(prompt_sections)
