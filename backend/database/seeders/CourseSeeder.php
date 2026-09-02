<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [

            /*
            |--------------------------------------------------------------------------
            | BSIT
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BSIT',
                'name' => 'BS Information Technology',
                'department' => 'College of Computing Studies'
            ],


            /*
            |--------------------------------------------------------------------------
            | BSAIS
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BSAIS',
                'name' => 'BS Accounting Information System',
                'department' => 'College of Business Education'
            ],


            /*
            |--------------------------------------------------------------------------
            | BSBA FINANCIAL MANAGEMENT
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BSBA-FM',
                'name' => 'BS Business Administration Major in Financial Management',
                'department' => 'College of Business Education'
            ],


            /*
            |--------------------------------------------------------------------------
            | BSBA MARKETING MANAGEMENT
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BSBA-MM',
                'name' => 'BS Business Administration Major in Marketing Management',
                'department' => 'College of Business Education'
            ],
            /*
            |--------------------------------------------------------------------------
            | BSBA HUMAN RESOURCE MANAGEMENT
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'BSBA-HRM',
                'name' => 'BS Business Administration - Human Resource Management',
                'department' => 'College of Business Education'
            ],


            /*
            |--------------------------------------------------------------------------
            | BEED
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BEED',
                'name' => 'Bachelor of Elementary Education',
                'department' => 'College of Education'
            ],


            /*
            |--------------------------------------------------------------------------
            | BSED
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BSED',
                'name' => 'Bachelor of Secondary Education',
                'department' => 'College of Education'
            ],


            /*
            |--------------------------------------------------------------------------
            | BSCRIM
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BSCRIM',
                'name' => 'BS Criminology',
                'department' => 'College of Criminal Justice Education'
            ],

        ];


        foreach ($courses as $course) {

            Course::updateOrCreate(

                [
                    'code' => $course['code']
                ],

                [
                    'name' => $course['name'],
                    'department' => $course['department']
                ]

            );

        }
    }
}