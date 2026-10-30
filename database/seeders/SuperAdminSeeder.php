<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@erp.local',
            ],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('Admin@123456'),
                'role' => 'super_admin',
                'company_id' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}