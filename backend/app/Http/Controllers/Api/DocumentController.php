<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function enrollmentForm(Request $request)
    {

        $student = auth()->user()->student;

        $enrollment = $student->enrollments()
            ->latest()
            ->first();


        $pdf = Pdf::loadView(
            'pdf.enrollment-form',
            compact(
                'student',
                'enrollment'
            )
        );


        return $pdf->stream('enrollment-form.pdf');

    }
}