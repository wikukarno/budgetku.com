<?php

namespace Database\Seeders;

use App\Models\PaymentMethod ;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cash',
                'icon' => 'cash.svg',
            ],
            [
                'name' => 'Bank Transfer',
                'icon' => 'bank-transfer.svg',
            ],
            [
                'name' => 'Credit Card',
                'icon' => 'credit-card.svg',
            ],
            [
                'name' => 'Debit Card',
                'icon' => 'debit-card.svg',
            ],
            [
                'name' => 'PayPal',
                'icon' => 'paypal.svg',
            ],
            [
                'name' => 'E-Wallet',
                'icon' => 'e-wallet.svg',
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create([
                'name' => $method['name'],
                'icon' => $method['icon'],
                'users_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
