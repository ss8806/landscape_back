<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('categories')->insert([
            [
                'rate' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rate' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                'rate' => '3',
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                'rate' => '4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rate' => '5',
                'created_at' => now(),
                'updated_at' => now(),
            ],      
        ]);
    }
}
