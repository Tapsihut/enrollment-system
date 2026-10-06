<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Step 1: Temporarily allow both old and new statuses
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE enrollments
            MODIFY status ENUM(
                'Pending',
                'Approved',
                'Rejected',
                'Paid',
                'Enrolled',
                'Processing',
                'Completed'
            ) NOT NULL DEFAULT 'Pending'
        ");

        /*
        |--------------------------------------------------------------------------
        | Step 2: Convert old statuses
        |--------------------------------------------------------------------------
        */

        DB::table('enrollments')
            ->where('status', 'Approved')
            ->update([
                'status' => 'Paid'
            ]);

        DB::table('enrollments')
            ->where('status', 'Enrolled')
            ->update([
                'status' => 'Completed'
            ]);

        /*
        |--------------------------------------------------------------------------
        | Step 3: Remove old statuses
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE enrollments
            MODIFY status ENUM(
                'Pending',
                'Paid',
                'Processing',
                'Rejected',
                'Completed'
            ) NOT NULL DEFAULT 'Pending'
        ");
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Step 1: Temporarily allow both old and new statuses
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE enrollments
            MODIFY status ENUM(
                'Pending',
                'Approved',
                'Rejected',
                'Paid',
                'Enrolled',
                'Processing',
                'Completed'
            ) NOT NULL DEFAULT 'Pending'
        ");

        /*
        |--------------------------------------------------------------------------
        | Step 2: Convert new statuses back
        |--------------------------------------------------------------------------
        */

        DB::table('enrollments')
            ->where('status', 'Completed')
            ->update([
                'status' => 'Enrolled'
            ]);

        DB::table('enrollments')
            ->where('status', 'Processing')
            ->update([
                'status' => 'Approved'
            ]);

        /*
        |--------------------------------------------------------------------------
        | Step 3: Restore old enum
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE enrollments
            MODIFY status ENUM(
                'Pending',
                'Approved',
                'Rejected',
                'Paid',
                'Enrolled'
            ) NOT NULL DEFAULT 'Pending'
        ");
    }
};