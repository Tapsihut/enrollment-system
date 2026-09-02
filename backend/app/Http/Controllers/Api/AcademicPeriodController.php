<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicPeriodController extends Controller
{
    /**
     * Get current active academic period.
     */
    public function active()
    {
        $schoolYear = SchoolYear::where(
            'is_active',
            true
        )->first();

        $semester = Semester::where(
            'is_active',
            true
        )->first();

        if (!$schoolYear || !$semester) {

            return response()->json([
                'message' => 'No active academic period found.',
                'active_period' => null
            ], 404);
        }

        return response()->json([
            'active_period' => [
                'school_year_id' => $schoolYear->id,
                'school_year' => $schoolYear,

                'semester_id' => $semester->id,
                'semester' => $semester,
            ]
        ]);
    }


    /**
     * Set active academic year and semester.
     */
    public function setActive(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => [
                'required',
                'exists:school_years,id'
            ],

            'semester_id' => [
                'required',
                'exists:semesters,id'
            ],
        ]);


        $schoolYear = SchoolYear::find(
            $validated['school_year_id']
        );

        $semester = Semester::find(
            $validated['semester_id']
        );


        DB::transaction(function () use (
            $schoolYear,
            $semester
        ) {

            /*
             * Deactivate all academic years.
             */

            SchoolYear::query()->update([
                'is_active' => false
            ]);


            /*
             * Activate selected academic year.
             */

            $schoolYear->update([
                'is_active' => true
            ]);


            /*
             * Deactivate all semesters.
             */

            Semester::query()->update([
                'is_active' => false
            ]);


            /*
             * Activate selected semester.
             */

            $semester->update([
                'is_active' => true
            ]);
        });


        return response()->json([
            'message' =>
                'Academic period set as active successfully.',

            'active_period' => [
                'school_year_id' => $schoolYear->id,
                'school_year' => $schoolYear->fresh(),

                'semester_id' => $semester->id,
                'semester' => $semester->fresh(),
            ]
        ]);
    }
}