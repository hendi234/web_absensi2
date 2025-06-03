<?php

namespace Database\Seeders;

use App\Models\Employe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employe::create([
            'id' => 1,
            'nip' => '1234',
            'name' => 'Admin',
            'avatar' => '1234',
            'email' => 'admin@gmail.com',
            'position' => 'Admin',
            'education' => 'SMA',
            'join_date' => '2023-01-01',
            'created_by' => 1,
        ]);
    }
}
