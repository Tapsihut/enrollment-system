<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [

            /*
            |--------------------------------------------------------------------------
            | BSIT SUBJECTS
            |--------------------------------------------------------------------------
            */

            // FIRST YEAR - FIRST SEMESTER

            [
                'code' => 'ITc 1',
                'title' => 'Introduction to Computing',
                'units' => 3
            ],

            [
                'code' => 'ITc 2',
                'title' => 'Computer Programming 1',
                'units' => 3
            ],

            [
                'code' => 'GE 5',
                'title' => 'Purposive Communication',
                'units' => 3
            ],

            [
                'code' => 'GE 6',
                'title' => 'Contemporary World',
                'units' => 3
            ],

            [
                'code' => 'PATH-FIT 1',
                'title' => 'Movement Competency Training',
                'units' => 2
            ],

            [
                'code' => 'NSTP 1',
                'title' => 'National Service Training Program 1',
                'units' => 3
            ],

            [
                'code' => 'ITP1',
                'title' => 'Applied Linear Algebra',
                'units' => 3
            ],

            [
                'code' => 'BE Elect 3',
                'title' => 'Reading Visual Arts',
                'units' => 3
            ],

            [
                'code' => 'TIBI 101',
                'title' => 'Introduction to Data Analytics',
                'units' => 3
            ],


            // FIRST YEAR - SECOND SEMESTER

            [
                'code' => 'ITp 2',
                'title' => 'Introduction to Human Computer Interaction',
                'units' => 3
            ],

            [
                'code' => 'ITc 3',
                'title' => 'Data Structure and Algorithms',
                'units' => 3
            ],

            [
                'code' => 'ITc 4',
                'title' => 'Computer Programming 2',
                'units' => 3
            ],

            [
                'code' => 'GE 1',
                'title' => 'Understanding the Self',
                'units' => 3
            ],

            [
                'code' => 'GE 3',
                'title' => 'Mathematics in the Modern World',
                'units' => 3
            ],

            [
                'code' => 'ACCTG 101',
                'title' => 'Fundamentals of Computerized Accounting',
                'units' => 3
            ],

            [
                'code' => 'PATH-FIT 2',
                'title' => 'Philippine and International Folk Dances',
                'units' => 2
            ],

            [
                'code' => 'NSTP 2',
                'title' => 'National Service Training Program 2',
                'units' => 3
            ],

            [
                'code' => 'TIBI 102',
                'title' => 'Application of Statistics in IT',
                'units' => 3
            ],


            // SECOND YEAR - FIRST SEMESTER

            [
                'code' => 'ITc 5',
                'title' => 'Information Management 1',
                'units' => 3
            ],

            [
                'code' => 'ITp 3',
                'title' => 'Discrete Mathematics',
                'units' => 3
            ],

            [
                'code' => 'IT Elective 1',
                'title' => 'Platform Technologies',
                'units' => 3
            ],

            [
                'code' => 'ITp 4',
                'title' => 'Integrative Programming and Technologies 1',
                'units' => 3
            ],

            [
                'code' => 'GE 7',
                'title' => 'Science Technology and Society',
                'units' => 3
            ],

            [
                'code' => 'GE 8',
                'title' => 'Ethics',
                'units' => 3
            ],

            [
                'code' => 'BE Elect 1',
                'title' => 'Entrepreneurial Mind',
                'units' => 3
            ],

            [
                'code' => 'PATH-FIT 3',
                'title' => 'Sports',
                'units' => 2
            ],


            // SECOND YEAR - SECOND SEMESTER

            [
                'code' => 'ITp 5',
                'title' => 'Fundamentals of Database Systems',
                'units' => 3
            ],

            [
                'code' => 'ITp 6',
                'title' => 'Quantitative Methods',
                'units' => 3
            ],

            [
                'code' => 'ITp 7',
                'title' => 'Networking 1',
                'units' => 3
            ],

            [
                'code' => 'ITe 2',
                'title' => 'Elective 2 (Object-Oriented Programming)',
                'units' => 3
            ],

            [
                'code' => 'GE 2',
                'title' => 'Readings in the Philippine History',
                'units' => 3
            ],

            [
                'code' => 'BE Elect 2',
                'title' => 'Living in the IT Era',
                'units' => 3
            ],

            [
                'code' => 'GE 4',
                'title' => 'Art Appreciation',
                'units' => 3
            ],

            [
                'code' => 'PATH-FIT 4',
                'title' => 'Outdoor and Adventure Activities',
                'units' => 2
            ],

            [
                'code' => 'SFXC 1',
                'title' => 'Becoming a Xavier Knight 1',
                'units' => 2
            ],


            // THIRD YEAR - FIRST SEMESTER

            [
                'code' => 'ITp 8',
                'title' => 'Networking 2',
                'units' => 3
            ],

            [
                'code' => 'ITp 9',
                'title' => 'System Integration and Architecture 1',
                'units' => 3
            ],

            [
                'code' => 'Rizal',
                'title' => 'Life and Works of Rizal',
                'units' => 3
            ],

            [
                'code' => 'ITe 3',
                'title' => 'Elective 3 (Web Systems and Technologies)',
                'units' => 3
            ],

            [
                'code' => 'ITp 10',
                'title' => 'Special Topic 1',
                'units' => 3
            ],

            [
                'code' => 'ITp 11',
                'title' => 'Operating Systems and Applications',
                'units' => 3
            ],

            [
                'code' => 'ITp 12',
                'title' => 'Technopreneurship',
                'units' => 3
            ],

            [
                'code' => 'SFXC 2',
                'title' => 'Becoming a Xavier Knight 2',
                'units' => 2
            ],


            // THIRD YEAR - SECOND SEMESTER

            [
                'code' => 'ITp 13',
                'title' => 'Information Assurance and Security 1 (Fundamentals of Cyber Security)',
                'units' => 3
            ],

            [
                'code' => 'ITc 6',
                'title' => 'Application Development and Emerging Technologies',
                'units' => 3
            ],

            [
                'code' => 'ITe 4',
                'title' => 'Elective 4 (Human Computer Interaction)',
                'units' => 3
            ],

            [
                'code' => 'ITp 14',
                'title' => 'Social and Professional Issues in Computing',
                'units' => 3
            ],

            [
                'code' => 'ITp 15',
                'title' => 'Special Topic 2 (IT Project Management)',
                'units' => 3
            ],

            [
                'code' => 'ITp 16',
                'title' => 'Capstone Project 1',
                'units' => 3
            ],

            [
                'code' => 'SFXC 3',
                'title' => 'Becoming a Xavier Knight 3',
                'units' => 2
            ],


            // FOURTH YEAR - FIRST SEMESTER

            [
                'code' => 'ITp 17',
                'title' => 'System Administration and Maintenance',
                'units' => 3
            ],

            [
                'code' => 'ITp 18',
                'title' => 'Information Assurance and Security 2 (Data Privacy)',
                'units' => 3
            ],

            [
                'code' => 'ITp 19',
                'title' => 'Capstone Project 2',
                'units' => 3
            ],

            [
                'code' => 'OA 5',
                'title' => 'Business Technical and Report Writing',
                'units' => 3
            ],

            [
                'code' => 'ITp 20',
                'title' => 'Special Topic 4 (Eligibility Certification)',
                'units' => 3
            ],

            [
                'code' => 'SFXC 4',
                'title' => 'Becoming a Xavier Knight 4',
                'units' => 2
            ],


            // FOURTH YEAR - SECOND SEMESTER

            [
                'code' => 'ITp 21',
                'title' => 'Practicum',
                'units' => 6
            ],


            /*
            |--------------------------------------------------------------------------
            | COMMON / BSBA SUBJECTS
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BE ELECT 2',
                'title' => 'Living in the IT Era',
                'units' => 3
            ],

            [
                'code' => 'FPBL',
                'title' => 'Formation Program for Business Learner',
                'units' => 3
            ],

            [
                'code' => 'NSTP1A',
                'title' => 'Civic Welfare and Training Services 1',
                'units' => 3
            ],

            [
                'code' => 'BA101',
                'title' => 'Business Calculus',
                'units' => 3
            ],

            [
                'code' => 'BA1',
                'title' => 'Math of Investment',
                'units' => 3
            ],

            [
                'code' => 'GE 9',
                'title' => 'The Life and Works of Jose Rizal',
                'units' => 3
            ],

            [
                'code' => 'NSTP2A',
                'title' => 'Civic Welfare and Training Services 2',
                'units' => 3
            ],

            [
                'code' => 'OA16',
                'title' => 'Operations Management and TQM',
                'units' => 3
            ],

            [
                'code' => 'B-LAW 1',
                'title' => 'Law on Obligations and Contracts',
                'units' => 3
            ],

            [
                'code' => 'BA2',
                'title' => 'Basic Microeconomics',
                'units' => 3
            ],

            [
                'code' => 'BA3',
                'title' => 'Human Resource Management',
                'units' => 3
            ],

            [
                'code' => 'BA4',
                'title' => 'International Business and Trade',
                'units' => 3
            ],

            [
                'code' => 'BA5',
                'title' => 'Good Governance and Social Responsibility',
                'units' => 3
            ],

            [
                'code' => 'BA6',
                'title' => 'Business Research',
                'units' => 3
            ],

            [
                'code' => 'BA8',
                'title' => 'Research Methodology with Statistics',
                'units' => 3
            ],

            [
                'code' => 'ACCTG 3',
                'title' => 'Managerial Accounting',
                'units' => 3
            ],

            [
                'code' => 'ACCTG 3A',
                'title' => 'Principles of Cost Accounting',
                'units' => 3
            ],

            [
                'code' => 'TAX 1',
                'title' => 'Income Taxation',
                'units' => 3
            ],

            [
                'code' => 'TAX 2',
                'title' => 'Business and Transfer Tax',
                'units' => 3
            ],

            [
                'code' => 'B-LAW 2',
                'title' => 'Partnership and Corporation Law',
                'units' => 3
            ],

            [
                'code' => 'B-LAW 3',
                'title' => 'Sales, Agency and Other Mercantile Law',
                'units' => 3
            ],

            [
                'code' => 'OA15',
                'title' => 'Strategic Management',
                'units' => 3
            ],

            [
                'code' => 'OA16',
                'title' => 'Operations Management and TQM',
                'units' => 3
            ],


            /*
            |--------------------------------------------------------------------------
            | BSBA FINANCIAL MANAGEMENT
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BAFM1',
                'title' => 'Financial Management 1',
                'units' => 3
            ],

            [
                'code' => 'BAFM2',
                'title' => 'Financial Management 2',
                'units' => 3
            ],

            [
                'code' => 'BAFM3',
                'title' => 'Financial Management 3',
                'units' => 3
            ],

            [
                'code' => 'BAFM4',
                'title' => 'Financial Management 4',
                'units' => 3
            ],

            [
                'code' => 'BAFM5',
                'title' => 'Financial Management 5',
                'units' => 3
            ],

            [
                'code' => 'BAFM6',
                'title' => 'Financial Management 6',
                'units' => 3
            ],

            [
                'code' => 'BAFM7',
                'title' => 'Financial Management 7',
                'units' => 3
            ],

            [
                'code' => 'BAFM9',
                'title' => 'Financial Management 9',
                'units' => 3
            ],

            [
                'code' => 'BAFM10',
                'title' => 'Financial Management 10',
                'units' => 3
            ],

            [
                'code' => 'BAFM11',
                'title' => 'Financial Management 11',
                'units' => 3
            ],

            [
                'code' => 'BAFM12',
                'title' => 'Financial Management 12',
                'units' => 3
            ],

            [
                'code' => 'BA9',
                'title' => 'Feasibility Study',
                'units' => 3
            ],

            [
                'code' => 'BAPrac 1',
                'title' => 'Practicum 1',
                'units' => 3
            ],

            [
                'code' => 'BAPrac2',
                'title' => 'Practicum 2',
                'units' => 3
            ],


            /*
            |--------------------------------------------------------------------------
            | BSBA MARKETING MANAGEMENT
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BAMM2',
                'title' => 'Professional Salesmanship',
                'units' => 3
            ],

            [
                'code' => 'BAMM3',
                'title' => 'Marketing Research',
                'units' => 3
            ],

            [
                'code' => 'BAMM4',
                'title' => 'Pricing Strategy',
                'units' => 3
            ],

            [
                'code' => 'BAMM5',
                'title' => 'Advertising',
                'units' => 3
            ],

            [
                'code' => 'BAMM6',
                'title' => 'Retail Management',
                'units' => 3
            ],

            [
                'code' => 'BAMM7',
                'title' => 'Product Management',
                'units' => 3
            ],

            [
                'code' => 'BAMM8',
                'title' => 'Distribution Management',
                'units' => 3
            ],

            [
                'code' => 'BAMM9',
                'title' => 'Consumer Behavior',
                'units' => 3
            ],

            [
                'code' => 'BAMM10',
                'title' => 'Marketing Management',
                'units' => 3
            ],

            [
                'code' => 'BAMM11',
                'title' => 'E-Commerce and Internet Marketing',
                'units' => 3
            ],

            [
                'code' => 'BAMM12',
                'title' => 'Entrepreneurial Management',
                'units' => 3
            ],

            [
                'code' => 'BAMM19',
                'title' => 'Franchising',
                'units' => 3
            ],


            /*
            |--------------------------------------------------------------------------
            | OTHER MARKETING SUBJECTS
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BE ELECT 1',
                'title' => 'The Entrepreneurial Mind',
                'units' => 3
            ],

            [
                'code' => 'OA 5',
                'title' => 'Business Technical and Report Writing',
                'units' => 3
            ],


            /*
            |--------------------------------------------------------------------------
            | BSBA HUMAN RESOURCE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'HRM 1',
                'title' => 'Human Behavior in Organization',
                'units' => 3
            ],

            [
                'code' => 'GE 10',
                'title' => 'Business Psychology',
                'units' => 3
            ],

            [
                'code' => 'BA 1',
                'title' => 'Math of Investment',
                'units' => 3
            ],

            [
                'code' => 'HRM 2',
                'title' => 'Marketing Management',
                'units' => 3
            ],

            [
                'code' => 'OA 4',
                'title' => 'Personal and Professional Development',
                'units' => 3
            ],

            [
                'code' => 'ICT 2',
                'title' => 'Living in the IT Era',
                'units' => 3
            ],

            [
                'code' => 'NSTP 2A',
                'title' => 'Civic Welfare Training Service 2',
                'units' => 3
            ],

            [
                'code' => 'ICT 3',
                'title' => 'Computer Applications in Business',
                'units' => 3
            ],

            [
                'code' => 'BE 1',
                'title' => 'Entrepreneurial Mind',
                'units' => 3
            ],

            [
                'code' => 'HRM 3',
                'title' => 'Administrative and Office Management',
                'units' => 3
            ],

            [
                'code' => 'HRM 4',
                'title' => 'Quantitative Techniques in Business',
                'units' => 3
            ],

            [
                'code' => 'HRM 5',
                'title' => 'Management Information System',
                'units' => 3
            ],

            [
                'code' => 'HRM 6',
                'title' => 'Recruitment and Selection',
                'units' => 3
            ],

            [
                'code' => 'OA 14',
                'title' => 'Introduction to Project Management',
                'units' => 3
            ],

            [
                'code' => 'HRM 7',
                'title' => 'Labor Law and Legislation',
                'units' => 3
            ],

            [
                'code' => 'HRM 8',
                'title' => 'Training and Development',
                'units' => 3
            ],

            [
                'code' => 'OA 10',
                'title' => 'Event Management',
                'units' => 3
            ],

            [
                'code' => 'HRM 9',
                'title' => 'Labor Relations and Negotiations',
                'units' => 3
            ],

            [
                'code' => 'HRM 10',
                'title' => 'Compensation Administrative',
                'units' => 3
            ],

            [
                'code' => 'HRM 14',
                'title' => 'Business Research',
                'units' => 3
            ],

            [
                'code' => 'HRM 11',
                'title' => 'Special Topics in Resource Management',
                'units' => 3
            ],

            [
                'code' => 'HRM 12',
                'title' => 'Logistics Management',
                'units' => 3
            ],

            [
                'code' => 'HRM 13',
                'title' => 'Organizational Development',
                'units' => 3
            ],

            [
                'code' => 'HRM 15',
                'title' => 'Thesis',
                'units' => 3
            ],

            [
                'code' => 'HRM 16',
                'title' => 'Internship (600 hours)',
                'units' => 6
            ],


            /*
            |--------------------------------------------------------------------------
            | COMMON SUBJECTS USED BY HRM / OTHER BSBA PROGRAMS
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'BA 2',
                'title' => 'Basic Microeconomics',
                'units' => 3
            ],

            [
                'code' => 'ACCTG 3',
                'title' => 'Managerial Accounting',
                'units' => 3
            ],

            [
                'code' => 'Tax 1',
                'title' => 'Income Taxation',
                'units' => 3
            ],

            [
                'code' => 'Rizal',
                'title' => 'Life and Works of Rizal',
                'units' => 3
            ],

            [
                'code' => 'B-Law 1',
                'title' => 'Obligations and Contracts',
                'units' => 3
            ],

            [
                'code' => 'OA 15',
                'title' => 'Strategic Management',
                'units' => 3
            ],


            /*
            |--------------------------------------------------------------------------
            | SFXC COMMON SUBJECTS
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'SFXC 1',
                'title' => 'Becoming a Xavier Knight 1',
                'units' => 2
            ],

            [
                'code' => 'SFXC 2',
                'title' => 'Becoming a Xavier Knight 2',
                'units' => 2
            ],

            [
                'code' => 'SFXC 3',
                'title' => 'Becoming a Xavier Knight 3',
                'units' => 2
            ],

            [
                'code' => 'SFXC 4',
                'title' => 'Becoming a Xavier Knight 4',
                'units' => 2
            ],


            /*
            |--------------------------------------------------------------------------
            | PRACTICUM / FEASIBILITY
            |--------------------------------------------------------------------------
            */

            [
                'code' => 'Feasibility Study',
                'title' => 'Feasibility Study',
                'units' => 3
            ],

            [
                'code' => 'Practicum 1',
                'title' => 'Practicum 1',
                'units' => 3
            ],

            [
                'code' => 'BAPrac2',
                'title' => 'Practicum 2',
                'units' => 3
            ],

            /*
            |--------------------------------------------------------------------------
            | BS CRIMINOLOGY
            | Curriculum AY 2022-2023
            | CMO No. 5 Series of 2018
            |--------------------------------------------------------------------------
            */

            // FIRST YEAR - FIRST SEMESTER

            [
                'code' => 'GE-Elect 2',
                'title' => 'Gender and Development Society',
                'units' => 3
            ],

            [
                'code' => 'IC 1',
                'title' => 'ICT Fundamentals with Office Productivity',
                'units' => 3
            ],

            [
                'code' => 'DefTac 101',
                'title' => 'Fundamentals of Martial Arts',
                'units' => 2
            ],

            [
                'code' => 'Crim 1',
                'title' => 'Introduction to Criminology',
                'units' => 3
            ],

            [
                'code' => 'ROTC 1',
                'title' => 'Military Science 1',
                'units' => 3
            ],


            // FIRST YEAR - SECOND SEMESTER

            [
                'code' => 'GE-Elect 4',
                'title' => 'Introduction to Logic',
                'units' => 3
            ],

            [
                'code' => 'CLJ 1',
                'title' => 'Introduction to Philippine Criminal Justice System',
                'units' => 3
            ],

            [
                'code' => 'Crim 2',
                'title' => 'Theories of Crime Causation',
                'units' => 3
            ],

            [
                'code' => 'DefTac 102',
                'title' => 'Arnis and Disarming Techniques',
                'units' => 2
            ],

            [
                'code' => 'ROTC 2',
                'title' => 'Military Science 2',
                'units' => 3
            ],


            // FIRST YEAR - SUMMER

            [
                'code' => 'BE 1',
                'title' => 'Entrepreneurial Mind',
                'units' => 3
            ],


            // SECOND YEAR - FIRST SEMESTER

            [
                'code' => 'GE-Elect 5',
                'title' => 'Environmental Science',
                'units' => 3
            ],

            [
                'code' => 'CFLM 1',
                'title' => 'Character Formation 1 - Nationalism and Patriotism',
                'units' => 3
            ],

            [
                'code' => 'CDI 1',
                'title' => 'Fundamentals of Investigation and Intelligence',
                'units' => 4
            ],

            [
                'code' => 'Crim 3',
                'title' => 'Human Behavior Victimology',
                'units' => 3
            ],

            [
                'code' => 'LEA 1',
                'title' => 'Law Enforcement Organization and Administration',
                'units' => 4
            ],

            [
                'code' => 'LEA 2',
                'title' => 'Comparative Models in Policing',
                'units' => 3
            ],

            [
                'code' => 'LEA 3',
                'title' => 'Introduction to Industrial Security Concepts',
                'units' => 3
            ],

            [
                'code' => 'DefTac 103',
                'title' => 'First Aid and Water Safety',
                'units' => 2
            ],


            // SECOND YEAR - SECOND SEMESTER

            [
                'code' => 'GE-Elect 6',
                'title' => 'General Chemistry (Organic)',
                'units' => 3
            ],

            [
                'code' => 'CFLM 2',
                'title' => 'Character Formation 2 - Leadership, Decision Making, Management and Administration',
                'units' => 3
            ],

            [
                'code' => 'Forensic 1',
                'title' => 'Forensic Photography',
                'units' => 3
            ],

            [
                'code' => 'CDI 2',
                'title' => 'Specialized Crime Investigation 1 with Legal Medicine',
                'units' => 3
            ],

            [
                'code' => 'CDI 3',
                'title' => 'Traffic Management and Accident Investigation with Driving',
                'units' => 3
            ],

            [
                'code' => 'Crim 4',
                'title' => 'Professional Conduct and Ethical Standards',
                'units' => 3
            ],

            [
                'code' => 'Forensic 2',
                'title' => 'Personal Identification Techniques',
                'units' => 3
            ],

            [
                'code' => 'DefTac 104',
                'title' => 'Fundamentals of Marksmanship',
                'units' => 2
            ],

            [
                'code' => 'CLJ 2',
                'title' => 'Human Rights Education with Peace Studies Education',
                'units' => 3
            ],


            // THIRD YEAR - FIRST SEMESTER

            [
                'code' => 'Forensic 3',
                'title' => 'Forensic Chemistry and Toxicology',
                'units' => 5
            ],

            [
                'code' => 'CA 1',
                'title' => 'Institutional Corrections',
                'units' => 3
            ],

            [
                'code' => 'CLJ 3',
                'title' => 'Criminal Law (Book 1)',
                'units' => 3
            ],

            [
                'code' => 'CDI 4',
                'title' => 'Specialized Crime Investigation 2 with Simulation on Interrogation and Interview',
                'units' => 3
            ],

            [
                'code' => 'CDI 5',
                'title' => 'Technical English 1 (Investigative Report Writing and Presentation)',
                'units' => 3
            ],

            [
                'code' => 'Crim 5',
                'title' => 'Juvenile Delinquency and Juvenile Justice System',
                'units' => 3
            ],

            [
                'code' => 'Forensic 4',
                'title' => 'Questioned Documents Examination',
                'units' => 3
            ],

            [
                'code' => 'Crim 6',
                'title' => 'Dispute Resolution and Crises/Incidents Management',
                'units' => 3
            ],


            // THIRD YEAR - SECOND SEMESTER

            [
                'code' => 'Crim 7',
                'title' => 'Criminological Research 1',
                'units' => 3
            ],

            [
                'code' => 'CA 2',
                'title' => 'Non-Institutional Corrections',
                'units' => 3
            ],

            [
                'code' => 'CLJ 4',
                'title' => 'Criminal Law (Book 2)',
                'units' => 4
            ],

            [
                'code' => 'CDI 7',
                'title' => 'Fire Protection and Arson Investigation',
                'units' => 3
            ],

            [
                'code' => 'CDI 8',
                'title' => 'Vice and Drug Education and Control',
                'units' => 3
            ],

            [
                'code' => 'CDI 6',
                'title' => 'Technical English 2 (Legal Forms)',
                'units' => 3
            ],

            [
                'code' => 'CDI 9',
                'title' => 'Introduction to Cybercrime and Environmental Laws and Protection',
                'units' => 3
            ],

            [
                'code' => 'Forensic 5',
                'title' => 'Lie Detection Techniques',
                'units' => 3
            ],

            [
                'code' => 'LEA 4',
                'title' => 'Law Enforcement Operations and Planning with Crime Mapping',
                'units' => 3
            ],


            // THIRD YEAR - SUMMER

            [
                'code' => 'Pre-Prac',
                'title' => 'Pre-Internship Training',
                'units' => 3
            ],

            [
                'code' => 'CLJ 5',
                'title' => 'Evidence',
                'units' => 3
            ],


            // FOURTH YEAR - FIRST SEMESTER

            [
                'code' => 'CA 3',
                'title' => 'Therapeutic Modalities',
                'units' => 2
            ],

            [
                'code' => 'Crim 8',
                'title' => 'Criminological Research 2',
                'units' => 3
            ],

            [
                'code' => 'Forensic 6',
                'title' => 'Forensic Ballistics',
                'units' => 3
            ],

            [
                'code' => 'CLJ 6',
                'title' => 'Criminal Procedure and Court Testimony',
                'units' => 3
            ],

            [
                'code' => 'CrimPrac 1',
                'title' => 'Practicum (OJT)',
                'units' => 3
            ],


            // FOURTH YEAR - SECOND SEMESTER

            [
                'code' => 'Enh',
                'title' => 'Enhancement',
                'units' => 3
            ],

            [
                'code' => 'CrimPrac 2',
                'title' => 'Practicum (OJT)',
                'units' => 3
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | CREATE / UPDATE SUBJECTS
        |--------------------------------------------------------------------------
        */

        foreach ($subjects as $subject) {

            Subject::updateOrCreate(
                [
                    'code' => $subject['code']
                ],
                [
                    'title' => $subject['title'],
                    'units' => $subject['units']
                ]
            );

        }


        $this->command->info(
            'All BSIT, BSBA Financial Management, BSBA Marketing Management, and BSBA Human Resource Management subjects seeded successfully.'
        );
    }
}