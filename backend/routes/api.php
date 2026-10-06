<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\RegistrarController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\SchoolYearController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\EnrollmentOptionController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\RegistrarReportController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\AcademicPeriodController;
use App\Http\Controllers\Api\EnrollmentDocumentRequirementController;
use App\Http\Controllers\SecurityBankWebhookController;


/*
|--------------------------------------------------------------------------
| Student Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        '/student/dashboard',
        [StudentDashboardController::class, 'index']
    );
});


/*
|--------------------------------------------------------------------------
| Enrollment Options
|--------------------------------------------------------------------------
*/

Route::get(
    '/enrollment/options',
    [EnrollmentOptionController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/login',
    [AuthController::class, 'login']
);


/*
|--------------------------------------------------------------------------
| School Directory
|--------------------------------------------------------------------------
|
| Used by the student enrollment form to search and select
| schools from the DepEd school directory.
|
| IMPORTANT:
| /schools/search must come BEFORE /schools/{id}
| so Laravel does not treat "search" as an ID.
|
*/

Route::get(
    '/schools/search',
    [SchoolController::class, 'search']
);

Route::get(
    '/schools/{id}',
    [SchoolController::class, 'show']
);


/*
|--------------------------------------------------------------------------
| PayMongo Webhook
|--------------------------------------------------------------------------
|
| PayMongo calls this endpoint directly.
| Do NOT protect this route with auth:sanctum.
|
*/

Route::post(
    '/paymongo/webhook',
    [PaymentController::class, 'webhook']
);


/*
|--------------------------------------------------------------------------
| Security Bank Webhook
|--------------------------------------------------------------------------
|
| Security Bank calls this endpoint directly.
| Do NOT protect this route with auth:sanctum.
|
*/

Route::post(
    '/security-bank/webhook',
    [SecurityBankWebhookController::class, 'handle']
);


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/me',
        [AuthController::class, 'me']
    );

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );


    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    Route::prefix('student')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [StudentProfileController::class, 'show']
        );

        Route::get(
            '/profile/completion',
            [StudentProfileController::class, 'completion']
        );

        Route::post(
            '/profile',
            [StudentProfileController::class, 'store']
        );

        Route::put(
            '/profile',
            [StudentProfileController::class, 'update']
        );


        /*
        |--------------------------------------------------------------------------
        | Enrollment
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/enrollment',
            [EnrollmentController::class, 'store']
        );

        Route::get(
            '/enrollment/check-current',
            [EnrollmentController::class, 'checkCurrentEnrollment']
        );


        /*
        |--------------------------------------------------------------------------
        | Payment
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/payment/info',
            [PaymentController::class, 'paymentInfo']
        );


        /*
        | PayMongo / GCash
        */

        Route::post(
            '/payment/create/{id}',
            [PaymentController::class, 'createCheckout']
        );


        /*
        | Security Bank QR
        */

        Route::post(
            '/payment/security-bank/{id}',
            [PaymentController::class, 'createSecurityBankQr']
        );


        /*
        | Kept for compatibility with existing frontend.
        */

        Route::post(
            '/payment/confirm',
            [PaymentController::class, 'confirmPayment']
        );


        /*
        |--------------------------------------------------------------------------
        | Receipt
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/receipt',
            [ReceiptController::class, 'show']
        );


        /*
        |--------------------------------------------------------------------------
        | Documents
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/documents/enrollment-form',
            [DocumentController::class, 'enrollmentForm']
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Registrar
    |--------------------------------------------------------------------------
    */

    Route::prefix('registrar')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [RegistrarController::class, 'dashboard']
        );


        /*
        |--------------------------------------------------------------------------
        | Enrollment Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/enrollments',
            [RegistrarController::class, 'index']
        );

        Route::get(
            '/enrollment/{id}',
            [RegistrarController::class, 'show']
        );


        /*
        | Paid → Processing
        */

        Route::put(
            '/enrollment/{id}/process',
            [RegistrarController::class, 'process']
        );


        /*
        | Processing → Completed
        */

        Route::put(
            '/enrollment/{id}/complete',
            [RegistrarController::class, 'complete']
        );


        /*
        | Pending / Paid / Processing → Rejected
        */

        Route::put(
            '/enrollment/{id}/reject',
            [RegistrarController::class, 'reject']
        );


        /*
        |--------------------------------------------------------------------------
        | Promissory Document Requirements
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/enrollment/{enrollmentId}/document-requirements',
            [
                EnrollmentDocumentRequirementController::class,
                'index'
            ]
        );

        Route::put(
            '/document-requirement/{id}/approve',
            [
                EnrollmentDocumentRequirementController::class,
                'approve'
            ]
        );

        Route::put(
            '/document-requirement/{id}/reject',
            [
                EnrollmentDocumentRequirementController::class,
                'reject'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [RegistrarReportController::class, 'index']
        );

        Route::get(
            '/reports/students',
            [RegistrarReportController::class, 'students']
        );

        Route::get(
            '/reports/enrollment',
            [RegistrarReportController::class, 'enrollmentReport']
        );

        Route::get(
            '/reports/course',
            [RegistrarReportController::class, 'courseReport']
        );

        Route::get(
            '/reports/assessment',
            [RegistrarReportController::class, 'assessmentReport']
        );


        /*
        |--------------------------------------------------------------------------
        | Academic Period Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/academic-years',
            [SchoolYearController::class, 'index']
        );

        Route::get(
            '/academic-years/active',
            [SchoolYearController::class, 'active']
        );

        Route::post(
            '/academic-years/{id}/activate',
            [SchoolYearController::class, 'activate']
        );


        /*
        |--------------------------------------------------------------------------
        | Semesters
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/semesters',
            [SemesterController::class, 'index']
        );

        Route::get(
            '/semesters/active',
            [SemesterController::class, 'active']
        );

        Route::post(
            '/semesters/{id}/activate',
            [SemesterController::class, 'activate']
        );


        /*
        |--------------------------------------------------------------------------
        | Active Academic Period
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/active-period',
            [AcademicPeriodController::class, 'active']
        );

        Route::post(
            '/active-period',
            [AcademicPeriodController::class, 'setActive']
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Cashier
    |--------------------------------------------------------------------------
    */

    Route::prefix('cashier')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [CashierController::class, 'dashboard']
        );


        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/payments',
            [CashierController::class, 'payments']
        );

        Route::get(
            '/payments/{id}',
            [CashierController::class, 'showPayment']
        );


        /*
        |--------------------------------------------------------------------------
        | Receipts
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/receipts',
            [CashierController::class, 'receipts']
        );

        Route::get(
            '/receipts/{id}',
            [CashierController::class, 'receipt']
        );


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [CashierController::class, 'reports']
        );

    });

});