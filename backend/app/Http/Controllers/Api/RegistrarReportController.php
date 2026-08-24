<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Course;
use Illuminate\Http\Request;

class RegistrarReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Main Report
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Enrollment Statistics
        |--------------------------------------------------------------------------
        */

        $totalEnrollments = Enrollment::count();

        $pending = Enrollment::where(
            'status',
            'Pending'
        )->count();

        $approved = Enrollment::where(
            'status',
            'Approved'
        )->count();

        $enrolled = Enrollment::where(
            'status',
            'Enrolled'
        )->count();

        $rejected = Enrollment::where(
            'status',
            'Rejected'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Payment Statistics
        |--------------------------------------------------------------------------
        */

        $paid = Payment::where(
            'status',
            'Paid'
        )->count();

        $unpaid = Payment::where(
            'status',
            'Pending'
        )->count();

        $failed = Payment::where(
            'status',
            'Failed'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Course Report
        |--------------------------------------------------------------------------
        */

        $courses = Course::withCount([
            'enrollments'
        ])
        ->orderBy(
            'enrollments_count',
            'desc'
        )
        ->get();


        /*
        |--------------------------------------------------------------------------
        | Recent Enrollments
        |--------------------------------------------------------------------------
        */

        $recentEnrollments = Enrollment::with([
            'student',
            'course',
            'curriculum',
            'schoolYear',
            'semester'
        ])
        ->latest()
        ->take(50)
        ->get();


        /*
        |--------------------------------------------------------------------------
        | Return Report
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'statistics' => [

                'total' => $totalEnrollments,

                'pending' => $pending,

                'approved' => $approved,

                'enrolled' => $enrolled,

                'rejected' => $rejected,

            ],


            'payments' => [

                'paid' => $paid,

                'unpaid' => $unpaid,

                'failed' => $failed,

            ],


            'courses' => $courses,


            'recent' => $recentEnrollments,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Enrollment Report
    |--------------------------------------------------------------------------
    */

    public function enrollmentReport()
    {
        $enrollments = Enrollment::with([
            'student',
            'course',
            'curriculum',
            'schoolYear',
            'semester'
        ])
        ->latest()
        ->get();


        return response()->json([
            'report' => 'Enrollment Report',
            'generated_at' => now(),
            'total' => $enrollments->count(),
            'data' => $enrollments
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Course Report
    |--------------------------------------------------------------------------
    */

    public function courseReport()
    {
        $courses = Course::withCount([
            'enrollments'
        ])
        ->orderBy(
            'enrollments_count',
            'desc'
        )
        ->get();


        return response()->json([
            'report' => 'Course Report',
            'generated_at' => now(),
            'data' => $courses
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Assessment / Payment Report
    |--------------------------------------------------------------------------
    */

    public function assessmentReport()
    {
        $payments = Payment::with([
            'enrollment.student',
            'enrollment.course'
        ])
        ->latest()
        ->get();


        return response()->json([
            'report' => 'Assessment Report',
            'generated_at' => now(),
            'total' => $payments->count(),
            'paid' => $payments->where('status', 'Paid')->count(),
            'pending' => $payments->where('status', 'Pending')->count(),
            'failed' => $payments->where('status', 'Failed')->count(),
            'data' => $payments
        ]);
    }
}