<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class DefaultExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Parents Medical Carer',
                'description' => 'Medical expenses for parents',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Disabled Equipment',
                'description' => 'Equipment for disabled persons',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Basic Education Fees',
                'description' => 'Primary and secondary education',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Higher Education Fees',
                'description' => 'University and college education',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Self Enhancement Courses',
                'description' => 'Skills development courses',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Serious Medical Expenses',
                'description' => 'Major medical treatments',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Fertility Treatment',
                'description' => 'IVF and related procedures',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Vaccination',
                'description' => 'Immunization expenses',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Medical Examination',
                'description' => 'Health check-ups',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Covid-19 Detection',
                'description' => 'Testing and related expenses',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Mental Health Care',
                'description' => 'Psychiatric and counseling services',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Learning Disability Assessment',
                'description' => 'Diagnostic services',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Learning Disability Treatment',
                'description' => 'Therapy and support',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Reading Materials',
                'description' => 'Books and educational materials',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Electronic Gadget',
                'description' => 'Computers and tablets',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Gym and Sports Equipment',
                'description' => 'Fitness gear',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Internet Subscription',
                'description' => 'Broadband and mobile data',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Additional Sports Equipment',
                'description' => 'Extra sports gear',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Sports Rental and Entrance Fees',
                'description' => 'Facility access',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Sports Competition Fees',
                'description' => 'Tournament participation',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Breastfeeding Equipment',
                'description' => 'Pumps and accessories',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Childcare Fees',
                'description' => 'Daycare and babysitting',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'SSPN',
                'description' => 'National Education Savings Scheme',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Life Insurance',
                'description' => 'Insurance premiums',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'EPF',
                'description' => 'Employee Provident Fund',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'PRS and Deferred Annuity',
                'description' => 'Retirement savings',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Education and Medical Insurance',
                'description' => 'Insurance coverage',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'SOCSO',
                'description' => 'Social security contributions',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Electric Vehicle Fee',
                'description' => 'EV-related expenses',
                'is_default' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Default if no clear category applies',
                'is_default' => true,
                'is_system' => true,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category['name'], 'user_id' => null],
                [
                    'description' => $category['description'],
                    'is_default' => $category['is_default'],
                    'is_system' => $category['is_system'],
                ]
            );
        }
    }
}
