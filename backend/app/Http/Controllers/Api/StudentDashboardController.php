<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Payment;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Get authenticated user
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Default dashboard values
        |--------------------------------------------------------------------------
        */

        $enrollmentFee = 1500.00;

        $steps = [
            'profile' => false,
            'enrollment' => false,
            'payment' => false,
            'processing' => false,
            'completed' => false,
        ];

        /*
        |--------------------------------------------------------------------------
        | Check student profile
        |--------------------------------------------------------------------------
        */

        $student = $user?->student;

        if (!$student) {
            return response()->json([
                'enrollment_status' => 'No Enrollment',
                'enrollment_fee' => $enrollmentFee,
                'payment_status' => 'Not Available',
                'progress' => 0,
                'steps' => $steps,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine whether profile is complete
        |--------------------------------------------------------------------------
        */

        $profileFields = [
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

        $profileComplete = true;

        foreach ($profileFields as $field) {
            if (empty($student->{$field})) {
                $profileComplete = false;
                break;
            }
        }

        $steps['profile'] = $profileComplete;

        /*
        |--------------------------------------------------------------------------
        | Initial progress
        |--------------------------------------------------------------------------
        */

        $progress = 0;

        if ($profileComplete) {
            $progress = 20;
        }

        /*
        |--------------------------------------------------------------------------
        | Get latest enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment = Enrollment::where(
            'student_id',
            $student->id
        )
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | No enrollment
        |--------------------------------------------------------------------------
        */

        if (!$enrollment) {
            return response()->json([
                'enrollment_status' => 'No Enrollment',
                'enrollment_fee' => $enrollmentFee,
                'payment_status' => 'Not Available',
                'progress' => $progress,
                'steps' => $steps,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Enrollment has been submitted
        |--------------------------------------------------------------------------
        */

        $steps['enrollment'] = true;

        $progress = max($progress, 40);

        /*
        |--------------------------------------------------------------------------
        | Get payment
        |--------------------------------------------------------------------------
        |
        | Prefer a Paid payment in case there are multiple payment attempts.
        |
        */

        $payment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
            ->orderByRaw("
                CASE
                    WHEN status = 'Paid' THEN 0
                    ELSE 1
                END
            ")
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Payment status
        |--------------------------------------------------------------------------
        */

        $paymentStatus = 'Pending';

        if ($payment) {
            $paymentStatus = $payment->status;
        }

        /*
        |--------------------------------------------------------------------------
        | Student-facing enrollment status
        |--------------------------------------------------------------------------
        |
        | Database:
        |
        | Pending
        | Paid
        | Processing
        | Completed
        | Rejected
        |
        | Dashboard:
        |
        | Pending
        | Enrolled
        | Processing
        | Completed
        | Rejected
        |
        */

        $displayStatus = $enrollment->status;

        /*
        |--------------------------------------------------------------------------
        | Enrollment workflow
        |--------------------------------------------------------------------------
        */

        switch ($enrollment->status) {

            /*
            |--------------------------------------------------------------------------
            | PENDING
            |--------------------------------------------------------------------------
            |
            | Enrollment submitted but payment has not been completed.
            |
            */

            case 'Pending':

                $displayStatus = 'Pending';

                $steps['payment'] = false;
                $steps['processing'] = false;
                $steps['completed'] = false;

                $progress = 40;

                $enrollmentFee = 1500.00;

                break;


            /*
            |--------------------------------------------------------------------------
            | PAID
            |--------------------------------------------------------------------------
            |
            | Payment has been successfully completed.
            |
            | Student dashboard displays this as:
            |
            | ENROLLED
            |
            */

            case 'Paid':

                $displayStatus = 'Enrolled';

                $steps['payment'] = true;
                $steps['processing'] = false;
                $steps['completed'] = false;

                $progress = 60;

                $enrollmentFee = 0;

                break;


            /*
            |--------------------------------------------------------------------------
            | PROCESSING
            |--------------------------------------------------------------------------
            |
            | College/registrar is processing the enrollment.
            |
            */

            case 'Processing':

                $displayStatus = 'Processing';

                $steps['payment'] = true;
                $steps['processing'] = true;
                $steps['completed'] = false;

                $progress = 80;

                $enrollmentFee = 0;

                break;


            /*
            |--------------------------------------------------------------------------
            | COMPLETED
            |--------------------------------------------------------------------------
            |
            | Study load and payment receipt have been completed/sent.
            |
            */

            case 'Completed':

                $displayStatus = 'Completed';

                $steps['profile'] = true;
                $steps['enrollment'] = true;
                $steps['payment'] = true;
                $steps['processing'] = true;
                $steps['completed'] = true;

                $progress = 100;

                $enrollmentFee = 0;

                break;


            /*
            |--------------------------------------------------------------------------
            | REJECTED
            |--------------------------------------------------------------------------
            |
            | Keep payment state visible if payment was already completed.
            |
            */

            case 'Rejected':

                $displayStatus = 'Rejected';

                if ($paymentStatus === 'Paid') {

                    $steps['payment'] = true;

                    $progress = 60;

                    $enrollmentFee = 0;

                } else {

                    $steps['payment'] = false;

                    $progress = 40;

                    $enrollmentFee = 1500.00;
                }

                $steps['processing'] = false;
                $steps['completed'] = false;

                break;


            /*
            |--------------------------------------------------------------------------
            | Unknown status
            |--------------------------------------------------------------------------
            */

            default:

                $displayStatus = $enrollment->status;

                if ($paymentStatus === 'Paid') {

                    $steps['payment'] = true;

                    $progress = 60;

                    $enrollmentFee = 0;

                } else {

                    $progress = 40;

                    $enrollmentFee = 1500.00;
                }

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Always synchronize payment state
        |--------------------------------------------------------------------------
        |
        | If PayMongo says the payment is Paid, make sure the payment
        | step is completed regardless of other enrollment information.
        |
        */

        if ($paymentStatus === 'Paid') {

            $steps['payment'] = true;

            $enrollmentFee = 0;

            /*
            | If enrollment is still Paid in the database,
            | the student-facing status remains Enrolled.
            */

            if ($enrollment->status === 'Paid') {

                $displayStatus = 'Enrolled';

                if ($progress < 60) {
                    $progress = 60;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Return dashboard data
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'enrollment_status' => $displayStatus,
            'enrollment_fee' => $enrollmentFee,
            'payment_status' => $paymentStatus,
            'progress' => $progress,
            'steps' => $steps,
        ]);
    }
}
