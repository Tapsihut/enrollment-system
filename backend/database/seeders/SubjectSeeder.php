<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {

        $subjects = [

            // FIRST YEAR - FIRST SEMESTER

            [
                'code'=>'ITc 1',
                'title'=>'Introduction to Computing',
                'units'=>3
            ],

            [
                'code'=>'ITc 2',
                'title'=>'Computer Programming 1',
                'units'=>3
            ],

            [
                'code'=>'GE 5',
                'title'=>'Purposive Communication',
                'units'=>3
            ],

            [
                'code'=>'GE 6',
                'title'=>'Contemporary World',
                'units'=>3
            ],

            [
                'code'=>'PATH-FIT 1',
                'title'=>'Movement Competency Training',
                'units'=>2
            ],

            [
                'code'=>'NSTP 1',
                'title'=>'National Service Training Program 1',
                'units'=>3
            ],

            [
                'code'=>'ITP1',
                'title'=>'Applied Linear Algebra',
                'units'=>3
            ],

            [
                'code'=>'BE Elect 3',
                'title'=>'Reading Visual Arts',
                'units'=>3
            ],

            [
                'code'=>'TIBI 101',
                'title'=>'Introduction to Data Analytics',
                'units'=>3
            ],



            // FIRST YEAR - SECOND SEMESTER


            [
                'code'=>'ITp 2',
                'title'=>'Introduction to Human Computer Interaction',
                'units'=>3
            ],

            [
                'code'=>'ITc 3',
                'title'=>'Data Structure and Algorithms',
                'units'=>3
            ],

            [
                'code'=>'ITc 4',
                'title'=>'Computer Programming 2',
                'units'=>3
            ],

            [
                'code'=>'GE 1',
                'title'=>'Understanding the Self',
                'units'=>3
            ],

            [
                'code'=>'GE 3',
                'title'=>'Mathematics in the Modern World',
                'units'=>3
            ],

            [
                'code'=>'ACCTG 101',
                'title'=>'Fundamentals of Computerized Accounting',
                'units'=>3
            ],

            [
                'code'=>'PATH-FIT 2',
                'title'=>'Philippine and International Folk Dances',
                'units'=>2
            ],

            [
                'code'=>'NSTP 2',
                'title'=>'National Service Training Program 2',
                'units'=>3
            ],

            [
                'code'=>'TIBI 102',
                'title'=>'Application of Statistics in IT',
                'units'=>3
            ],



            // SECOND YEAR FIRST SEMESTER


            [
                'code'=>'ITc 5',
                'title'=>'Information Management 1',
                'units'=>3
            ],

            [
                'code'=>'ITp 3',
                'title'=>'Discrete Mathematics',
                'units'=>3
            ],

            [
                'code'=>'IT Elective 1',
                'title'=>'Platform Technologies',
                'units'=>3
            ],

            [
                'code'=>'ITp 4',
                'title'=>'Integrative Programming and Technologies 1',
                'units'=>3
            ],

            [
                'code'=>'GE 7',
                'title'=>'Science Technology and Society',
                'units'=>3
            ],

            [
                'code'=>'GE 8',
                'title'=>'Ethics',
                'units'=>3
            ],

            [
                'code'=>'BE Elect 1',
                'title'=>'Entrepreneurial Mind',
                'units'=>3
            ],

            [
                'code'=>'PATH-FIT 3',
                'title'=>'Sports',
                'units'=>2
            ],



            // SECOND YEAR SECOND SEMESTER


            [
                'code'=>'ITp 5',
                'title'=>'Fundamentals of Database Systems',
                'units'=>3
            ],

            [
                'code'=>'ITp 6',
                'title'=>'Quantitative Methods',
                'units'=>3
            ],

            [
                'code'=>'ITp 7',
                'title'=>'Networking 1',
                'units'=>3
            ],

            [
                'code'=>'ITe 2',
                'title'=>'Elective 2 (Object-Oriented Programming)',
                'units'=>3
            ],

            [
                'code'=>'GE 2',
                'title'=>'Readings in the Philippine History',
                'units'=>3
            ],

            [
                'code'=>'BE Elect 2',
                'title'=>'Living in the IT ERA',
                'units'=>3
            ],

            [
                'code'=>'GE 4',
                'title'=>'Art Appreciation',
                'units'=>3
            ],

            [
                'code'=>'PATH-FIT 4',
                'title'=>'Outdoor and Adventure Activities',
                'units'=>2
            ],

            [
                'code'=>'SFXC 1',
                'title'=>'Becoming a Xavier Knight 1',
                'units'=>2
            ],



            // THIRD YEAR FIRST SEMESTER


            [
                'code'=>'ITp 8',
                'title'=>'Networking 2',
                'units'=>3
            ],

            [
                'code'=>'ITp 9',
                'title'=>'System Integration and Architecture 1',
                'units'=>3
            ],

            [
                'code'=>'Rizal',
                'title'=>'Life and Works of Rizal',
                'units'=>3
            ],

            [
                'code'=>'ITe 3',
                'title'=>'Elective 3 (Web Systems and Technologies)',
                'units'=>3
            ],

            [
                'code'=>'ITp 10',
                'title'=>'Special Topic 1',
                'units'=>3
            ],

            [
                'code'=>'ITp 11',
                'title'=>'Operating Systems and Applications',
                'units'=>3
            ],

            [
                'code'=>'ITp 12',
                'title'=>'Technopreneurship',
                'units'=>3
            ],

            [
                'code'=>'SFXC 2',
                'title'=>'Becoming a Xavier Knight 2',
                'units'=>2
            ],



            // THIRD YEAR SECOND SEMESTER


            [
                'code'=>'ITp 13',
                'title'=>'Information Assurance and Security 1 (Fundamentals of Cyber Security)',
                'units'=>3
            ],

            [
                'code'=>'ITc 6',
                'title'=>'Application Development and Emerging Technologies',
                'units'=>3
            ],

            [
                'code'=>'ITe 4',
                'title'=>'Elective 4 (Human Computer Interaction)',
                'units'=>3
            ],

            [
                'code'=>'ITp 14',
                'title'=>'Social and Professional Issues in Computing',
                'units'=>3
            ],

            [
                'code'=>'ITp 15',
                'title'=>'Special Topic 2 (IT Project Management)',
                'units'=>3
            ],

            [
                'code'=>'ITp 16',
                'title'=>'Capstone Project 1',
                'units'=>3
            ],

            [
                'code'=>'SFXC 3',
                'title'=>'Becoming a Xavier Knight 3',
                'units'=>2
            ],



            // FOURTH YEAR FIRST SEMESTER


            [
                'code'=>'ITp 17',
                'title'=>'System Administration and Maintenance',
                'units'=>3
            ],

            [
                'code'=>'ITp 18',
                'title'=>'Information Assurance and Security 2 (Data Privacy)',
                'units'=>3
            ],

            [
                'code'=>'ITp 19',
                'title'=>'Capstone Project 2',
                'units'=>3
            ],

            [
                'code'=>'OA 5',
                'title'=>'Business Technical and Report Writing',
                'units'=>3
            ],

            [
                'code'=>'ITp 20',
                'title'=>'Special Topic 4 (Eligibility Certification)',
                'units'=>3
            ],

            [
                'code'=>'SFXC 4',
                'title'=>'Becoming a Xavier Knight 4',
                'units'=>2
            ],



            // FOURTH YEAR SECOND SEMESTER


            [
                'code'=>'ITp 21',
                'title'=>'Practicum',
                'units'=>6
            ],

        ];



        foreach($subjects as $subject){

            Subject::create($subject);

        }

    }
}