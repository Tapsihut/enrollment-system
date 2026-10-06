<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;

class Payment extends Model
{
    protected $fillable = [
        'enrollment_id',

        // General payment fields
        'payment_reference',
        'amount',
        'status',
        'payment_method',
        'payment_provider',

        // PayMongo
        'paymongo_payment_id',

        // Security Bank
        'security_bank_payment_id',
        'security_bank_qr_id',
        'security_bank_transaction_id',
        'security_bank_qr_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function enrollment()
    {
        return $this->belongsTo(
            Enrollment::class,
            'enrollment_id'
        );
    }
}