<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Assessment extends Model
{

    protected $fillable = [

        'enrollment_id',

        'enrollment_fee',

        'miscellaneous_fee',

        'other_fee',

        'total_amount',

        'status'

    ];


    public function enrollment()
    {
        return $this->belongsTo(
            Enrollment::class
        );
    }

}