import os
import requests
from dotenv import load_dotenv

load_dotenv()

EXCHANGE_RATE_API_KEY = os.getenv("EXCHANGE_RATE_API_KEY")

# https://www.exchangerate-api.com/docs/python-currency-api
def get_conversion_rate(currency: str):
    url = f'https://v6.exchangerate-api.com/v6/{EXCHANGE_RATE_API_KEY}/latest/MYR'

    # Making our request
    response = requests.get(url)
    data = response.json()
    conversion_rate = data['conversion_rates'][currency]
    return conversion_rate


def convert_currency_to_myr(amount: float, conversion_rate: float):
    return round(amount / conversion_rate, 2)


def check_currency_and_convert(result: dict):
    # Convert currency to MYR if it is not MYR
    if result['currency'] != 'MYR':
        conversion_rate = get_conversion_rate(result['currency'])
        converted_total_amount = 0
        for item in result['items']:
            item['unit_price'] = convert_currency_to_myr(item['unit_price'], conversion_rate)
            item['total_price'] = convert_currency_to_myr(item['total_price'], conversion_rate)
            converted_total_amount += item['total_price']

        result['total_amount'] = converted_total_amount
        result[
            'conversion_message'] = f'Receipt processed in {result["currency"]}, converted to MYR for tax purposes. Please verify the actual amount with your bank transaction.'

    return result
