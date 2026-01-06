<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@aisat.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }
}