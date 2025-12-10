<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Ahmed',
            'email' => 'a@a.com',
            'mobile' => '987654321',
            'role_id' => 1,
            'password' => bcrypt(111111),
            'email_verified_at' => now()->timestamp,
            'verification_code' => '5555'
        ]);

        $user2 = User::create([
            'name' => 'Ahmed2',
            'email' => 'a@a2.com',
            'mobile' => '9876543231',
            'role_id' => 2,
            'password' => bcrypt(111111),
            'email_verified_at' => now()->timestamp,
            'verification_code' => '5556'
        ]);
    }
}
