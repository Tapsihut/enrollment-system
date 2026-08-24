<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class CashierController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Cashier Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $totalPayments = Payment::count();

        $paidPayments = Payment::where(
            'status',
            'Paid'
        )->count();

        $pendingPayments = Payment::where(
            'status',
            'Pending'
        )->count();

        $failedPayments = Payment::where(
            'status',
            'Failed'
        )->count();

        $todayCollection = Payment::where(
            'status',
            'Paid'
        )
        ->whereDate(
            'created_at',
            today()
        )
        ->sum('amount');

        $totalCollection = Payment::where(
            'status',
            'Paid'
        )
        ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Recent Payments
        |--------------------------------------------------------------------------
        */

        $recentPayments = Payment::with([
            'enrollment.student',
            'enrollment.course',
            'enrollment.curriculum',
            'enrollment.schoolYear',
            'enrollment.semester'
        ])
        ->latest()
        ->take(10)
        ->get();

        return response()->json([
            'statistics' => [
                'total_payments' => $totalPayments,
                'paid' => $paidPayments,
                'pending' => $pendingPayments,
                'failed' => $failedPayments,
                'today_collection' => $todayCollection,
                'total_collection' => $totalCollection,
            ],

            'recent_payments' => $recentPayments,

            'updated_at' => now()->format('h:i:s A')
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

public function payments(Request $request)
{
    $query = Payment::with([
        'enrollment.student',
        'enrollment.course',
        'enrollment.schoolYear',
        'enrollment.semester',
        'enrollment.curriculum'
    ]);

    if ($request->filled('search')) {

        $search = $request->search;

        $query->whereHas('enrollment.student', function ($q) use ($search) {

            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('student_number', 'like', "%{$search}%");

        });

    }

    if ($request->filled('status')) {

        $query->where(
            'status',
            $request->status
        );

    }

    if ($request->filled('payment_method')) {

        $query->where(
            'payment_method',
            $request->payment_method
        );

    }

    $payments = $query
        ->latest()
        ->paginate(
            $request->get('per_page', 10)
        );

    /*
    |--------------------------------------------------------------------------
    | Format Payment Data
    |--------------------------------------------------------------------------
    */

    $payments->getCollection()->transform(function ($payment) {

        $student = $payment->enrollment?->student;
        $course = $payment->enrollment?->course;
        $schoolYear = $payment->enrollment?->schoolYear;
        $semester = $payment->enrollment?->semester;
        $curriculum = $payment->enrollment?->curriculum;

        return [

            'id' =>
                $payment->id,

            'enrollment_id' =>
                $payment->enrollment_id,

            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            'student_name' =>
                $student
                    ? trim(
                        ($student->first_name ?? '') . ' ' .
                        ($student->middle_name ?? '') . ' ' .
                        ($student->last_name ?? '')
                    )
                    : null,

            'student_number' =>
                $student?->student_number,

            /*
            |--------------------------------------------------------------------------
            | Course
            |--------------------------------------------------------------------------
            */

            'course' =>
                $course?->code
                ?? $course?->course_code
                ?? $course?->name
                ?? $course?->course_name,

            /*
            |--------------------------------------------------------------------------
            | School Year
            |--------------------------------------------------------------------------
            */

            'year' =>
                $schoolYear?->year
                ?? $schoolYear?->name
                ?? $schoolYear?->school_year,

            /*
            |--------------------------------------------------------------------------
            | Semester
            |--------------------------------------------------------------------------
            */

            'semester' =>
                $semester?->name
                ?? $semester?->semester,

            /*
            |--------------------------------------------------------------------------
            | Curriculum
            |--------------------------------------------------------------------------
            */

            'curriculum' =>
                $curriculum?->name
                ?? $curriculum?->year
                ?? $curriculum?->curriculum_name,

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            'amount' =>
                $payment->amount,

            'payment_method' =>
                $payment->payment_method,

            'payment_reference' =>
                $payment->payment_reference
                ?? $payment->reference_number,

            'status' =>
                $payment->status,

            'created_at' =>
                $payment->created_at,

        ];

    });

    return response()->json($payments);
}

    /*
    |--------------------------------------------------------------------------
    | Payment Details
    |--------------------------------------------------------------------------
    */

    public function showPayment($id)
    {
        $payment = Payment::with([
            'enrollment.student',
            'enrollment.course',
            'enrollment.curriculum',
            'enrollment.schoolYear',
            'enrollment.semester'
        ])
        ->findOrFail($id);

        return response()->json([
            'payment' => $payment
        ]);
    }

    public function receipts(Request $request)
{
    $query = Payment::with([
        'enrollment.student',
        'enrollment.course',
        'enrollment.schoolYear',
        'enrollment.semester',
        'enrollment.curriculum'
    ])
    ->where('status', 'Paid');

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where(
                'payment_reference',
                'like',
                "%{$search}%"
            )

            ->orWhere(
                'reference_number',
                'like',
                "%{$search}%"
            )

            ->orWhereHas(
                'enrollment.student',
                function ($student) use ($search) {

                    $student
                        ->where(
                            'first_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'last_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'student_number',
                            'like',
                            "%{$search}%"
                        );

                }
            );

        });

    }

    /*
    |--------------------------------------------------------------------------
    | DATE FILTER
    |--------------------------------------------------------------------------
    */

    if ($request->filled('date')) {

        $query->whereDate(
            'created_at',
            $request->date
        );

    }

    /*
    |--------------------------------------------------------------------------
    | LATEST FIRST
    |--------------------------------------------------------------------------
    */

    $query->latest();

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $receipts = $query->paginate(
        $request->get('per_page', 10)
    );

    /*
    |--------------------------------------------------------------------------
    | FORMAT DATA
    |--------------------------------------------------------------------------
    */

    $receipts->getCollection()->transform(
        function ($payment) {

            $student =
                $payment->enrollment?->student;

            $course =
                $payment->enrollment?->course;

            $schoolYear =
                $payment->enrollment?->schoolYear;

            $semester =
                $payment->enrollment?->semester;

            $curriculum =
                $payment->enrollment?->curriculum;


            return [

                'id' =>
                    $payment->id,

                'enrollment_id' =>
                    $payment->enrollment_id,


                /*
                |--------------------------------------------------------------------------
                | Student
                |--------------------------------------------------------------------------
                */

                'student_name' =>
                    $student
                        ? trim(
                            ($student->first_name ?? '') .
                            ' ' .
                            ($student->middle_name ?? '') .
                            ' ' .
                            ($student->last_name ?? '')
                        )
                        : null,

                'student_number' =>
                    $student?->student_number,


                /*
                |--------------------------------------------------------------------------
                | Course
                |--------------------------------------------------------------------------
                */

                'course' =>
                    $course?->code
                    ?? $course?->course_code
                    ?? $course?->name
                    ?? $course?->course_name,


                /*
                |--------------------------------------------------------------------------
                | School Year
                |--------------------------------------------------------------------------
                */

                'year' =>
                    $schoolYear?->year
                    ?? $schoolYear?->name
                    ?? $schoolYear?->school_year,


                /*
                |--------------------------------------------------------------------------
                | Semester
                |--------------------------------------------------------------------------
                */

                'semester' =>
                    $semester?->name
                    ?? $semester?->semester,


                /*
                |--------------------------------------------------------------------------
                | Curriculum
                |--------------------------------------------------------------------------
                */

                'curriculum' =>
                    $curriculum?->name
                    ?? $curriculum?->year
                    ?? $curriculum?->curriculum_name,


                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                'amount' =>
                    $payment->amount,

                'payment_method' =>
                    $payment->payment_method,

                'payment_reference' =>
                    $payment->payment_reference
                    ?? $payment->reference_number,

                'status' =>
                    $payment->status,

                'created_at' =>
                    $payment->created_at

            ];

        }
    );

    return response()->json(
        $receipts
    );
}


    /*
    |--------------------------------------------------------------------------
    | Receipt
    |--------------------------------------------------------------------------
    */

    public function receipt($id)
    {
        $payment = Payment::with([
            'enrollment.student',
            'enrollment.course',
            'enrollment.schoolYear',
            'enrollment.semester'
        ])
        ->findOrFail($id);

        return response()->json([
            'payment' => $payment
        ]);
    }

    public function reports(Request $request)
{
    $query = Payment::query();

    /*
    |--------------------------------------------------------------------------
    | DATE FILTER
    |--------------------------------------------------------------------------
    */

    $from = $request->get('from');
    $to = $request->get('to');

    if ($from) {
        $query->whereDate('created_at', '>=', $from);
    }

    if ($to) {
        $query->whereDate('created_at', '<=', $to);
    }


    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    $totalPayments = (clone $query)->count();

    $paidPayments = (clone $query)
        ->where('status', 'Paid')
        ->count();

    $pendingPayments = (clone $query)
        ->where('status', 'Pending')
        ->count();

    $failedPayments = (clone $query)
        ->where('status', 'Failed')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | COLLECTION
    |--------------------------------------------------------------------------
    */

    $totalCollection = (clone $query)
        ->where('status', 'Paid')
        ->sum('amount');


    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHOD
    |--------------------------------------------------------------------------
    */

    $paymentMethods = (clone $query)
        ->where('status', 'Paid')
        ->selectRaw(
            'COALESCE(payment_method, "Unknown") as method,
             COUNT(*) as transactions,
             SUM(amount) as total'
        )
        ->groupBy('payment_method')
        ->orderByDesc('total')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | COURSE COLLECTION
    |--------------------------------------------------------------------------
    */

    $courseCollection = Payment::query()
        ->with([
            'enrollment.course'
        ])
        ->where('status', 'Paid')
        ->when(
            $from,
            function ($q) use ($from) {
                $q->whereDate(
                    'created_at',
                    '>=',
                    $from
                );
            }
        )
        ->when(
            $to,
            function ($q) use ($to) {
                $q->whereDate(
                    'created_at',
                    '<=',
                    $to
                );
            }
        )
        ->get()
        ->groupBy(function ($payment) {

            return optional(
                $payment->enrollment
            )->course->name
                ?? optional(
                    $payment->enrollment
                )->course->course_name
                ?? 'Unknown Course';

        })
        ->map(function ($payments, $course) {

            return [
                'course' => $course,
                'transactions' => $payments->count(),
                'total' => $payments->sum('amount')
            ];

        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | RECENT PAYMENTS
    |--------------------------------------------------------------------------
    */

    $recentPayments = Payment::with([
        'enrollment.student',
        'enrollment.course',
        'enrollment.schoolYear'
    ])
    ->when(
        $from,
        function ($q) use ($from) {
            $q->whereDate(
                'created_at',
                '>=',
                $from
            );
        }
    )
    ->when(
        $to,
        function ($q) use ($to) {
            $q->whereDate(
                'created_at',
                '<=',
                $to
            );
        }
    )
    ->latest()
    ->take(10)
    ->get();


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'summary' => [

            'total_payments' =>
                $totalPayments,

            'paid' =>
                $paidPayments,

            'pending' =>
                $pendingPayments,

            'failed' =>
                $failedPayments,

            'total_collection' =>
                $totalCollection

        ],

        'payment_methods' =>
            $paymentMethods,

        'course_collection' =>
            $courseCollection,

        'recent_payments' =>
            $recentPayments

    ]);
}
}