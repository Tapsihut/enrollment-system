<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\AcademicBackground;
use App\Models\CurriculumSubject;
use App\Models\EnrollmentSubject;

class RegistrarController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with([
            'student',
            'course',
            'curriculum',
            'schoolYear',
            'semester',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name,' ',last_name) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        if ($request->filled('course')) {
            $course = $request->course;
            $query->whereHas('course', function ($q) use ($course) {
                $q->where('name', $course);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $perPage = $request->get('per_page', 10);

        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        $enrollment = Enrollment::with([
            'student',
            'guardian',
            'course',
            'curriculum',
            'schoolYear',
            'semester',
            'academicBackground',
            'documents'
        ])->findOrFail($id);

        $enrollmentSubjects = EnrollmentSubject::with(['subject'])
            ->where('enrollment_id', $enrollment->id)
            ->get();

        return response()->json([
            'id' => $enrollment->id,
            'status' => $enrollment->status,
            'created_at' => $enrollment->created_at,
            'rejection_reason' => $enrollment->rejection_reason,
            'rejected_at' => $enrollment->rejected_at,
            'student' => $enrollment->student,
            'guardian' => $enrollment->guardian,
            'course' => $enrollment->course,
            'curriculum' => $enrollment->curriculum,
            'schoolYear' => $enrollment->schoolYear,
            'semester' => $enrollment->semester,
            'academicBackground' => $enrollment->academicBackground,
            'documents' => $enrollment->documents,
            'year_level' => $enrollment->year_level,
            'enrollmentSubjects' => $enrollmentSubjects
        ]);
    }

    public function approve($id)
    {
        $enrollment = Enrollment::with('semester')->findOrFail($id);

        if ($enrollment->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending enrollment applications can be approved.'
            ], 422);
        }

        $enrollment->status = 'Approved';
        $enrollment->rejection_reason = null;
        $enrollment->rejected_at = null;
        $enrollment->save();

        $semester = $enrollment->semester_id;

        $subjects = CurriculumSubject::where(
            'curriculum_id',
            $enrollment->curriculum_id
        )->where(
            'year_level',
            $enrollment->year_level
        )->where(
            'semester',
            $semester
        )->get();

        foreach ($subjects as $subject) {
            EnrollmentSubject::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'subject_id' => $subject->subject_id
                ],
                [
                    'units' => $subject->subject->units
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Enrollment approved and subjects assigned successfully.',
            'subjects_assigned' => $subjects->count()
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:5000',
        ], [
            'rejection_reason.required' =>
                'Please provide a reason for rejecting this enrollment.',
            'rejection_reason.min' =>
                'The rejection reason must be at least 5 characters.',
            'rejection_reason.max' =>
                'The rejection reason cannot exceed 5000 characters.',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending enrollment applications can be rejected.'
            ], 422);
        }

        $enrollment->status = 'Rejected';
        $enrollment->rejection_reason = $request->rejection_reason;
        $enrollment->rejected_at = now();
        $enrollment->save();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment rejected successfully.',
            'enrollment' => $enrollment
        ]);
    }

    public function dashboard()
    {
        return response()->json([
            'statistics' => [
                'total' => Enrollment::count(),
                'pending' => Enrollment::where('status', 'Pending')->count(),
                'approved' => Enrollment::where('status', 'Approved')->count(),
                'rejected' => Enrollment::where('status', 'Rejected')->count(),
                'payment' => Enrollment::where('status', 'Approved')->count(),
                'today' => Enrollment::whereDate('created_at', today())->count(),
            ],
            'recent' => Enrollment::with([
                'student',
                'course'
            ])->latest()->take(5)->get(),
            'updated_at' => now()->format('h:i:s A')
        ]);
    }
}