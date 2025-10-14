<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::create([
            'name'              => 'Suemi Online Shop',
            'email'             => 'suemionlineshop.official@gmail.com',
            'password'          => Hash::make('admin'), // 🔐 always hash passwords
            'contact_number'    => '09000000000',
            'sss_number'        => '',
            'pagibig_number'    => '',
            'philhealth_number' => '',
            'hourly_rate'       => 0,
            'daily_rate'        => 0,
            'signature'         => 'signatures/john-sign.png', // store file path if you want , public/signatures
            'role_id'           =>1,
            'is_employee'        => 'No',
            'is_live_seller'        => 'No',
        ]);

        User::create([
            'name'              => 'Developer',
            'email'             => 'developer@gmail.com',
            'password'          => Hash::make('developer'), // 🔐 always hash passwords
            'contact_number'    => '09000000001',
            'sss_number'        => '2',
            'pagibig_number'    => '2',
            'philhealth_number' => '2',
            'hourly_rate'       => 0,
            'daily_rate'        => 0,
            'signature'         => 'signatures/john-sign.png', // store file path if you want , public/signatures
            'role_id'           =>1,
            'is_employee'        => 'No',
            'is_live_seller'        => 'No',
        ]);
    }
}
