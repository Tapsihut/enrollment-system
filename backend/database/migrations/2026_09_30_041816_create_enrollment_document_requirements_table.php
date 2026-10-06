<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_document_requirements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('enrollment_id')
                ->constrained('enrollments')
                ->cascadeOnDelete();

            $table->string('document_type');

            $table->enum('submission_type', [
                'Uploaded',
                'Promissory',
            ]);

            $table->text('promissory_reason')
                ->nullable();

            $table->enum('status', [
                'Pending',
                'Approved',
                'Rejected',
            ])->default('Pending');

            $table->text('remarks')
                ->nullable();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Short custom index name
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['enrollment_id', 'document_type'],
                'enroll_doc_req_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'enrollment_document_requirements'
        );
    }
};