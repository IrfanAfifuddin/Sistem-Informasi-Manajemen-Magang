<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\InternProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@system.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        // 2. Mentor
        $mentor = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.mentor@example.com',
            'username' => 'mentor',
            'password' => Hash::make('mentor123'),
            'role' => 'mentor',
            'is_first_login' => false,
        ]);

        // 3. Intern (with default password as NIM)
        $intern = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.intern@example.com',
            'username' => '12345678', // NIM
            'password' => Hash::make('12345678'), // NIM as default password
            'role' => 'intern',
            'is_first_login' => true, // Force change password on first login
        ]);

        // 4. Intern Profile (mapped to Mentor)
        InternProfile::create([
            'user_id' => $intern->id,
            'university' => 'Universitas Indonesia',
            'major' => 'Teknik Informatika',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(3)->endOfMonth(),
            'mentor_id' => $mentor->id, // Mapped to Mentor Budi
        ]);
    }
}
