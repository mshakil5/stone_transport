<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
               'name'=>'Admin',
               'surname'=>'Admin',
               'email'=>'admin@gmail.com',
               'is_type'=>1,
               'password'=> Hash::make('123456'),
               'house_number'=>'H-231',
               'phone'=>'01676026364',
               'street_name'=>'DUKE Lane',
               'town'=>'modern city',
               'postcode'=>'232RBT',
            ],
            [
               'name'=>'Manager',
               'surname'=>'Manager',
               'email'=>'manager@gmail.com',
               'is_type'=> 2,
               'password'=> Hash::make('123456'),
               'house_number'=>'H-231',
               'phone'=>'01676026364',
               'street_name'=>'DUKE Lane',
               'town'=>'modern city',
               'postcode'=>'232RBT',
            ],
            [
               'name'=>'User',
               'surname'=>'User',
               'email'=>'user@gmail.com',
               'is_type'=>0,
               'password'=> Hash::make('123456'),
               'house_number'=>'H-231',
               'phone'=>'01676026364',
               'street_name'=>'DUKE Lane',
               'town'=>'modern city',
               'postcode'=>'232RBT',
            ],
        ];

        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
