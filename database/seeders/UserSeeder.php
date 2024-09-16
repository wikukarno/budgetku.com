<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
            'name'          => 'Wiku Karno',
            'email'         => 'prasetyagama2@gmail.com',
            'roles'         => 'Owner',
            'password'      => bcrypt('admin'), // password
            'created_at'    => date('Y-m-d h:i:s'),
            'updated_at'    => date('Y-m-d h:i:s'),
        ],
        [
            'name'          => 'Customer',
            'email'         => 'hi@wikukarno.com',
            'roles'         => 'Customer',
            'password'      => bcrypt('customer'), // password
            'created_at'    => date('Y-m-d h:i:s'),
            'updated_at'    => date('Y-m-d h:i:s'),
        ]


    ];

        User::insert($user);
    }
}
