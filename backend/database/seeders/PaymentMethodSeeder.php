<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // Default payment methods
        $methods = [
            [
                'name' => 'Cash',
                'description' => 'Cash payment',
                'is_default' => true,
            ],
            [
                'name' => 'Credit Card',
                'description' => 'Credit card payment',
                'is_default' => true,
            ],
            [
                'name' => 'Debit Card',
                'description' => 'Debit card payment',
                'is_default' => true,
            ],
            [
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'is_default' => true,
            ],
            [
                'name' => 'Mobile Payment',
                'description' => 'Mobile payment apps',
                'is_default' => true,
            ],
        ];

        // Create default payment methods for each user
        foreach ($users as $user) {
            foreach ($methods as $method) {
                PaymentMethod::firstOrCreate(
                    ['name' => $method['name'], 'user_id' => $user->id],
                    [
                        'description' => $method['description'],
                        'is_default' => $method['is_default'],
                    ]
                );
            }
        }
    }
}
