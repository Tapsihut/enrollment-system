<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolYearController extends Controller
{
    /**
     * Get all academic years.
     */
    public function index()
    {
        $schoolYears = SchoolYear::orderByDesc('school_year')->get();

        return response()->json($schoolYears);
    }


    /**
     * Get active academic year.
     */
    public function active()
    {
        $schoolYear = SchoolYear::where(
            'is_active',
            true
        )->first();

        return response()->json([
            'school_year' => $schoolYear
        ]);
    }


    /**
     * Activate academic year.
     */
    public function activate($id)
    {
        $schoolYear = SchoolYear::find($id);

        if (!$schoolYear) {
            return response()->json([
                'message' => 'Academic year not found.'
            ], 404);
        }

        DB::transaction(function () use ($schoolYear) {

            // Deactivate all academic years
            SchoolYear::query()->update([
                'is_active' => false
            ]);

            // Activate selected academic year
            $schoolYear->update([
                'is_active' => true
            ]);
        });

        return response()->json([
            'message' => 'Academic year activated successfully.',
            'school_year' => $schoolYear->fresh()
        ]);
    }
}