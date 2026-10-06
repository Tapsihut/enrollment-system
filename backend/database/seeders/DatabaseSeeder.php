<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        $this->call([

            CourseSeeder::class,
            CurriculumSeeder::class,
            SchoolYearSeeder::class,
            SemesterSeeder::class,
            SubjectSeeder::class,
            CurriculumSubjectSeeder::class,
            SchoolSeeder::class,


        ]);

    }
    

}