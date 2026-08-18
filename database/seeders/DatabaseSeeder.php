<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        AdminUser::create([
            'username' => 'MercifulCentre',
            'email' => 'admin@mercifulcentre.org',
            'full_name' => 'Super Admin',
            'password_hash' => Hash::make('Merciful12345'),
            'role' => 'super_admin',
        ]);
    }
}
