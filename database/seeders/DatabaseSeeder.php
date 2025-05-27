<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Faker\Provider\ar_EG\Payment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            FillUserUuidSeeder::class,
            // UserSeeder::class,
            // PaymentMethodSeeder::class,
        ]);
    }
}
