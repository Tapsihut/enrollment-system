<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::create('academic_backgrounds', function (Blueprint $table) {

            $table->id();


            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->enum('student_type', [

                'Freshmen',
                'Transferee',
                'Returnee',
                'Continuing'

            ]);


            $table->string('last_school')->nullable();


            $table->string('school_address')->nullable();


            $table->string('strand')->nullable();


            $table->year('graduation_year')->nullable();


            $table->decimal('gwa',5,2)->nullable();


            // Transferee

            $table->string('previous_course')
                ->nullable();


            $table->integer('units_earned')
                ->nullable();



            // Returnee

            $table->string('last_school_year')
                ->nullable();


            $table->string('last_semester')
                ->nullable();



            $table->timestamps();


        });


    }



    public function down(): void
    {

        Schema::dropIfExists('academic_backgrounds');

    }

};