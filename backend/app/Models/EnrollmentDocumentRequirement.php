<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentDocumentRequirement extends Model
{
    protected $fillable = [
        'enrollment_id',
        'document_type',
        'submission_type',
        'promissory_reason',
        'status',
        'remarks',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(
            Enrollment::class
        );
    }
}

