<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\RegistrarController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CurriculumController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\SchoolYearController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\PayMongoWebhookController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\EnrollmentOptionController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\RegistrarReportController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\AcademicPeriodController;

/*
|--------------------------------------------------------------------------
| Student Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function(){

    Route::get(
        '/student/dashboard',
        [StudentDashboardController::class,'index']
    );

});

/*
|--------------------------------------------------------------------------
| Academic Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/enrollment/options',
    [EnrollmentOptionController::class,'index']
);

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::post(
    '/register',
    [AuthController::class,'register']
);

Route::post(
    '/login',
    [AuthController::class,'login']
);

/*
|--------------------------------------------------------------------------
| PayMongo Webhook
|--------------------------------------------------------------------------
*/

Route::post(
    '/paymongo/webhook',
    [PayMongoWebhookController::class,'handle']
);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function(){

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/me',
        [AuthController::class,'me']
    );

    Route::post(
        '/logout',
        [AuthController::class,'logout']
    );

    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    Route::prefix('student')->group(function(){

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [StudentProfileController::class,'show']
        );

        Route::get(
            '/profile/completion',
            [StudentProfileController::class,'completion']
        );

        Route::post(
            '/profile',
            [StudentProfileController::class,'store']
        );

        Route::put(
            '/profile',
            [StudentProfileController::class,'update']
        );

        /*
        |--------------------------------------------------------------------------
        | Enrollment
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/enrollment',
            [EnrollmentController::class,'store']
        );

        Route::get(
            '/enrollment/check-current',
            [EnrollmentController::class,'checkCurrentEnrollment']
        );

        /*
        |--------------------------------------------------------------------------
        | Payment
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/payment/confirm',
            [PaymentController::class,'confirmPayment']
        );

        Route::get(
            '/payment/info',
            [PaymentController::class,'paymentInfo']
        );

        Route::post(
            '/payment/create/{id}',
            [PaymentController::class,'createCheckout']
        );

        /*
        |--------------------------------------------------------------------------
        | Receipt
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/receipt',
            [ReceiptController::class,'show']
        );

        /*
        |--------------------------------------------------------------------------
        | Documents
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/documents/enrollment-form',
            [DocumentController::class,'enrollmentForm']
        );

    });

    /*
    |--------------------------------------------------------------------------
    | Assessment
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/assessment/create/{id}',
        [AssessmentController::class,'create']
    );

    /*
    |--------------------------------------------------------------------------
    | Registrar
    |--------------------------------------------------------------------------
    */

    Route::prefix('registrar')->group(function(){

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [RegistrarController::class,'dashboard']
        );

        /*
        |--------------------------------------------------------------------------
        | Enrollment Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/enrollments',
            [RegistrarController::class,'index']
        );

        Route::get(
            '/enrollment/{id}',
            [RegistrarController::class,'show']
        );

        Route::put(
            '/enrollment/{id}/approve',
            [RegistrarController::class,'approve']
        );

        Route::put(
            '/enrollment/{id}/reject',
            [RegistrarController::class,'reject']
        );

        /*
        |--------------------------------------------------------------------------
        | Curriculum Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/curriculum/courses',
            [CurriculumController::class,'courses']
        );

        Route::get(
            '/curriculum/subjects/all',
            [CurriculumController::class,'allSubjects']
        );

        Route::get(
            '/curriculum',
            [CurriculumController::class,'index']
        );

        Route::get(
            '/curriculum/{id}',
            [CurriculumController::class,'show']
        );

        Route::get(
            '/curriculum/{id}/subjects',
            [CurriculumController::class,'subjects']
        );

        Route::post(
            '/curriculum/{id}/subjects',
            [CurriculumController::class,'addSubject']
        );

        Route::delete(
            '/curriculum-subject/{id}',
            [CurriculumController::class,'removeSubject']
        );

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [RegistrarReportController::class,'index']
        );

        // ADDED: Student Masterlist
        Route::get(
            '/reports/students',
            [RegistrarReportController::class,'students']
        );

        Route::get(
            '/reports/enrollment',
            [RegistrarReportController::class,'enrollmentReport']
        );

        Route::get(
            '/reports/course',
            [RegistrarReportController::class,'courseReport']
        );

        Route::get(
            '/reports/assessment',
            [RegistrarReportController::class,'assessmentReport']
        );

        /*
        |--------------------------------------------------------------------------
        | Academic Period Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/academic-years',
            [SchoolYearController::class,'index']
        );

        Route::get(
            '/academic-years/active',
            [SchoolYearController::class,'active']
        );

        Route::post(
            '/academic-years/{id}/activate',
            [SchoolYearController::class,'activate']
        );

        Route::get(
            '/semesters',
            [SemesterController::class,'index']
        );

        Route::get(
            '/semesters/active',
            [SemesterController::class,'active']
        );

        Route::post(
            '/semesters/{id}/activate',
            [SemesterController::class,'activate']
        );

        Route::get(
            '/active-period',
            [AcademicPeriodController::class,'active']
        );

        Route::post(
            '/active-period',
            [AcademicPeriodController::class,'setActive']
        );

    });

});

/*
|--------------------------------------------------------------------------
| Cashier
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->prefix('cashier')
    ->group(function(){

        Route::get(
            '/dashboard',
            [CashierController::class,'dashboard']
        );

        Route::get(
            '/payments',
            [CashierController::class,'payments']
        );

        Route::get(
            '/payments/{id}',
            [CashierController::class,'showPayment']
        );

        Route::get(
            '/receipts',
            [CashierController::class,'receipts']
        );

        Route::get(
            '/receipts/{id}',
            [CashierController::class,'receipt']
        );

        Route::get(
            '/reports',
            [CashierController::class,'reports']
        );

    });