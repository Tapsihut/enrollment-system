<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_provider')
                ->nullable()
                ->after('payment_method');

            $table->string('security_bank_payment_id')
                ->nullable()
                ->after('paymongo_payment_id');

            $table->string('security_bank_qr_id')
                ->nullable()
                ->after('security_bank_payment_id');

            $table->string('security_bank_transaction_id')
                ->nullable()
                ->after('security_bank_qr_id');

            $table->text('security_bank_qr_data')
                ->nullable()
                ->after('security_bank_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'security_bank_payment_id',
                'security_bank_qr_id',
                'security_bank_transaction_id',
                'security_bank_qr_data',
            ]);
        });
    }
};