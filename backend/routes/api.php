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
    | Student Dashboard Routes
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
    | Academic Routes
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/enrollment/options',
        [EnrollmentOptionController::class, 'index']
    );


    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
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
    | PayMongo Webhook
    |--------------------------------------------------------------------------
    |
    | No authentication because PayMongo calls this endpoint.
    |
    */

    Route::post(
        '/paymongo/webhook',
        [PayMongoWebhookController::class, 'handle']
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
        | Student Profile
        |--------------------------------------------------------------------------
        */

        Route::prefix('student')->group(function () {


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


            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/payment/confirm',
                [PaymentController::class, 'confirmPayment']
            );


            Route::get(
                '/payment/info',
                [PaymentController::class, 'paymentInfo']
            );


            Route::post(
                '/payment/create/{id}',
                [PaymentController::class, 'createCheckout']
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
        | Assessment
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/assessment/create/{id}',
            [AssessmentController::class, 'create']
        );


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

            // List enrollments
            Route::get(
                '/enrollments',
                [RegistrarController::class, 'index']
            );


            // View enrollment
            Route::get(
                '/enrollment/{id}',
                [RegistrarController::class, 'show']
            );


            // Approve enrollment
            Route::put(
                '/enrollment/{id}/approve',
                [RegistrarController::class, 'approve']
            );


            // Reject enrollment
            Route::put(
                '/enrollment/{id}/reject',
                [RegistrarController::class, 'reject']
            );


            /*
            |--------------------------------------------------------------------------
            | Curriculum Management
            |--------------------------------------------------------------------------
            */

            // Get all courses
            Route::get(
                '/curriculum/courses',
                [CurriculumController::class, 'courses']
            );


            // Get all subjects
            Route::get(
                '/curriculum/subjects/all',
                [CurriculumController::class, 'allSubjects']
            );


            // Get curricula
            Route::get(
                '/curriculum',
                [CurriculumController::class, 'index']
            );


            // Get one curriculum
            Route::get(
                '/curriculum/{id}',
                [CurriculumController::class, 'show']
            );


            // Get subjects assigned to a curriculum
            Route::get(
                '/curriculum/{id}/subjects',
                [CurriculumController::class, 'subjects']
            );


            // Add subject to curriculum
            Route::post(
                '/curriculum/{id}/subjects',
                [CurriculumController::class, 'addSubject']
            );


            // Remove subject from curriculum
            Route::delete(
                '/curriculum-subject/{id}',
                [CurriculumController::class, 'removeSubject']
            );

            Route::get(
                '/reports',
                [RegistrarReportController::class, 'index']
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

        // Get all academic years
        Route::get(
            '/academic-years',
            [SchoolYearController::class, 'index']
        );


        // Get active academic year
        Route::get(
            '/academic-years/active',
            [SchoolYearController::class, 'active']
        );


        // Activate academic year
        Route::post(
            '/academic-years/{id}/activate',
            [SchoolYearController::class, 'activate']
        );


        // Get all semesters
        Route::get(
            '/semesters',
            [SemesterController::class, 'index']
        );


        // Get active semester
        Route::get(
            '/semesters/active',
            [SemesterController::class, 'active']
        );


        // Activate semester
        Route::post(
            '/semesters/{id}/activate',
            [SemesterController::class, 'activate']
        );


        // Get current active academic period
        Route::get(
            '/active-period',
            [AcademicPeriodController::class, 'active']
        );


        // Set active academic year + semester
        Route::post(
            '/active-period',
            [AcademicPeriodController::class, 'setActive']
        );



        });

        /*
    |--------------------------------------------------------------------------
    | Cashier Dashboard Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')
        ->prefix('cashier')
        ->group(function () {

            Route::get(
                '/dashboard',
                [CashierController::class, 'dashboard']
            );

            Route::get(
                '/payments',
                [CashierController::class, 'payments']
            );

            Route::get(
                '/payments/{id}',
                [CashierController::class, 'showPayment']
            );

            Route::get(
                '/receipts',
                [CashierController::class, 'receipts']
            );

            Route::get(
                '/receipts/{id}',
                [CashierController::class, 'receipt']
            );
            Route::get(
                '/reports',
                [CashierController::class, 'reports']
            );
        });

    });