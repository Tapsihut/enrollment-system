<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {

            $table->enum('schedule_preference', [
                'Day Only',
                'Night Only',
                'Flexible (Day & Night)',
            ])
            ->nullable()
            ->after('year_level');

        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {

            $table->dropColumn('schedule_preference');

        });
    }
};