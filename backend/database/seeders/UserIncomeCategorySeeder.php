<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserIncomeCategory;
use Illuminate\Database\Seeder;

class UserIncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        $defaultCategories = [
            'salary' => '#10B981',
            'freelance' => '#3B82F6',
            'investment' => '#8B5CF6',
            'rental' => '#F59E0B',
            'business' => '#6366F1',
            'other' => '#6B7280',
        ];
        
        foreach ($users as $user) {
            foreach ($defaultCategories as $name => $color) {
                UserIncomeCategory::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $name
                    ],
                    [
                        'color' => $color,
                        'is_default' => true
                    ]
                );
            }
        }
    }
}
