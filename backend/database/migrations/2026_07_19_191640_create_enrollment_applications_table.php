<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('enrollment_applications', function (Blueprint $table) {

    $table->id();

    $table->foreignId('student_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('course_id')
        ->constrained();

    $table->foreignId('school_year_id')
        ->constrained();

    $table->foreignId('semester_id')
        ->constrained();

    $table->integer('year_level');

    $table->enum('status',[
        'Draft',
        'Submitted',
        'Approved',
        'Rejected',
        'Paid',
        'Enrolled'
    ])->default('Draft');

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_applications');
    }
};
