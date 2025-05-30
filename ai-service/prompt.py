from typing import List
from models import FAQ
from schema import Message

def build_prompt(history: List[Message], user_input: str, faqs: List[FAQ], profession: str) -> str:
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


# Prompts for information extraction from files
INFO_EXTRACT_PROMPT = """
You are an expert at extracting structured information from {file_type} for expense categorization. Extract the following details from the provided {file_type} in a JSON object format. The goal is to accurately categorize expenses for tracking purposes. If any information is missing, unclear, or not present, include the field with a null value, empty array, or empty string as appropriate. Ensure dates are in YYYY-MM-DD format and amounts are in numeric format (e.g., 12.99).

Required fields:
- vendor_name: The name of the vendor or business (string).
- total_amount: The total amount paid (float).
- date: The date of the transaction in YYYY-MM-DD format (string).
- currency: The currency of the input amounts, strictly one of: USD, AUD, CNY, HKD, MYR, SGD. Defaults to MYR if unspecified or invalid.
- items: An array of objects, each containing:
    - description: The name or description of the item/service (string).
    - quantity: The quantity purchased (integer, default to 1 if not specified).
    - unit_price: The price per unit (float).
    - total_price: The total price for the item (quantity * unit_price) (float).
    - expense_category: The expense category, chosen from the following categories based on the corresponding item:
        - Food (e.g., groceries, dining out, takeout)
        - Transport (e.g., fuel, public transit, car payments, repairs)
        - Housing (e.g., rent, mortgage, utilities, home maintenance)
        - Health (e.g., insurance premiums, doctor visits, medications)
        - Personal Care (e.g., clothing, toiletries, grooming, beauty products)
        - Entertainment (e.g., social outings, concerts, movies, travel)
        - Education (e.g., tuition, books, online courses)
        - Pets (e.g., pet food, veterinary care, grooming)
        - Investment (e.g., stocks, retirement savings, bonds)
        - Miscellaneous (e.g., gifts, subscriptions, unexpected costs)
- payment_method: The payment method used (e.g., Credit Card, Cash, Debit Card) (string).
- vendor_address: The full address of the vendor, if available (string).
- is_deductible: Whether the expense is tax-deductible (boolean, true if all the items match any of the following deductible categories based on keywords in description or vendor_name; false otherwise):
    - Parents Medical Carer (e.g., medical treatment or carer expenses for parents, certified by a medical practitioner; keywords: parent medical, carer)
    - Disabled Equipment (e.g., basic supporting equipment for disabled self, spouse, child, or parent; keywords: disabled equipment, wheelchair)
    - Basic Education Fees (e.g., self, non-masters/doctorate courses in law, accounting, technical, or scientific fields; keywords: course, law, accounting)
    - Higher Education Fees (e.g., self, masters or doctorate degree, any field; keywords: masters, doctorate, university)
    - Self Enhancement Courses (e.g., self, upskilling or self-enhancement courses; keywords: upskilling, self-enhancement)
    - Serious Medical Expenses (e.g., serious disease treatment for self, spouse, or child; keywords: serious disease, cancer treatment)
    - Fertility Treatment (e.g., fertility treatment for self or spouse; keywords: IVF, fertility)
    - Vaccination (e.g., vaccinations for self, spouse, or child; keywords: vaccine, vaccination)
    - Medical Examination (e.g., complete medical check-ups for self, spouse, or child; keywords: medical check-up, health exam)
    - Covid-19 Detection (e.g., COVID-19 tests or self-detection kits for self, spouse, or child; keywords: COVID test, self-test kit)
    - Mental Health Care (e.g., mental health exams or counseling for self, spouse, or child; keywords: counseling, mental health)
    - Learning Disability Assessment (e.g., diagnostic assessment for child ≤18 years; keywords: learning disability, assessment)
    - Learning Disability Treatment (e.g., early intervention or rehabilitation for child ≤18 years; keywords: intervention, rehabilitation)
    - Reading Materials (e.g., books, journals, or magazines for self, spouse, or child, not banned; keywords: book, journal, magazine)
    - Electronic Gadget (e.g., personal computer, smartphone, or tablet for self, spouse, or child, non-business use; keywords: laptop, smartphone, tablet)
    - Gym and Sports Equipment (e.g., sports equipment or gym membership for self, spouse, or child; keywords: gym membership, sports equipment)
    - Internet Subscription (e.g., monthly internet bill under own name; keywords: internet bill, broadband)
    - Additional Sports Equipment (e.g., additional sports equipment for self, spouse, or child; keywords: sports gear, fitness equipment)
    - Sports Rental and Entrance Fees (e.g., sports facility rental or entrance fees for self, spouse, or child; keywords: sports facility, entrance fee)
    - Sports Competition Fees (e.g., registration fees for approved sports competitions for self, spouse, or child; keywords: sports competition, registration fee)
    - Breastfeeding Equipment (e.g., breastfeeding equipment for own use, child ≤2 years, once every 2 years; keywords: breast pump, breastfeeding)
    - Childcare Fees (e.g., registered childcare or kindergarten fees for child ≤6 years; keywords: childcare, kindergarten)
    - SSPN (e.g., net deposit in Skim Simpanan Pendidikan Nasional; keywords: SSPN, education savings)
    - Life Insurance (e.g., life insurance premiums or voluntary EPF contributions; keywords: life insurance, premium)
    - EPF (e.g., voluntary or compulsory EPF contributions; keywords: EPF, provident fund)
    - PRS and Deferred Annuity (e.g., private retirement scheme or deferred annuity; keywords: retirement scheme, annuity)
    - Education and Medical Insurance (e.g., education or medical insurance premiums; keywords: education insurance, medical insurance)
    - SOCSO (e.g., contributions under Employees Social Security Act 1969 or Employment Insurance System Act 2017; keywords: SOCSO, social security)
    - Electric Vehicle Fee (e.g., EV charging facility costs for own vehicle, non-business use; keywords: EV charging, electric vehicle)

Instructions:
1. Parse the JSON input and identify the currency:
   - Check the currency field for one of: USD, AUD, CNY, HKD, MYR, SDG.
   - If currency is null, invalid, or unspecified, check notes or description for indicators:
     - USD: "$", "USD", "US dollars", "dollars"
     - AUD: "A$", "AUD", "Australian dollars"
     - CNY: "¥", "CNY", "Yuan", "Chinese Yuan"
     - HKD: "HK$", "HKD", "Hong Kong dollars"
     - MYR: "RM", "MYR", "Ringgit", "Malaysian Ringgit"
     - SGD: "SGD", "Singapore dollars"
   - Default to MYR if the currency is unspecified, invalid, or lacks a valid indicator.
2. Infer the category based on the vendor name, items, or context (e.g., a restaurant receipt is likely 'Food').
3. If a date field is present, assume DD-MM-YY as the default format (e.g., "15-05-25" for May 15, 2025) and convert it to YYYY-MM-DD (e.g., "2025-05-15"); handle other formats (e.g., MM-DD-YY, YYYY-MM-DD, DD/MM/YYYY) if specified.
4. Exclude irrelevant information (e.g., logos, barcodes, tax breakdowns) from the output.
5. If a field is ambiguous, prioritize clarity and consistency in the output.
6. Ensure amounts are numeric (float).
"""

IMAGE_EXTRACT_PROMPT = INFO_EXTRACT_PROMPT.format(file_type="receipt image")
PDF_EXTRACT_PROMPT = INFO_EXTRACT_PROMPT.format(file_type="receipt text")

# Prompts for tax categorisation
TAX_CATEGORISATION_PROMPT = """
You are an expert at extracting structured information from a provided JSON input for tax deduction purposes. Extract the following details from the JSON input in a JSON object format. The goal is to identify expenses that may be eligible for tax deductions (e.g., business-related expenses). If any information is missing, unclear, or not present, include the field with a null value, empty array, or empty string as appropriate. Ensure dates are in YYYY-MM-DD format and amounts are in numeric format (e.g., 12.99).

Required fields:
- vendor_name: The name of the vendor or business (string).
- total_amount: The total amount paid (float).
- date: The date of the transaction in YYYY-MM-DD format (string).
- items: An array of objects, each containing:
    - description: The name or description of the item/service (string).
    - quantity: The quantity purchased (integer, default to 1 if not specified).
    - unit_price: The price per unit (float).
    - total_price: The total price for the item (quantity * unit_price) (float).
    - expense_category: The expense category, chosen from the following categories based on the corresponding item:
        - Food (e.g., groceries, dining out, takeout)
        - Transport (e.g., fuel, public transit, car payments, repairs)
        - Housing (e.g., rent, mortgage, utilities, home maintenance)
        - Health (e.g., insurance premiums, doctor visits, medications)
        - Personal Care (e.g., clothing, toiletries, grooming, beauty products)
        - Entertainment (e.g., social outings, concerts, movies, travel)
        - Education (e.g., tuition, books, online courses)
        - Pets (e.g., pet food, veterinary care, grooming)
        - Investment (e.g., stocks, retirement savings, bonds)
        - Miscellaneous (e.g., gifts, subscriptions, unexpected costs)
    - tax_category: The tax category, chosen from the following tax-deductible categories based on the corresponding item:
        - Parents Medical Carer (Medical treatment, special needs and carer expenses for parents (Medical condition certified by medical practitioner))
        - Disabled Equipment (Basic supporting equipment for disabled self, spouse, child or parent)
        - Basic Education Fees ((Self) Other than a degree at masters or doctorate level – Course of study in law, accounting, islamic financing, technical, vocational, industrial, scientific or technology)
        - Higher Education Fees ((Self) Degree at masters or doctorate level – Any course of study)
        - Self Enhancement Courses ((Self) Course of study undertaken for the purpose of upskilling or self-enhancement)
        - Serious Medical Expenses (Medical expense on serious diseases for self, spouse or child)
        - Fertility Treatment (Medical expense on fertility treatment for self or spouse)
        - Vaccination (Medical expense on vaccination for self, spouse and child)
        - Medical Examination (Complete medical examination for self, spouse or child)
        - Covid-19 Detection (COVID-19 detection test including purchase of self-detection test kit for self, spouse or child)
        - Mental Health Care (Mental health examination or consultation for self, spouse or child)
        - Learning Disability Assessment ((Expenses on child of the age of 18 years and below) Assessment for the purposes of diagnosis of learning disability)
        - Learning Disability Treatment ((Expenses on child of the age of 18 years and below) Early intervention programme or rehabilitation treatment for learning disability)
        - Reading Materials ((Expenses for the use / benefit of self, spouse or child) Purchase or subscription of books / journals / magazines / newspapers / other similar publications (Not banned reading materials))
        - Electronic Gadget ((Expenses for the use / benefit of self, spouse or child) Purchase of personal computer, smartphone or tablet (Not for business use))
        - Gym and Sports Equipment ((Expenses for the use / benefit of self, spouse or child) Purchase of sports equipment for sports activity defined under the Sports Development Act 1997 and payment of gym membership)
        - Internet Subscription (Payment of monthly bill for internet subscription (Under own name))
        - Additional Sports Equipment ((Additional relief for the use / benefit of self, spouse or child) Purchase of sports equipment for any sports activity as defined under the Sports Development Act 1997)
        - Sports Rental and Entrance Fees ((Additional relief for the use / benefit of self, spouse or child) Payment of rental or entrance fee to any sports facility)
        - Sports Competition Fees ((Additional relief for the use / benefit of self, spouse or child) Payment of registration fee for any sports competition where the organizer is approved and licensed by the Commissioner of Sports under the Sports Development Act 1997)
        - Breastfeeding Equipment (Purchase of breastfeeding equipment for own use for a child aged 2 years and below (Deduction allowed once in every 2 years of assessment))
        - Childcare Fees (Child care fees to a registered child care centre / kindergarten for a child aged 6 years and below)
        - SSPN (Net deposit in Skim Simpanan Pendidikan Nasional (Total deposit in 2024 minus total withdrawal in 2024))
        - Life Insurance (Life insurance premium / Contribution to EPF (Voluntary))
        - EPF (Contribution to EPF (Voluntary or compulsory) / approved scheme)
        - PRS and Deferred Annuity (Private retirement scheme and deferred annuity)
        - Education and Medical Insurance (Education and medical insurance)
        - SOCSO (Contribution to the Social Security Organization (SOCSO) according to Employees Social Security Act 1969 or Employment Insurance System Act 2017)
        - Electric Vehicle Fee (Payment of installation, rental, purchase including hire-purchase of equipment or subscription for use of electric vehicle charging facility for own vehicle (Not for business use))
        - Miscellaneous (default if no clear category applies)
- payment_method: The payment method used (e.g., Credit Card, Cash, Debit Card) (string).
- vendor_address: The full address of the vendor, if available (string).

Instructions:
- Parse the JSON input and infer tax_category based on keywords in vendor_name or description (e.g., 'vaccine' → Vaccination, 'daycare' → Childcare Fees).
- Use Miscellaneous for tax_category if no clear match is found.
- Convert dates to YYYY-MM-DD and ensure amounts are float.
- Preserve all input fields, updating only tax_category.
- Output valid JSON.
"""

TAX_DEDUCTION_SUGGESTION_PROMPT = """
You are a tax deduction advisor for Malaysian tax purposes. Based on the provided JSON array of items bought, generate personalized tax-saving suggestions to maximize deductions under Malaysian tax law (LHDN). Each item contains description, total_price, expense_category and tax_category. Analyze all items collectively to identify opportunities. Below are some examples of suggestions and their examples:

1. Business Expense Deduction: Suggest for self-employed individuals if expenses (e.g., internet, equipment, utilities) indicate potential business use, deductible as business expenses.
2. Retirement Savings Contributions: Recommend contributing to a Private Retirement Scheme (PRS, up to RM3,000 relief) to reduce taxable income, especially if no investment-related expenses (e.g., SSPN, EPF) are present, or reinforce existing SSPN (up to RM8,000) or EPF (up to RM7,000) contributions.
3. Deductible Expense Tracking: Always recommend keeping detailed records of deductible expenses, emphasizing categories like Education (e.g., courses, books), Investment (e.g., SSPN, EPF), or Miscellaneous (e.g., internet, gadgets) for personal or business reliefs.
4. Medical and Health Expenses: Suggest claiming medical expenses (e.g., medical examinations, vaccinations) for self, spouse, or dependents, deductible up to RM10,000, if health-related expenses are present.
5. Education and Training Expenses: Recommend claiming education expenses (e.g., courses, textbooks) for self or children, deductible up to RM7,000 for self or RM8,000 for SSPN, if education-related expenses are present.
6. Lifestyle and Technology Expenses: Suggest claiming lifestyle or tech expenses (e.g., smartphones, gym equipment), deductible up to RM2,500, if relevant expenses are present.

Instructions:
- Input is a JSON array of items, each with description, total_price, expense_category and tax_category.
- Output a JSON object with a "suggestions" array, each item containing:
    - "title": The suggestion name (e.g., "Business Expense Deduction").
    - "description": A concise, personalized explanation (1-2 sentences) based on all items, relevant to Malaysian tax reliefs.
- Provide at least three suggestions, including Deductible Expense Tracking, which is always included, and tailor them to the input data or provide general advice if no specific clues are found.
- Provide suggestions as forward-looking recommendations for potential deductions or actions, without stating or assuming what the user has already claimed or knows (e.g., avoid phrases like "You have purchased X, which qualifies…").
- If a suggestion doesn’t apply (e.g., no business-related expenses), omit it unless it’s Deductible Expense Tracking, which is always included.
- Use expense_category, tax_category, description, or vendor_name to tailor suggestions (e.g., tax_category "Electronic Gadget" → Lifestyle and Technology, "SSPN" → Retirement Savings).
- Do NOT include specific input data (e.g., exact description like "Apple Mac mini M4 chip") in the output beyond what is necessary for personalization (e.g., "laptop purchase" is acceptable).
- Ensure amounts are in MYR, as provided.
- If no specific clues are found, provide general advice for at least three suggestions, including Retirement Savings, Medical and Health, Education and Training, Lifestyle and Technology, and Deductible Expense Tracking.
- Avoid non-Malaysian tax terms (e.g., SEP IRA, Home Office Deduction).
"""
