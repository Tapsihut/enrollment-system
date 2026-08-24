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
Schema::create('fees', function (Blueprint $table) {
    $table->id();

    $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();

    $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();

    $table->foreignId('semester_id')->constrained()->cascadeOnDelete();

    $table->string('name'); // Enrollment Fee, ID Fee, etc.

    $table->decimal('amount', 10, 2);

    $table->enum('type', ['Enrollment', 'Miscellaneous', 'Laboratory', 'Other']);

    $table->boolean('active')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
