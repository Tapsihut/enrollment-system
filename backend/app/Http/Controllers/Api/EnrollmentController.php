<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\Enrollment;
use App\Models\StudentDocument;
use App\Models\AcademicBackground;
use App\Models\EnrollmentDocumentRequirement;
use App\Models\Course;

class EnrollmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STORE ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        Log::info('Enrollment Request', [
            'user_id' => optional($request->user())->id,
            'student_type' => $request->student_type,
            'course_id' => $request->course_id,
            'curriculum_id' => $request->curriculum_id,
            'school_year_id' => $request->school_year_id,
            'semester_id' => $request->semester_id,
            'has_psa' => $request->hasFile('psa_birth_certificate'),
            'has_good_moral' => $request->hasFile('good_moral'),
            'has_academic' => $request->hasFile('academic_document'),
            'has_id_picture' => $request->hasFile('id_picture'),
        ]);

        $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Student Information
            |--------------------------------------------------------------------------
            */

            'student_type' => ['required','string','max:50'],
            'first_name' => ['required','string','max:100'],
            'middle_name' => ['nullable','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'birth_date' => ['required','date'],
            'gender' => ['required','string','max:50'],
            'civil_status' => ['required','string','max:50'],
            'nationality' => ['nullable','string','max:100'],
            'religion' => ['nullable','string','max:100'],
            'address' => ['required','string','max:1000'],
            'contact_number' => ['required','string','max:30'],
            'email' => ['required','email','max:255'],

            /*
            |--------------------------------------------------------------------------
            | Enrollment Details
            |--------------------------------------------------------------------------
            */

            'course_id' => [
                'required',
                'exists:courses,id',
            ],

            'curriculum_id' => [
                'required',
                'exists:curricula,id',
            ],

            'school_year_id' => [
                'required',
                'exists:school_years,id',
            ],

            'semester_id' => [
                'required',
                'exists:semesters,id',
            ],

            'year_level' => [
                'required',
                'string',
                'max:50',
            ],

            /*
            |--------------------------------------------------------------------------
            | Schedule Preference
            |--------------------------------------------------------------------------
            */

            'schedule_preference' => [
                'nullable',
                'in:Day Only,Night Only,Flexible (Day & Night)',
            ],

            /*
            |--------------------------------------------------------------------------
            | Academic Background
            |--------------------------------------------------------------------------
            */

            'school_id' => [
                'nullable',
                'integer',
                'exists:schools,id',
            ],

            'last_school' => [
                'nullable',
                'string',
                'max:255',
            ],

            'school_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'strand' => [
                'nullable',
                'string',
                'max:100',
            ],

            'graduation_year' => [
                'nullable',
                'digits:4',
            ],

            'gwa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'previous_course' => [
                'nullable',
                'string',
                'max:255',
            ],

            'units_earned' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'last_school_year' => [
                'nullable',
                'string',
                'max:20',
            ],

            'last_semester' => [
                'nullable',
                'string',
                'max:50',
            ],

            /*
            |--------------------------------------------------------------------------
            | Documents
            |--------------------------------------------------------------------------
            */

            'psa_birth_certificate' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'good_moral' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'academic_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'id_picture' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png',
                'max:3072',
            ],

            /*
            |--------------------------------------------------------------------------
            | Promissory Undertakings
            |--------------------------------------------------------------------------
            */

            'psa_birth_certificate_promissory' => [
                'nullable',
                'boolean',
            ],

            'psa_birth_certificate_promissory_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'good_moral_promissory' => [
                'nullable',
                'boolean',
            ],

            'good_moral_promissory_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'academic_document_promissory' => [
                'nullable',
                'boolean',
            ],

            'academic_document_promissory_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'id_picture_promissory' => [
                'nullable',
                'boolean',
            ],

            'id_picture_promissory_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::beginTransaction();

        $storedFiles = [];

        try {
            /*
            |--------------------------------------------------------------------------
            | AUTHENTICATED USER
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            if (!$user) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            /*
            |--------------------------------------------------------------------------
            | STUDENT
            |--------------------------------------------------------------------------
            */

            $student = Student::where(
                'user_id',
                $user->id
            )->first();

            if (!$student) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Your profile is incomplete.',
                    'redirect' => '/student/profile',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK ACTIVE ENROLLMENT
            |--------------------------------------------------------------------------
            */

            $existingEnrollment = Enrollment::where(
                'student_id',
                $student->id
            )
                ->whereIn('status', [
                    'Pending',
                    'Paid',
                    'Processing',
                ])
                ->latest()
                ->first();

            if ($existingEnrollment) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active enrollment.',
                    'status' => $existingEnrollment->status,
                    'enrollment_id' => $existingEnrollment->id,
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | COURSE
            |--------------------------------------------------------------------------
            */

            $course = Course::find($request->course_id);

            if (!$course) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Selected course was not found.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK SCHEDULE REQUIREMENT
            |--------------------------------------------------------------------------
            */

            $courseText = strtolower(
                trim(
                    ($course->name ?? '') . ' ' .
                    ($course->code ?? '')
                )
            );

            $scheduleCourses = [
                'entrep',
                'entrepreneurship',
                'bsba',
                'bsoa',
                'beed',
                'bsed',
            ];

            $scheduleRequired = collect(
                $scheduleCourses
            )->contains(function ($keyword) use ($courseText) {
                return str_contains(
                    $courseText,
                    $keyword
                );
            });

            if (
                $scheduleRequired &&
                !$request->schedule_preference
            ) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Please select your schedule preference.',
                    'field' => 'schedule_preference',
                ], 422);
            }

            if (!$scheduleRequired) {
                $request->merge([
                    'schedule_preference' => null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE STUDENT PROFILE
            |--------------------------------------------------------------------------
            */

            $student->update([
                'student_type' => $request->student_type,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'civil_status' => $request->civil_status,
                'nationality' => $request->nationality,
                'religion' => $request->religion,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
            ]);

            /*
            |--------------------------------------------------------------------------
            | GUARDIAN
            |--------------------------------------------------------------------------
            */

            Guardian::updateOrCreate(
                [
                    'student_id' => $student->id,
                ],
                [
                    'guardian_name' =>
                        $request->guardian_name,

                    'relationship' =>
                        $request->guardian_relationship,

                    'guardian_contact' =>
                        $request->guardian_contact,

                    'guardian_address' =>
                        $request->guardian_address,

                    'father_name' =>
                        $request->father_name,

                    'father_contact' =>
                        $request->father_contact,

                    'mother_name' =>
                        $request->mother_name,

                    'mother_contact' =>
                        $request->mother_contact,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | ACADEMIC BACKGROUND
            |--------------------------------------------------------------------------
            */

            AcademicBackground::updateOrCreate(
                [
                    'student_id' => $student->id,
                ],
                [
                    'school_id' =>
                        $request->school_id ?: null,

                    'last_school' =>
                        $request->last_school,

                    'school_address' =>
                        $request->school_address,

                    'strand' =>
                        $request->strand,

                    'graduation_year' =>
                        $request->graduation_year,

                    'gwa' =>
                        $request->gwa,

                    'previous_course' =>
                        $request->previous_course,

                    'units_earned' =>
                        $request->units_earned,

                    'last_school_year' =>
                        $request->last_school_year,

                    'last_semester' =>
                        $request->last_semester,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | CREATE ENROLLMENT
            |--------------------------------------------------------------------------
            */

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $request->course_id,
                'curriculum_id' => $request->curriculum_id,
                'school_year_id' => $request->school_year_id,
                'semester_id' => $request->semester_id,
                'year_level' => $request->year_level,
                'schedule_preference' =>
                    $request->schedule_preference,
                'status' => 'Pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTS
            |--------------------------------------------------------------------------
            */

            $documents = [
                'psa_birth_certificate',
                'good_moral',
                'academic_document',
                'id_picture',
            ];

            foreach ($documents as $document) {
                $promissoryField =
                    $document . '_promissory';

                $reasonField =
                    $document . '_promissory_reason';

                /*
                |--------------------------------------------------------------------------
                | ACTUAL FILE
                |--------------------------------------------------------------------------
                */

                if ($request->hasFile($document)) {
                    $file = $request->file($document);

                    if (!$file->isValid()) {
                        throw new \RuntimeException(
                            "The uploaded {$document} file is invalid."
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Generate random filename
                    |--------------------------------------------------------------------------
                    */

                    $extension = strtolower(
                        $file->extension()
                    );

                    $filename =
                        Str::random(40) .
                        '.' .
                        $extension;

                    /*
                    |--------------------------------------------------------------------------
                    | PRIVATE STORAGE
                    |--------------------------------------------------------------------------
                    */

                    $directory =
                        'enrollment-documents/' .
                        $student->id .
                        '/' .
                        $enrollment->id;

                    $path = $file->storeAs(
                        $directory,
                        $filename,
                        'local'
                    );

                    if (!$path) {
                        throw new \RuntimeException(
                            "Unable to store {$document}."
                        );
                    }

                    $storedFiles[] = $path;

                    /*
                    |--------------------------------------------------------------------------
                    | Save document metadata
                    |--------------------------------------------------------------------------
                    */

                    StudentDocument::create([
                        'student_id' => $student->id,
                        'document_type' => $document,
                        'file_name' =>
                            $file->getClientOriginalName(),
                        'file_path' => $path,
                        'status' => 'Pending',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Document requirement
                    |--------------------------------------------------------------------------
                    */

                    EnrollmentDocumentRequirement::updateOrCreate(
                        [
                            'enrollment_id' =>
                                $enrollment->id,

                            'document_type' =>
                                $document,
                        ],
                        [
                            'submission_type' =>
                                'Uploaded',

                            'promissory_reason' =>
                                null,

                            'status' =>
                                'Pending',

                            'remarks' =>
                                null,

                            'reviewed_at' =>
                                null,
                        ]
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | PROMISSORY UNDERTAKING
                |--------------------------------------------------------------------------
                */

                if (
                    $request->boolean(
                        $promissoryField
                    )
                ) {
                    $reason =
                        $request->input(
                            $reasonField
                        );

                    if (
                        !$reason ||
                        trim($reason) === ''
                    ) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' =>
                                "Please provide a reason for the {$document} promissory undertaking.",
                            'field' =>
                                $reasonField,
                        ], 422);
                    }

                    EnrollmentDocumentRequirement::updateOrCreate(
                        [
                            'enrollment_id' =>
                                $enrollment->id,

                            'document_type' =>
                                $document,
                        ],
                        [
                            'submission_type' =>
                                'Promissory',

                            'promissory_reason' =>
                                trim($reason),

                            'status' =>
                                'Pending',

                            'remarks' =>
                                null,

                            'reviewed_at' =>
                                null,
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | LOAD RELATIONSHIPS
            |--------------------------------------------------------------------------
            */

            $enrollment->load([
                'student',
                'course',
                'curriculum',
                'schoolYear',
                'semester',
                'payment',
                'documentRequirements',
            ]);

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,
                'message' =>
                    'Enrollment submitted successfully. Please proceed with payment.',
                'student' => $student,
                'enrollment' => $enrollment,
                'documents' => $student->documents,
                'document_requirements' =>
                    $enrollment->documentRequirements,
                'redirect' =>
                    '/student/payment',
            ], 201);

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK DATABASE
            |--------------------------------------------------------------------------
            */

            DB::rollBack();

            /*
            |--------------------------------------------------------------------------
            | REMOVE FILES THAT WERE ALREADY STORED
            |--------------------------------------------------------------------------
            */

            foreach ($storedFiles as $path) {
                try {
                    Storage::disk('local')->delete($path);
                } catch (\Throwable $fileError) {
                    Log::warning(
                        'Unable to remove enrollment file after rollback.',
                        [
                            'path' => $path,
                            'error' =>
                                $fileError->getMessage(),
                        ]
                    );
                }
            }

            Log::error(
                'Enrollment Error',
                [
                    'user_id' =>
                        optional($request->user())->id,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to submit enrollment. Please try again.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK CURRENT ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function checkCurrentEnrollment(
        Request $request
    ) {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'allowed' => false,
                'message' =>
                    'User not authenticated.',
            ], 401);
        }

        $student = Student::where(
            'user_id',
            $user->id
        )->first();

        if (!$student) {
            return response()->json([
                'allowed' => false,
                'message' =>
                    'Student record not found.',
            ], 404);
        }

        $existingEnrollment = Enrollment::where(
            'student_id',
            $student->id
        )
            ->whereIn('status', [
                'Pending',
                'Paid',
                'Processing',
            ])
            ->latest()
            ->first();

        if ($existingEnrollment) {
            $message = match(
                $existingEnrollment->status
            ) {
                'Pending' =>
                    'Your enrollment has been submitted. Please proceed with payment.',

                'Paid' =>
                    'Your enrollment payment has been received. Your enrollment is being processed.',

                'Processing' =>
                    'Your enrollment is currently being processed. Please allow 1–2 working days.',

                default =>
                    'You already have an active enrollment.',
            };

            return response()->json([
                'allowed' => false,
                'status' =>
                    $existingEnrollment->status,
                'message' => $message,
                'enrollment_id' =>
                    $existingEnrollment->id,
            ]);
        }

        return response()->json([
            'allowed' => true,
            'message' =>
                'You may proceed with enrollment.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function process($id)
    {
        try {
            $enrollment =
                Enrollment::findOrFail($id);

            if ($enrollment->status !== 'Paid') {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Only paid enrollments can be processed.',
                ], 422);
            }

            $enrollment->update([
                'status' => 'Processing',
            ]);

            $enrollment->load([
                'student',
                'course',
                'curriculum',
                'schoolYear',
                'semester',
                'payment',
                'documentRequirements',
            ]);

            return response()->json([
                'success' => true,
                'message' =>
                    'Enrollment is now being processed.',
                'enrollment' => $enrollment,
            ]);

        } catch (\Throwable $e) {
            Log::error(
                'Process Enrollment Error',
                [
                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to process enrollment.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        $id
    ) {
        $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'min:5',
                'max:5000',
            ],
        ], [
            'rejection_reason.required' =>
                'Please provide a reason for rejecting this enrollment.',

            'rejection_reason.min' =>
                'The rejection reason must be at least 5 characters.',

            'rejection_reason.max' =>
                'The rejection reason cannot exceed 5000 characters.',
        ]);

        try {
            $enrollment =
                Enrollment::findOrFail($id);

            if (!in_array(
                $enrollment->status,
                [
                    'Pending',
                    'Paid',
                    'Processing',
                ]
            )) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'This enrollment can no longer be rejected.',
                ], 422);
            }

            $enrollment->update([
                'status' => 'Rejected',
                'rejection_reason' =>
                    $request->rejection_reason,
                'rejected_at' => now(),
            ]);

            $enrollment->load([
                'student',
                'course',
                'curriculum',
                'schoolYear',
                'semester',
                'payment',
                'documentRequirements',
            ]);

            return response()->json([
                'success' => true,
                'message' =>
                    'Enrollment rejected successfully.',
                'enrollment' => $enrollment,
            ]);

        } catch (\Throwable $e) {
            Log::error(
                'Reject Enrollment Error',
                [
                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to reject enrollment.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function complete($id)
    {
        try {
            $enrollment =
                Enrollment::findOrFail($id);

            if (
                $enrollment->status !==
                'Processing'
            ) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Only processing enrollments can be completed.',
                ], 422);
            }

            $enrollment->update([
                'status' => 'Completed',
            ]);

            $enrollment->load([
                'student',
                'course',
                'curriculum',
                'schoolYear',
                'semester',
                'payment',
                'documentRequirements',
            ]);

            return response()->json([
                'success' => true,
                'message' =>
                    'Enrollment completed successfully.',
                'enrollment' => $enrollment,
            ]);

        } catch (\Throwable $e) {
            Log::error(
                'Complete Enrollment Error',
                [
                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to complete enrollment.',
            ], 500);
        }
    }
}