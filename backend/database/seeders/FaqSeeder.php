<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        $faqs = [
            [
                'question' => 'Do I need to register Tax Identification Number (TIN)?',
                'answer' => 'Yes, you need to register a TIN if you are :
Individual with single status who receive employment income in more than RM34,001 per year (after EPF deduction)
Married individuals and unemployed spouses who receive employment income in excess of RM46,001 per year
Individuals who run a business (even if the business suffers a loss)
New employees subject to Monthly Tax Deduction (STD)
Individuals with taxable income
Individuals who sell/buy real estate
Notes :
TIN registration for individual citizens and permanent residents aged 18 and above is automatic through data obtained from the National Registration Department.
Individuals other than the above categories must apply for TIN registration through the e-Daftar application at MyTax Portal, https://mytax.hasil.gov.my.
Are You That Individual? If YES, please refer to the three (3) steps below:',
                'category' => 'Registration',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How do I register Tax Identification Number (TIN)?',
                'answer' => 'TIN registration can be done online via e-Daftar at MyTax Portal :
Notes :
TIN registration for individual citizens and permanent residents aged 18 and above is automatic through data obtained from the National Registration Department.
Individuals other than the above categories must apply for TIN registration through the e-Daftar application at MyTax Portal, https://mytax.hasil.gov.my.',
                'category' => 'Registration',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What are the supporting documents required during Tax Identification Number (TIN)registration?',
                'answer' => 'Supporting documents required are : Online (e-Daftar) :
Copy of passport / related document such as a copy of the social visit pass, UNCHR card and others for non-citizen or non-resident
Notes :
TIN registration for individual citizens and permanent residents aged 18 and above is automatic through data obtained from the National Registration Department.
Individuals other than the above categories must apply for TIN registration through the e-Daftar application at MyTax Portal, https://mytax.hasil.gov.my.',
                'category' => 'Registration',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I have two income tax files. How?',
                'answer' => 'Kindly refer to the HASiL office that handles your income tax file to update the status.',
                'category' => 'Registration',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I have never received any taxation notification from IRBM? Why?',
                'answer' => 'Personal information in IRBM\'s system (record) such as mailing address may have changed and yet to be updated. Kindly refer to the nearest IRBM office nearby.',
                'category' => 'Registration',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'My personal information is not updated. How?',
                'answer' => 'You are advised to inform IRBM when there are changes to any of your personal information. Updating personal information can be done by :
Online via e-Kemaskini : https://ez.hasil.gov.my/ci ,
Customer Feedback Form : https://maklumbalaspelanggan.hasil.gov.my/, or 
Walk in to IRBM office that handles your income tax file.
Among the information that shall be updated are (if any):
Correspondence address/residential address
Business address (if applicable)
Personal email address
Contact number
Passport number (for non -Malaysian citizens)
Bank account name and number (if applicable)
Marital status
Tax agent information (if applicable)
Kindly fill up CP600B - Change of Address Application Form and a copy of the supporting document to update mailing address, personal email address and telephone number.
However, to update other personal information, kindly submit a notification letter along with relevant supporting documents.',
                'category' => 'Update Information',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I have registered my income tax number. What\'s next?',
                'answer' => 'You are responsible to:
Submit Income Tax Return Form (ITRF) via e-Filing or manually;
Report income and expenses, including deductions and rebates;
Calculate/Compute income tax; and
Keep records and relevant documents for seven (7) years for auditing purposes.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Do I need to submit supporting documentation along with ITRF?',
                'answer' => 'No, but it shall be kept for seven (7) years, starting from the year in which the ITRF has been submitted.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What are the examples of supporting documentations?',
                'answer' => 'EA/EC Form
Dividen vouchers
Insurance premium receipt
Book purchase receipt
Medical receipts
Donation receipt
Receipt of payment of zakat
Child\'s birth certificate
Marriage certificate
Other supporting documents
Worksheet (if any)',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What happen if I do not keep proper records and documentation?',
                'answer' => 'Under section 119A, ITA 1967, he may be prosecuted and if convicted, shall be fined not less than RM300 and not more than RM10,000 or imprisonment for a term not exceeding 1 year or Both.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What forms do I need to fill out?',
                'answer' => 'If you are a :
Individuals with no business income, please complete :
Form BE : Resident Individual Who Does Not Carry On Business
Individuals with business income, please complete :
Form B - Resident Individual with Business, Employment and Other Income
Form M : Non-Resident Individuals With Business, Employment and Other Income
Form BT - Resident Individual With Business, Employment and Other Income (Knowledge Workers / Expert Workers)
Form MT - Non-Resident Individuals With Business, Employment and Other Income (Knowledge Workers)',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'When is the deadline for ITRF submission?',
                'answer' => 'Individuals with no business: before or on April 30th
Individuals with business: before or on June 30th',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What is the late submission penalty?',
                'answer' => 'Failure to submit ITRF within the stipulated period is an offense and is subject to a penalty / tax increase under the Income Tax Act 1967.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I\'m leaving for work abroad. What should I do?',
                'answer' => 'Employer :
Your employer is responsible to inform IRBM within thirty (30) days before the cessation date by completing and submitting Form CP21 (Notification by the Employer for Employees Who Want to Leave Malaysia) to the LHDNM office that handles your income tax file. The employer has to withhold any payment until tax clearance letter is received.
Employee :
A written letter with relevant supporting documents on income derived in Malaysia will no longer be received due to working abroad must be submitted to the LHDNM office that handles your income tax file.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is income derived from outside Malaysia taxable?',
                'answer' => 'In general, income from employment should be taxable in the country where the services are performed regardless of the place where the contract is signed or where the remuneration is paid.
However, from the Year of Assessment 2004, income received in Malaysia from outside Malaysia is tax exempt. Therefore, any income received by resident or non-resident taxpayers in Malaysia are taxable (Paragraph 28 (1), Schedule 6 of the Income Tax Act 1967).
If you have worked abroad and the work carried out is related to the employment carried out in Malaysia, thus the employment income received will be taxable in Malaysia. Kindly log on to IRBM Official Portal, https://www.hasil.gov.my >> Legislation >> Public Ruling >> No.1/2011 – Taxation of Malaysian Employees Seconded Overseas for further information.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I quit my job in 2015 and re-employed in mid-2016 where, my annual income was less than RM 34,000. Do I need to submit the ITRF for the year of assessment 2016?',
                'answer' => 'Pursuant to the provisions of section 77 of the Income Tax Act 1967 each person (other than companies, trust bodies and cooperatives) is required to submit the ITRF to the Director General:-
Individuals carrying on a Business, Employment and Other Income before or on 30 June
Individuals with Employment Income and Other Income (not carrying on business) before or on April 30th
in the year following a year of assessment, provided:
Have taxable income for the year of assessment; or
Does not have taxable income for the year of assessment but has taxable income or has submitted ITRF or has been required to submit ITRF for the year of assessment prior to the year of assessment.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How would I know if ITRF submission via e-Filing is thoroughly successful?',
                'answer' => 'A confirmation notification will appear once the ITRF is successfully submitted for that particular year of assessment.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Do I have to fill up and submit a new ITRF to IRBM Information Processing Centre if the income declared was inaccurate?',
                'answer' => 'ITRF is only to be submitted once. To amend, kindly send an appeal letter (stating the mistakes made), supporting documents and manual re-calculation to IRBM that handles your tax file. However, if the amendment includes under declared of income or over declared of reliefs which may results in additional tax, you need to fill in the BE/B Amended Return Form (ARF). The completed ARF must be submitted to the IRBM office that handles your income tax file.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'My employer has made a Monthly Tax Deduction (MTD). Do I still need to submit ITRF?',
                'answer' => 'Yes, you need to submit your annual income via e-Filing or manually if you are not subject to the MTD as the Final Tax.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How to fill in the ITRF manually?',
                'answer' => 'Kindly refer to the IRBM official portal https://www.hasil.gov.my > Download > Form/Info > Individual > select Year of Assessment> Guide Notes/Explanatory Notes .',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'My tax file is in LHDNM Pahang and I am currently working in Shah Alam. Can I transfer my file to LHDNM Selangor?',
                'answer' => 'Yes, kindly contact IRBM Pahang to transfer the tax file to LHDNM Selangor and notify (latest HASiL office) of any future changes.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is medical expenses incurred for self is allowed?',
                'answer' => 'You can claim medical expenses for serious illnesses incurred on self, spouse or child up to a maximum of RM6, 000.00 per annum and medical expenses for parents up to RM5, 000.00 per annum. Your claim must be supported with an original receipt issued by a medical practitioner.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is the number of children eligible for relief limited /restricted under the Income Tax Act?',
                'answer' => 'There is no limit on the number of children, however relief is only given for unmarried child who is :
Under the age of 18 years;
18 years and above and studying full-time instruction or still under training (articleship) or bond in an enterprise or profession; or
Physically or mentally disabled and registered with the Social Welfare Department (a maximum relief of RM 6,000.00).',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I will retire from civil service, how do I get my income tax clearance letter?',
                'answer' => 'Kindly contact the pejabat HASiL where your income tax file is registered and bring along Form CP22B (Notification of cessation of employment or cessation by reason of death for an employee in public sector) which has been completed by your employer. Please ensure the CP22B is submitted six (6) months before retirement period.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is my donation to mosque fund allowed for a tax deduction?',
                'answer' => 'Deductions for donations made to a body or fund approved by the Director General is allowed. The approval (notification through the gazette) is normally stated on the donation receipt.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Where can I check on the donation made is to a institution or organisation that has been approved by the Government of Malaysia?',
                'answer' => 'Log on to IRBM\'s official portal> Internal Links> List of Institutions or Organizations Approved Under Subsection 44 (6) of the ITA 1967. The donation receipts must display the phrase "This donation is under Subsection 44 (6) of the ITA 1967".',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is payment to the Zakat Collection centre part of the tax calculation?',
                'answer' => 'Yes because an individual resident in Malaysia who has taxable income is eligible to claim a tax rebate on the payment of zakat, fitrah or others required by Islam to the religious authority established under any written law. The amount of rebate allowed to an individual is limited to the amount of income tax charged for a year of assessment.
"Religious authorities established under any written law" include the Zakat Collection Center and the State Islamic Religious Council.
"Written law" means a legal enactment or legal instrument in force in Malaysia.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is the term Zakat inclusive of income and property Zakat?',
                'answer' => 'Zakat is defined on all types of zakat payments made in a calendar year.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How to claim tax relief under Section 132 and 133?',
                'answer' => 'Section 132 - Refers to tax relief in respect of income derived from Malaysia which has been subjected to tax in Malaysia as well as countries outside Malaysia. Refer to the Form BE Guidebook to determine the countries which have Avoidance of Double Taxation Agreement with Malaysia. Calculations can be made in HK-8 of the Form BE Guidebook.  Section 133 - Refers to tax relief in respect of income derived from Malaysia which has been subjected to tax in Malaysia as well as countries outside Malaysia. These countries do not have Avoidance of Double Taxation Agreement with Malaysia and the calculation can be made in HK-9 of the Form BE Guidebook.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What is MTD as Final Tax?',
                'answer' => 'Effective from the Year of Assessment 2014, MTD as the Final Tax allows taxpayers with employment income and have MTD the option of not submitting ITRF by e-Filing or manually as per conditions.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What are the conditions of MTD as Final Tax?',
                'answer' => 'Has only one source of employment income under section 13 of the ITA 1967, including Benefits-In-Kind (BIK ) and Value of Living Accommodation (VOLA);
MTD is deducted in accordance with the Income Tax (Deduction from Remuneration) Rules 1994 or MTD not applicable as income below the deductible level (MTD \'0\');
Serving the same employer;
Taxes are not borne by the employer; and
Not opted for joint/combined assessment with the spouse.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I am an engineer and pay professional fees to the Association of Engineers. Where can I claim this fee?',
                'answer' => 'Kindly deduct from Gross Income (as per EA Form). The net amount must be filled in the Statutory income from employment column (deduction from professional fees).',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I have a rental income as well as rental-related expenses. How do I compute my income and claim these expenses?',
                'answer' => 'You can make the calculation as in HK-4 (Details of Property / Assets and Rent Amount) in the Form BE Guidebook to get the net rent and this amount is transferred to the statutory rental income column. Alternatively, you can deduct those expenses from the gross rental amount. Please ensure that the expense is an allowable expense.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How do I fill up my pension income in BE Form?',
                'answer' => 'Pension income must be reported if:
Retirement before the age of 55
Your pension income received until 55 years of age is taxable. Do fill up the pension income at the pension, annuity and other periodic payments field.
 
If a person receiving more than one (1) pension incomes, only the highest amount of pension will be tax exempted. Kindly refer to Paragraph 16 of Schedule 6 of the Income Tax Act 1967. 
Derivative pensions Derivative pensions received are not taxable. Example: The wife/husband receives a pension (issued) on the death of the spouse. This pension is not taxable.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Initially I only receive employment income, but started doing business later. Which form to fill in?',
                'answer' => 'Kindly fill in and submit B Form for business and employment incomes before or on 30th June.',
                'category' => 'Assessment',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I started contributing to a PRS in October 2012. Am I eligible for tax relief?',
                'answer' => 'Yes; the effective period for tax relief on PRS contributions is 10 years commencing from the Year of Assessment 2012 until the Year of Assessment 2021. A maximum of RM3000 can be claimed for every Year of Assessment and the relief can be made in the PRIVATE RETIREMENT SCHEME AND DEFERRED ANNUITY column.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'My gross salary in November 2012 was RM 4,000. My employer deducted RM450/month from my salary for a private retirement scheme and the employer\'s contribution is RM 760. What is the amount that I am eligible to claim?',
                'answer' => 'You are eligible to claim RM 450 x 2 months = RM 900 only for the Year of Assessment 2012. However, for the Year of Assessment 2013, the annual contribution amount is RM 5400 (RM 450 x12 months) but the claim is restricted to RM 3000 only. The amount of contribution by the employer is not taken into account in determining the amount of tax relief.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is income received by a PRS taxable?',
                'answer' => 'Income received by PRS is tax exempted paragraph 20, Schedule 6 of the ITA 1967.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What if I withdraw my contributions to PRS before reaching the age of 55?',
                'answer' => 'Effective from 01 January 2013, final tax rate of 8% will be imposed on contributors with early withdrawal (before the age of 55). This provision however, does not apply to withdrawals due to death or the contributor leaving Malaysia permanently.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What is the final tax to be paid if a withdrawal of RM30,000 from my PRS before reaching the age of 55 is made?',
                'answer' => 'Contributors will be charged a final tax of RM 2400 (RM 30000 x 8%).',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Who is responsible for the final tax payment of 8% if the withdrawal of PRS contribution is at the age of 45?',
                'answer' => 'The PRS provider must deduct 8% tax and remit it to the DGIR within 1 month after the payment is made.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'If the PRS provider does not make the final tax payment of 8%?',
                'answer' => 'If the PRS provider has made payment to the recipient without deducting the final tax of 8% and is willing to pay the final tax to the DGIR himself, then the PRS provider can recover the final tax amount from you.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Who is the provider of this PRS?',
                'answer' => 'PRS providers refer to providers approved under section 139Q, Capital Markets and Services Act 2007. Please refer to the Securities Commission Malaysia website for more information.',
                'category' => 'Private Retirement Scheme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'If I am taxed, what is the actual amount of tax I have to pay?',
                'answer' => 'To find out the actual amount of tax, a comparison must be made between the amount of Monthly Tax Deduction (MTD) that has been deducted and the tax payable. If the MTD paid is insufficient, then the amount of the difference (ie between the amount of MTD and the tax payable) must be paid to the IRBM.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How do I pay the remaining tax?',
                'answer' => 'There are three (3) payment methods, namely:-
Online through the ByrHasil application : https://byrhasil.hasil.gov.my/
Payment via internet banking. The service uses FPX as a Gateway for users to pay taxes. Users need to have an internet banking account with any bank participating in FPX. 
Though LHDNM collection agents
Payment at the Agent\'s Counter
Payment Via Internet
Payment Via ATM
Payment Via Tele-Banking
Payment Through Cash Deposit Machine',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What information is required when paying taxes?',
                'answer' => 'Maklumat bayaran yang diperlukan adalah :
Income tax number
Name and identification number
Year of assessment
Payment code
Instalment number
Total amount
Payment receipts and bank slips should be kept for record and reference.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'When is the due date for individuals\' tax balance payment?',
                'answer' => 'EMPLOYMENT INCOME: before or on April 30th
BUSINESS INCOME: before or on 30 June',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What is the individual tax payment code?',
                'answer' => 'The individual tax payment code is 084 or 095.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How can I pay the remaining tax if I am abroad?',
                'answer' => 'Payment of income tax through monetary transfer from oversea can be done in a few methods, namely:
Telegraphic Transfer (TT)/Interbank Giro Transfer (IBG)/Electronic Fund Transfer (EFT)',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I received a CP38 Salary Deduction Instruction letter while the employer had deducted the PCB in my monthly salary. What does CP38 mean?',
                'answer' => 'MTD is a tax deduction based on the MTD Schedular on employees\' remuneration and to be remitted to the IRBM monthly, while CP38 deduction can only be done if IRBM has issued specific instructions to employers to make deductions on certain amounts in certain months. The CP38 notification is issued to the employer as supplementary instructions to clear the balance of tax liability of employees over and above the MTD.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How do I make a CP38 Deduction Instruction payment?',
                'answer' => 'The employer is responsible for making the payment of the CP38 Deduction Instruction. A separate payment of MTD and CP38 must be made by employer as provided in Form CP39.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How to amend the CP500 instalment amount?',
                'answer' => 'An appeal on amendment of instalment must be made no later than 30th June by submitting Form CP502 along with the CP500 notice to the LHDNM office where your income tax file is handled.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Will there be a penalty imposed if payment is overdue?',
                'answer' => 'A 10% late payment penalty will be imposed on the amount not paid after April 30th of the following year. An additional of 5% penalty will then be imposed if taxpayer fails to pay the tax and initial penalty (10%) within 60 days from the penalty date imposed.',
                'category' => 'Collection',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'When will I receive my current year\'s tax refund?',
                'answer' => 'If manual form or e-Filing submission is on or before the deadline, the refund will be processed within:
30 working days from the date of submission via e-Filing.
90 working days from the date of manual submission.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Do I need to submit a refund application if I have submitted the Income Tax Return Form (ITRF) before the deadline and am eligible to claim a refund?',
                'answer' => 'No, as the refund will be processed within 30 working days after the submission via e-Filing or 90 working days if submission via manual or hand delivery.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Why have I not received a refund within the period as stated in the IRBM Client\'s Charter?',
                'answer' => 'IRBM is in the midst of auditing the Income Tax Return Form submitted, hence the delay. However, you are advised to contact the LHDNM Office that handles your file or Hasil Care Line for more information.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'When to expect for a refund if ITRF is submitted after the deadline?',
                'answer' => 'Refunds will be processed after a late submission penalty is imposed.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Why have I still not received the refund after more than 3 months of ITRF submission?',
                'answer' => 'Kindly contact the HASiL office where your file is handled or the Customer Care Officer (CCO) of the respective LHDNM or call Hasil Care Line at toll-free 03-8911 1000 for further information on your refund status.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Do I need to submit my dividend voucher to IRBM if I am eligible for a refund (dividend)?',
                'answer' => 'No, unless requested by IRBM.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'I have received a letter on refund approval but have not received the cheque.',
                'answer' => 'The cheque will be processed within 14 working days from the refund approval date. Kindly contact the LHDNM office that handles your file or Hasil Care Line toll-free 03-8911 1000 (LHDN) if you still have not received after the stipulated period.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How to replace an expired cheque?',
                'answer' => 'Kindly contact the HASiL pejabat that handles your income tax file or the Hasil Care Line (toll-free) at 03-8911 1000 (IRBM) for the issuance of a replacement cheque.',
                'category' => 'Refund',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('faqs')->insert($faqs);
    }
}