<?php

namespace Database\Seeders;

use App\Models\Administration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Administration::create([
            'nom' => 'Admin ESTK',
            'email' => 'admin@estk.ma',
            'mot_de_passe' => Hash::make('password123'),
        ]);
    }
} 