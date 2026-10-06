<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'school_id',
        'school_name',
        'school_type',
        'region',
        'division',
        'district',
        'province',
        'municipality',
        'barangay',
        'address',
        'sector',
        'school_subclassification',
        'curricular_offering',
        'active',
        'source',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function academicBackgrounds()
    {
        return $this->hasMany(AcademicBackground::class);
    }
}