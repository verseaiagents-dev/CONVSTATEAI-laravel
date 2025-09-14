<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısı oluştur
        User::create([
            'name' => 'Kadir Durmazlar',
            'email' => 'kadirdurmazlar@gmail.com',
            'password' => Hash::make('Copperage.26'),
            'is_admin' => true,
            'email_verified_at' => now(),
            'language' => 'tr',
            'personal_token' => bin2hex(random_bytes(32)),
            'token_expires_at' => now()->addYears(10),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: kadirdurmazlar@gmail.com');
        $this->command->info('Password: Copperage.26');
    }
}
