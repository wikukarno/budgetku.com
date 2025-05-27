<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FillUserUuidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if (is_null($user->uuid)) {
                $user->uuid = Str::uuid();
                $user->save();
            }
        }
        
        $this->command->info('User UUIDs have been filled successfully.');
    }
}
