<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('curricula')->updateOrInsert(
            [
                'course_id'=>1,
                'effective_year'=>'2026'
            ],
            [
                'name'=>'2026 Curriculum',
                'active'=>1,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        );


        DB::table('curricula')->updateOrInsert(
            [
                'course_id'=>1,
                'effective_year'=>'2025'
            ],
            [
                'name'=>'2025 Curriculum',
                'active'=>0,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        );


        DB::table('curricula')->updateOrInsert(
            [
                'course_id'=>2,
                'effective_year'=>'2026'
            ],
            [
                'name'=>'2026 Curriculum',
                'active'=>1,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        );


        DB::table('curricula')->updateOrInsert(
            [
                'course_id'=>3,
                'effective_year'=>'2026'
            ],
            [
                'name'=>'2026 Curriculum',
                'active'=>1,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        );


    }
}