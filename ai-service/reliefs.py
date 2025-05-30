# https://www.hasil.gov.my/media/qjgl3j2a/form_b2023_2.pdf
FORM_B_RELIEFS = {
    "Parents Medical Carer": {"relief_limit": 8000, "relief_category": 'H2'},
    "Disabled Equipment": {"relief_limit": 6000, "relief_category": 'H3'},
    # Unknown H4
    "Basic Education Fees": {
        "relief_limit": 7000,
        "relief_category": 'H5.1',
        'grouped_relief_category': 'H5',
        'grouped_relief_limit': 7000
    },
    "Higher Education Fees": {
        "relief_limit": 7000,
        "relief_category": 'H5.2',
        'grouped_relief_category': 'H5',
        'grouped_relief_limit': 7000
    },
    "Self Enhancement Courses": {
        "relief_limit": 2000,
        "relief_category": 'H5.3',
        'grouped_relief_category': 'H5',
        'grouped_relief_limit': 7000
    },
    "Serious Medical Expenses": {
        "relief_limit": 10000,
        "relief_category": 'H6.1',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Fertility Treatment": {
        "relief_limit": 10000,
        "relief_category": 'H6.2',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Vaccination": {
        "relief_limit": 1000,
        "relief_category": 'H6.3',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Medical Examination": {
        "relief_limit": 1000,
        "relief_category": 'H7.1',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Covid-19 Detection": {
        "relief_limit": 1000,
        "relief_category": 'H7.2',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Mental Health Care": {
        "relief_limit": 1000,
        "relief_category": 'H7.3',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Learning Disability Assessment": {
        "relief_limit": 4000,
        "relief_category": 'H8.1',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Learning Disability Treatment": {
        "relief_limit": 4000,
        "relief_category": 'H8.2',
        'grouped_relief_category': 'H6_H7_H8',
        'grouped_relief_limit': 10000
    },
    "Reading Materials": {
        "relief_limit": 2500,
        "relief_category": 'H9.1',
        'grouped_relief_category': 'H9',
        'grouped_relief_limit': 2500
    },
    "Electronic Gadget": {
        "relief_limit": 2500,
        "relief_category": 'H9.2',
        'grouped_relief_category': 'H9',
        'grouped_relief_limit': 2500
    },
    "Gym and Sports Equipment": {
        "relief_limit": 2500,
        "relief_category": 'H9.3',
        'grouped_relief_category': 'H9',
        'grouped_relief_limit': 2500
    },
    "Internet Subscription": {
        "relief_limit": 2500,
        "relief_category": 'H9.4',
        'grouped_relief_category': 'H9',
        'grouped_relief_limit': 2500
    },
    "Additional Sports Equipment": {
        "relief_limit": 500,
        "relief_category": 'H10.1',
        'grouped_relief_category': 'H10',
        'grouped_relief_limit': 500
    },
    "Sports Rental and Entrance Fees": {
        "relief_limit": 500,
        "relief_category": 'H10.2',
        'grouped_relief_category': 'H10',
        'grouped_relief_limit': 500
    },
    "Sports Competition Fees": {
        "relief_limit": 500,
        "relief_category": 'H10.3',
        'grouped_relief_category': 'H10',
        'grouped_relief_limit': 500
    },
    "Breastfeeding Equipment": {"relief_limit": 1000, "relief_category": 'H11'},
    "Childcare Fees": {"relief_limit": 3000, "relief_category": 'H12'},
    "SSPN": {"relief_limit": 8000, "relief_category": 'H13'},
    # Unknown H14 to H16
    "Life Insurance": {
        "relief_limit": 3000,
        "relief_category": 'H17.1',
        'grouped_relief_category': 'H17',
        'grouped_relief_limit': 7000
    },
    "EPF": {
        "relief_limit": 4000,
        "relief_category": 'H17.2',
        'grouped_relief_category': 'H17',
        'grouped_relief_limit': 7000
    },
    "PRS and Deferred Annuity": {"relief_limit": 3000, "relief_category": 'H18'},
    "Education and Medical Insurance": {"relief_limit": 3000, "relief_category": 'H19'},
    "SOCSO": {"relief_limit": 350, "relief_category": 'H20'},
    "Electric Vehicle Fee": {"relief_limit": 2500, "relief_category": 'H21'},
}

FORM_BE_RELIEFS = {
    "Parents Medical Carer": {"relief_limit": 8000, "relief_category": 'G2'},
    "Disabled Equipment": {"relief_limit": 6000, "relief_category": 'G3'},
    # Unknown G4
    "Basic Education Fees": {
        "relief_limit": 7000,
        "relief_category": 'G5.1',
        'grouped_relief_category': 'G5',
        'grouped_relief_limit': 7000
    },
    "Higher Education Fees": {
        "relief_limit": 7000,
        "relief_category": 'G5.2',
        'grouped_relief_category': 'G5',
        'grouped_relief_limit': 7000
    },
    "Self Enhancement Courses": {
        "relief_limit": 2000,
        "relief_category": 'G5.3',
        'grouped_relief_category': 'G5',
        'grouped_relief_limit': 7000
    },
    "Serious Medical Expenses": {
        "relief_limit": 10000,
        "relief_category": 'G6.1',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Fertility Treatment": {
        "relief_limit": 10000,
        "relief_category": 'G6.2',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Vaccination": {
        "relief_limit": 1000,
        "relief_category": 'G6.3',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Medical Examination": {
        "relief_limit": 1000,
        "relief_category": 'G7.1',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Covid-19 Detection": {
        "relief_limit": 1000,
        "relief_category": 'G7.2',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Mental Health Care": {
        "relief_limit": 1000,
        "relief_category": 'G7.3',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Learning Disability Assessment": {
        "relief_limit": 4000,
        "relief_category": 'G8.1',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Learning Disability Treatment": {
        "relief_limit": 4000,
        "relief_category": 'G8.2',
        'grouped_relief_category': 'G6_G7_G8',
        'grouped_relief_limit': 10000
    },
    "Reading Materials": {
        "relief_limit": 2500,
        "relief_category": 'G9.1',
        'grouped_relief_category': 'G9',
        'grouped_relief_limit': 2500
    },
    "Electronic Gadget": {
        "relief_limit": 2500,
        "relief_category": 'G9.2',
        'grouped_relief_category': 'G9',
        'grouped_relief_limit': 2500
    },
    "Gym and Sports Equipment": {
        "relief_limit": 2500,
        "relief_category": 'G9.3',
        'grouped_relief_category': 'G9',
        'grouped_relief_limit': 2500
    },
    "Internet Subscription": {
        "relief_limit": 2500,
        "relief_category": 'G9.4',
        'grouped_relief_category': 'G9',
        'grouped_relief_limit': 2500
    },
    "Additional Sports Equipment": {
        "relief_limit": 500,
        "relief_category": 'G10.1',
        'grouped_relief_category': 'G10',
        'grouped_relief_limit': 500
    },
    "Sports Rental and Entrance Fees": {
        "relief_limit": 500,
        "relief_category": 'G10.2',
        'grouped_relief_category': 'G10',
        'grouped_relief_limit': 500
    },
    "Sports Competition Fees": {
        "relief_limit": 500,
        "relief_category": 'G10.3',
        'grouped_relief_category': 'G10',
        'grouped_relief_limit': 500
    },
    "Breastfeeding Equipment": {"relief_limit": 1000, "relief_category": 'G11'},
    "Childcare Fees": {"relief_limit": 3000, "relief_category": 'G12'},
    "SSPN": {"relief_limit": 8000, "relief_category": 'G13'},
    # Unknown G14 to G16
    "Life Insurance": {
        "relief_limit": 3000,
        "relief_category": 'G17.1',
        'grouped_relief_category': 'G17',
        'grouped_relief_limit': 7000
    },
    "EPF": {
        "relief_limit": 4000,
        "relief_category": 'G17.2',
        'grouped_relief_category': 'G17',
        'grouped_relief_limit': 7000
    },
    "PRS and Deferred Annuity": {"relief_limit": 3000, "relief_category": 'G18'},
    "Education and Medical Insurance": {"relief_limit": 3000, "relief_category": 'G19'},
    "SOCSO": {"relief_limit": 350, "relief_category": 'G20'},
    "Electric Vehicle Fee": {"relief_limit": 2500, "relief_category": 'G21'},
}
