<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('father_name')->nullable();

            $table->string('father_contact')->nullable();

            $table->string('mother_name')->nullable();

            $table->string('mother_contact')->nullable();

            $table->string('guardian_name');

            $table->string('relationship');

            $table->string('guardian_contact');

            $table->text('guardian_address');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};