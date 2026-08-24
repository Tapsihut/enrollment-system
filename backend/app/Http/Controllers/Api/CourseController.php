<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get Courses
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $courses = Course::orderBy('name')
            ->get();

        return response()->json(
            $courses
        );
    }
}