<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    /**
     * Get all semesters.
     */
    public function index()
    {
        $semesters = Semester::orderBy('id')->get();

        return response()->json($semesters);
    }


    /**
     * Get active semester.
     */
    public function active()
    {
        $semester = Semester::where(
            'is_active',
            true
        )->first();

        return response()->json([
            'semester' => $semester
        ]);
    }


    /**
     * Activate semester.
     */
    public function activate($id)
    {
        $semester = Semester::find($id);

        if (!$semester) {
            return response()->json([
                'message' => 'Semester not found.'
            ], 404);
        }

        DB::transaction(function () use ($semester) {

            // Deactivate all semesters
            Semester::query()->update([
                'is_active' => false
            ]);

            // Activate selected semester
            $semester->update([
                'is_active' => true
            ]);
        });

        return response()->json([
            'message' => 'Semester activated successfully.',
            'semester' => $semester->fresh()
        ]);
    }
}