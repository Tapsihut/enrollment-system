<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentProfileRequest;
use App\Models\Student;

class StudentProfileController extends Controller
{
    private function requiredProfileFields()
    {
        return [
            'first_name',
            'last_name',
            'birth_date',
            'gender',
            'civil_status',
            'nationality',
            'contact_number',
            'email',
            'address',
        ];
    }

    private function profileCompletion($student)
    {
        if (!$student) {
            return [
                'complete' => false,
                'percentage' => 0,
                'missing' => ['student_profile']
            ];
        }

        $requiredFields = $this->requiredProfileFields();

        $missing = [];

        foreach ($requiredFields as $field) {
            if (blank($student->{$field})) {
                $missing[] = $field;
            }
        }

        $completed = count($requiredFields) - count($missing);

        $percentage = round(
            ($completed / count($requiredFields)) * 100
        );

        return [
            'complete' => empty($missing),
            'percentage' => $percentage,
            'missing' => $missing
        ];
    }

    public function store(StoreStudentProfileRequest $request)
    {
        $student = Student::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json([
            'message' => 'Profile saved successfully.',
            'student' => $student,
            'profile_complete' => $this->profileCompletion($student)
        ]);
    }

    public function update(StoreStudentProfileRequest $request)
    {
        $student = Student::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'student' => $student,
            'profile_complete' => $this->profileCompletion($student)
        ]);
    }

    public function show()
    {
        $student = auth()->user()->student;

        $documentsAvailable = false;
        $enrollmentCompleted = false;
        $enrollment = null;

        if ($student) {
            $enrollment = $student
                ->enrollments()
                ->latest()
                ->first();

            if ($enrollment && $enrollment->status === 'Completed') {
                $documentsAvailable = true;
                $enrollmentCompleted = true;
            }
        }

        return response()->json([
            'student' => $student,
            'profile_complete' => $this->profileCompletion($student),
            'documents_available' => $documentsAvailable,
            'enrollment_completed' => $enrollmentCompleted,
            'enrollment' => $enrollment
        ]);
    }

    public function completion()
    {
        $student = auth()->user()->student;

        return response()->json(
            $this->profileCompletion($student)
        );
    }
}