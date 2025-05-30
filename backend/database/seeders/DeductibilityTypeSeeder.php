<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeductibilityType;

class DeductibilityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Fully Deductible',
                'description' => 'Expense can be fully deducted for tax purposes',
            ],
            [
                'name' => 'Partially Deductible',
                'description' => 'Expense can be partially deducted for tax purposes',
            ],
            [
                'name' => 'Non-Deductible',
                'description' => 'Expense cannot be deducted for tax purposes',
            ],
        ];

        foreach ($types as $type) {
            DeductibilityType::firstOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }
    }
}
