<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@invoicemanagement.com'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@invoicemanagement.com',
                'password' => Hash::make('password'),
                'company'  => 'Invoice Management Co.',
                'phone'    => '+91 9000000000',
                'address'  => '123 Admin Street, City, State - 000001',
            ]
        );
    }
}
