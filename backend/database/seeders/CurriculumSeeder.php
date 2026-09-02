<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Curriculum;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | BSIT
        |--------------------------------------------------------------------------
        */

        $bsit = Course::where('code', 'BSIT')->first();

        if ($bsit) {

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsit->id,
                    'effective_year' => '2026'
                ],
                [
                    'name' => '2026 Curriculum',
                    'active' => 1
                ]
            );

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsit->id,
                    'effective_year' => '2025'
                ],
                [
                    'name' => '2025 Curriculum',
                    'active' => 0
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BSAIS
        |--------------------------------------------------------------------------
        */

        $bsais = Course::where('code', 'BSAIS')->first();

        if ($bsais) {

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsais->id,
                    'effective_year' => '2026'
                ],
                [
                    'name' => '2026 Curriculum',
                    'active' => 1
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BSBA FINANCIAL MANAGEMENT
        |--------------------------------------------------------------------------
        */

        $bsbaFM = Course::where('code', 'BSBA-FM')->first();

        if ($bsbaFM) {

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsbaFM->id,
                    'effective_year' => '2024'
                ],
                [
                    'name' => '2024-2025 Curriculum',
                    'active' => 1
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BSBA MARKETING MANAGEMENT
        |--------------------------------------------------------------------------
        */

        $bsbaMM = Course::where('code', 'BSBA-MM')->first();

        if ($bsbaMM) {

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsbaMM->id,
                    'effective_year' => '2024'
                ],
                [
                    'name' => '2024-2025 Curriculum',
                    'active' => 1
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BSBA HUMAN RESOURCE MANAGEMENT
        |--------------------------------------------------------------------------
        */

        $bsbaHRM = Course::where('code', 'BSBA-HRM')->first();

        if ($bsbaHRM) {

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsbaHRM->id,
                    'effective_year' => '2024'
                ],
                [
                    'name' => '2024-2025 Curriculum',
                    'active' => 1
                ]
            );
        }

                /*
        |--------------------------------------------------------------------------
        | BSCRIM
        |--------------------------------------------------------------------------
        */

        $bsCRIM = Course::where('code', 'BSCRIM')->first();

        if ($bsCRIM) {

            Curriculum::updateOrCreate(
                [
                    'course_id' => $bsCRIM->id,
                    'effective_year' => '2022'
                ],
                [
                    'name' => '2022-2023 Curriculum',
                    'active' => 1
                ]
            );
        }
    }
}