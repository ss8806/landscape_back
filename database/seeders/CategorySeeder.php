<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
                'name' => '山',
                'sort_no' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '海',
                'sort_no' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                'name' => '空',
                'sort_no' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                'name' => '大地',
                'sort_no' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '川',
                'sort_no' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '森',
                'sort_no' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'その他',
                'sort_no' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],               
        ]);
    }
}
