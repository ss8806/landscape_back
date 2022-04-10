<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            [
                'name' => 'a',
                'email' => 'a@a.com',
                'password' => \Hash::make('a'),
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'yamada',
                'email' => 'yamada@example.com',
                'password' => \Hash::make('123456789'),
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'tanaka',
                'email' => 'tanaka@example.com',
                'password' => \Hash::make('123456789'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}