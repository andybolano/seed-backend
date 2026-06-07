<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@seed.dev'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('Admin1234!'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        // Demo user (email verified)
        $user = User::firstOrCreate(
            ['email' => 'user@seed.dev'],
            [
                'name'              => 'Usuario Demo',
                'password'          => Hash::make('User1234!'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('user');
    }
}
