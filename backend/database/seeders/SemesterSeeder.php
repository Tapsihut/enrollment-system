<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('semesters')->insert([

            [
                'name' => 'First Semester',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Second Semester',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Summer',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}