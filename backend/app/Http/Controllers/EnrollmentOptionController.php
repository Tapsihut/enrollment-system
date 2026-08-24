<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Curriculum;

class EnrollmentOptionController extends Controller
{
    public function index()
    {
        return response()->json([

            // Only active school years
            'school_years' => SchoolYear::where('is_active', 1)
                ->orderBy('school_year', 'desc')
                ->get(),

            // Only active semesters
            'semesters' => Semester::where('is_active', 1)
                ->orderBy('name')
                ->get(),

            // Courses
            'courses' => Course::orderBy('name')
                ->get(),

            // Only active curricula
            'curricula' => Curriculum::where('active', 1)
                ->orderBy('name')
                ->get(),

        ]);
    }
}