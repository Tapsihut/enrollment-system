<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Assessment;

class RegistrarReportController extends Controller
{
    public function index()
    {
        $totalApplications = Enrollment::count();

        $pending = Enrollment::where('status', 'Pending')->count();
        $approved = Enrollment::where('status', 'Approved')->count();
        $enrolled = Enrollment::where('status', 'Enrolled')->count();
        $rejected = Enrollment::where('status', 'Rejected')->count();

        $paid = Payment::where('status', 'Paid')->count();
        $unpaid = Payment::where('status', 'Pending')->count();
        $failed = Payment::where('status', 'Failed')->count();

        $courseSummary = Enrollment::with('course')
            ->selectRaw('course_id, COUNT(*) as total')
            ->groupBy('course_id')
            ->get();

        $recentEnrollments = Enrollment::with([
            'student',
            'course',
            'schoolYear',
            'semester'
        ])
        ->latest()
        ->take(10)
        ->get();

        $paymentSummary = Payment::selectRaw(
            'status, COUNT(*) as total, SUM(amount) as amount'
        )
        ->groupBy('status')
        ->get();

        return response()->json([
            'statistics' => [
                'total' => $totalApplications,
                'pending' => $pending,
                'approved' => $approved,
                'enrolled' => $enrolled,
                'rejected' => $rejected
            ],

            'payments' => [
                'paid' => $paid,
                'unpaid' => $unpaid,
                'failed' => $failed
            ],

            'courses' => $courseSummary,

            'recent' => $recentEnrollments,

            'paymentSummary' => $paymentSummary
        ]);
    }

    public function students()
    {
        $students = Student::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'students' => $students
        ]);
    }

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
            'enrollments' => $enrollments
        ]);
    }

    public function courseReport()
    {
        $courses = Enrollment::with('course')
            ->selectRaw('course_id, COUNT(*) as total')
            ->groupBy('course_id')
            ->get();

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function assessmentReport()
    {
        $assessments = Assessment::with([
            'enrollment.student',
            'enrollment.course'
        ])
        ->latest()
        ->get();

        return response()->json([
            'assessments' => $assessments
        ]);
    }
}
