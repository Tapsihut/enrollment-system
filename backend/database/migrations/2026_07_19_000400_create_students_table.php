<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();


            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->string('student_number')
                ->nullable();


            // Student Classification
            $table->enum('student_type', [

                'Freshmen',
                'Transferee',
                'Continuing',
                'Returnee'

            ]);



            $table->string('first_name');


            $table->string('middle_name')
                ->nullable();


            $table->string('last_name');


            $table->date('birth_date');


            $table->enum('gender', [

                'Male',
                'Female'

            ]);



            $table->string('civil_status')
                ->nullable();


            $table->string('nationality')
                ->nullable();


            $table->string('religion')
                ->nullable();


            $table->string('contact_number');


            $table->string('email')
                ->nullable();


            $table->text('address');


            $table->timestamps();

        });
    }



    public function down(): void
    {
        Schema::dropIfExists('students');
    }

};