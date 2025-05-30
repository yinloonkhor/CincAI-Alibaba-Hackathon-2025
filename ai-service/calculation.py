import os
import json
import pandas as pd
from openai import OpenAI
from dotenv import load_dotenv
from reliefs import FORM_B_RELIEFS
from prompt import TAX_CATEGORISATION_PROMPT, TAX_DEDUCTION_SUGGESTION_PROMPT

load_dotenv()

client = OpenAI(
    api_key=os.getenv("DASHSCOPE_API_KEY"),
    base_url="https://dashscope-intl.aliyuncs.com/compatible-mode/v1"
)


class TaxCalculator:
    def __init__(self):
        self.items = []
        self.expenses = self._get_expenses()

    @staticmethod
    def _get_expenses():
        expenses = FORM_B_RELIEFS.copy()
        for expense_category in expenses:
            expenses[expense_category]['expense_relief_amount'] = 0
            expenses[expense_category]['items'] = []

        return expenses

    @staticmethod
    def update_tax_category(items_info: dict):
        json_input = json.dumps(items_info, indent=2)

        response = client.chat.completions.create(
            model="qwen2.5-32b-instruct",
            messages=[
                {
                    "role": "system",
                    "content": TAX_CATEGORISATION_PROMPT
                },
                {
                    "role": "user",
                    "content": [
                        {
                            "type": "text",
                            "text": json_input
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
        return result

    # Add items and update tax category
    def add_items(self, items_info: dict):
        updated_items_info = self.update_tax_category(items_info)
        for item in updated_items_info['items']:
            if item['tax_category'] in self.expenses:
                expense_info = self.expenses[item['tax_category']]
                expense_info['items'].append(item)
            else:
                print(
                    f"Item {item['description']} is categorised as {item['tax_category']}, which is not in the relief list.")

            self.items.append(item)

    def _calculate_relief_per_item(self):
        item_relief_details = []
        for expense_category, expense_info in self.expenses.items():
            grouped_relief_category = expense_info.get('grouped_relief_category', expense_info['relief_category'])

            for item in expense_info['items']:
                # Case where relief amount is equal to relief limit
                if expense_info['expense_relief_amount'] == expense_info['relief_limit']:
                    item_relief_details.append({
                        'Expense Category': expense_category,
                        'Grouped Relief Category': grouped_relief_category,
                        'Relief Category': expense_info['relief_category'],
                        'Tax Category': item['tax_category'],
                        'Description': item['description'],
                        'Total Price (RM)': item['total_price'],
                        'Total Relief per Item (RM)': 0,
                        'Total Relief per Expense Category (RM)': 0,
                        'Remarks': 'Relief Limit Reached'
                    })
                    continue

                # Cases where relief amount is less than relief limit
                if expense_info['expense_relief_amount'] + item['total_price'] > expense_info['relief_limit']:
                    item_relief_amount = expense_info['relief_limit'] - expense_info['expense_relief_amount']
                    expense_info['expense_relief_amount'] = expense_info['relief_limit']
                else:
                    item_relief_amount = item['total_price']
                    expense_info['expense_relief_amount'] += item_relief_amount

                item_relief_details.append({
                    'Expense Category': expense_category,
                    'Grouped Relief Category': grouped_relief_category,
                    'Relief Category': expense_info['relief_category'],
                    'Tax Category': item['tax_category'],
                    'Description': item['description'],
                    'Total Price (RM)': item['total_price'],
                    'Total Relief per Item (RM)': item_relief_amount,
                    'Total Relief per Expense Category (RM)': expense_info['expense_relief_amount'],
                    'Remarks': '',
                })
                item['item_relief_amount'] = item_relief_amount

        item_relief_details_df = pd.DataFrame(item_relief_details)
        return item_relief_details_df

    # Relief details are categorised into fine categories like H10.1, H10.2, etc.
    # This function will group the relief details into the coarse categories like H10.
    def _calculate_relief_per_expense_category(self):
        expense_relief_details = {'Expense Category': [], 'Relief Category': [],
                                  'Expense Relief Amount (RM)': []}  # {expense_category: expense_relief_amount}
        grouped_expense_relief_details = {}  # {grouped_relief_category: grouped_expense_relief_amount}
        for expense_category, expense_info in self.expenses.items():
            # Ungrouped relief category
            expense_relief_details['Expense Category'].append(expense_category)
            expense_relief_details['Relief Category'].append(expense_info['relief_category'])
            expense_relief_details['Expense Relief Amount (RM)'].append(expense_info['expense_relief_amount'])

            # Relief category is grouped
            if 'grouped_relief_category' in expense_info:
                grouped_relief_category = expense_info['grouped_relief_category']
                grouped_expense_relief_amount = grouped_expense_relief_details.get(grouped_relief_category, None)

                # Newly initialised grouped relief category
                if grouped_expense_relief_amount is None:
                    grouped_expense_relief_details[grouped_relief_category] = expense_info['expense_relief_amount']
                # Grouped relief amount limit is reached
                elif grouped_expense_relief_amount + expense_info['expense_relief_amount'] > expense_info[
                    'grouped_relief_limit']:
                    grouped_expense_relief_details[grouped_relief_category] = expense_info['grouped_relief_limit']
                # Grouped relief amount limit is not reached
                else:
                    grouped_expense_relief_details[grouped_relief_category] += expense_info['expense_relief_amount']

            # Relief category is not grouped
            else:
                grouped_expense_relief_details[expense_info['relief_category']] = expense_info['expense_relief_amount']

        expense_relief_details_df = pd.DataFrame(expense_relief_details)
        grouped_expense_relief_details_df = pd.DataFrame(grouped_expense_relief_details.items(),
                                                         columns=['Grouped Relief Category',
                                                                  'Grouped Relief Amount (RM)'])
        return expense_relief_details_df, grouped_expense_relief_details_df

    def get_relief_details(self):
        item_relief_details = self._calculate_relief_per_item()
        # expense_relief_details_df, grouped_expense_relief_details = self._calculate_relief_per_expense_category()
        return item_relief_details

    def export_relief_details(self):
        relief_details_df = self.get_relief_details()
        relief_details_df.to_excel('Relief Details.xlsx', index=False)
        # print('Relief Details:')
        # print(relief_details_df)

    def get_tax_deduction_suggestions(self):
        json_input = json.dumps(self.items, indent=2)

        response = client.chat.completions.create(
            model="qwen2.5-32b-instruct",
            messages=[
                {
                    "role": "system",
                    "content": TAX_DEDUCTION_SUGGESTION_PROMPT
                },
                {
                    "role": "user",
                    "content": [
                        {
                            "type": "text",
                            "text": json_input
                        }
                    ]
                },
            ],
            temperature=0.7,
            response_format={"type": "json_object"}
        )

        # Extract content within curly braces
        json_result = response.choices[0].message.content
        suggestions = json.loads(json_result)
        return suggestions