<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('school_years')->insert([

            [
                'school_year' => '2026-2027',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'school_year' => '2027-2028',
                'is_active' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}