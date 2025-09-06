<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'uuid'          => Str::uuid(),
            'name'          => 'Wiku Karno',
            'email'         => 'hi@wikukarno.com',
            'roles'         => 'Owner',
            'password'      => bcrypt('admin12345'), // password
            'created_at'    => date('Y-m-d h:i:s'),
            'updated_at'    => date('Y-m-d h:i:s'),
        ],
        [
            'uuid'          => Str::uuid(),
            'name'          => 'Customer',
            'email'         => 'karnowiku@gmail.com',
            'roles'         => 'Customer',
            'password'      => bcrypt('admin12345'), // password
            'created_at'    => date('Y-m-d h:i:s'),
            'updated_at'    => date('Y-m-d h:i:s'),
        ]


    ];

        User::insert($user);
    }
}
