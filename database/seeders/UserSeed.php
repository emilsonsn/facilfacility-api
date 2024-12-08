<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::firstOrCreate([
            'email' => 'admin@admin',
        ],
        [
            'name' => 'Admin',
            'image' => null,
            'password' => Hash::make('admin'),
            'phone' => '83991236636',
            'address' => 'Rua Niza Siqueira de Melo',
            'city' => 'Santa Rita',
            'region' => 'Estados',
            'contry' => 'País',
            'zip_code' => '58301275',
            'is_active' => true,
            'profile' => 'Admin',
        ]);
    }
}
