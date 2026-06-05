<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador principal
        User::firstOrCreate(
            ['email' => 'admin@edcsst.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'), // CAMBIAR EN PRODUCCIÓN
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
