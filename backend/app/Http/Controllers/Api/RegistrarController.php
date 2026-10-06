<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;

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

        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'desc';
        }

        $query->orderBy('created_at', $sort);

        $perPage = min((int) $request->get('per_page', 10), 100);

        return response()->json(
            $query->paginate($perPage)
        );
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
            'documents',
            'payment'
        ])->findOrFail($id);

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
            'payment' => $enrollment->payment
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Paid → Processing
    |--------------------------------------------------------------------------
    */

    public function process($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status !== 'Paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only paid enrollments can be processed.'
            ], 422);
        }

        $enrollment->status = 'Processing';
        $enrollment->rejection_reason = null;
        $enrollment->rejected_at = null;
        $enrollment->save();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment is now being processed.',
            'enrollment' => $enrollment
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pending / Paid / Processing → Rejected
    |--------------------------------------------------------------------------
    */

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

        if (!in_array($enrollment->status, [
            'Pending',
            'Paid',
            'Processing'
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'This enrollment cannot be rejected in its current status.'
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

    /*
    |--------------------------------------------------------------------------
    | Processing → Completed
    |--------------------------------------------------------------------------
    */

    public function complete($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status !== 'Processing') {
            return response()->json([
                'success' => false,
                'message' => 'Only enrollments currently being processed can be completed.'
            ], 422);
        }

        $enrollment->status = 'Completed';
        $enrollment->rejection_reason = null;
        $enrollment->rejected_at = null;
        $enrollment->save();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment completed successfully.',
            'enrollment' => $enrollment
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        return response()->json([
            'statistics' => [
                'total' => Enrollment::count(),

                'pending' => Enrollment::where(
                    'status',
                    'Pending'
                )->count(),

                'paid' => Enrollment::where(
                    'status',
                    'Paid'
                )->count(),

                'processing' => Enrollment::where(
                    'status',
                    'Processing'
                )->count(),

                'rejected' => Enrollment::where(
                    'status',
                    'Rejected'
                )->count(),

                'completed' => Enrollment::where(
                    'status',
                    'Completed'
                )->count(),

                'today' => Enrollment::whereDate(
                    'created_at',
                    today()
                )->count(),
            ],

            'recent' => Enrollment::with([
                'student',
                'course',
                'payment'
            ])
                ->latest()
                ->take(5)
                ->get(),

            'updated_at' => now()->format('h:i:s A')
        ]);
    }
}