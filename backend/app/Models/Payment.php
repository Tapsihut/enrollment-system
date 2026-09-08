<?php

namespace App\Models;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'enrollment_id',
        'payment_reference',
        'paymongo_payment_id',
        'amount',
        'status',
        'payment_method'
    ];

    public function enrollment()
    {
        return $this->belongsTo(
            Enrollment::class,
            'enrollment_id'
        );
    }
}