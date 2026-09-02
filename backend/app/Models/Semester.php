<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'semester_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}