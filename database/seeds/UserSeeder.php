<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@pukaar.com',
            'mobile_number' => '+923001234567',
            'status_id' => 1,
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole('admin');

        $user = User::create([
            'first_name' => 'Therapist',
            'last_name' => 'Therapist',
            'email' => 'therapist@pukaar.com',
            'mobile_number' => '+923000123458',
            'status_id' => 1,
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole('therapist');

        $user = User::create([
            'first_name' => 'Client',
            'last_name' => 'Client',
            'email' => 'client@pukaar.com',
            'mobile_number' => '+923000123459',
            'status_id' => 1,
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole('client');
    }
}
