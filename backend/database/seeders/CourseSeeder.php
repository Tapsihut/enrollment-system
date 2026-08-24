<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {

        $courses = [

            [
                'code'=>'BSIT',
                'name'=>'BS Information Technology',
                'department'=>'College of Computing Studies'
            ],

            [
                'code'=>'BSAIS',
                'name'=>'BS Accounting Information System',
                'department'=>'College of Business Education'
            ],

            [
                'code'=>'BSBA',
                'name'=>'BS Business Administration',
                'department'=>'College of Business Education'
            ],

            [
                'code'=>'BEED',
                'name'=>'Bachelor of Elementary Education',
                'department'=>'College of Education'
            ],

            [
                'code'=>'BSED',
                'name'=>'Bachelor of Secondary Education',
                'department'=>'College of Education'
            ],

            [
                'code'=>'BSCRIM',
                'name'=>'BS Criminology',
                'department'=>'College of Criminal Justice Education'
            ],

        ];


        foreach($courses as $course){

            Course::updateOrCreate(

                [
                    'code'=>$course['code']
                ],

                [
                    'name'=>$course['name'],
                    'department'=>$course['department']
                ]

            );

        }

    }
}