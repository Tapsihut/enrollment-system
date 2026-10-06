<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();

            // DepEd / BEIS
            $table->string('school_id')->nullable()->unique();
            $table->string('school_name');

            // Location / organization
            $table->string('region')->nullable();
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('street_address')->nullable();
            $table->string('municipality')->nullable();
            $table->string('legislative_district')->nullable();
            $table->string('barangay')->nullable();

            // Classification
            $table->string('sector')->nullable();
            $table->string('urban_rural_classification')->nullable();
            $table->string('school_subclassification')->nullable();
            $table->string('curricular_offering')->nullable();

            // System
            $table->string('source')->default('DepEd');
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index('school_name');
            $table->index('municipality');
            $table->index('division');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};