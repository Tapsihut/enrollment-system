<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $table = 'school_years';

    protected $fillable = [
        'school_year',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}