<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enrollment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('payment_reference')
                ->nullable();

            $table->string('paymongo_payment_id')
                ->nullable();

            $table->decimal('amount', 10, 2);

            $table->enum('status', [
                'Pending',
                'Paid',
                'Failed'
            ])->default('Pending');

            $table->string('payment_method')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};