<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function search(Request $request)
    {
        $search = trim($request->get('search', ''));

        if ($search === '') {
            return response()->json([
                'data' => []
            ]);
        }

        $schools = School::query()
            ->where('active', true)
            ->where(function ($query) use ($search) {
                $query->where('school_name', 'like', "%{$search}%")
                    ->orWhere('school_id', 'like', "%{$search}%")
                    ->orWhere('municipality', 'like', "%{$search}%")
                    ->orWhere('province', 'like', "%{$search}%");
            })
            ->orderBy('school_name')
            ->limit(20)
            ->get([
                'id',
                'school_id',
                'school_name',
                'school_type',
                'region',
                'division',
                'province',
                'municipality',
                'address',
            ]);

        return response()->json([
            'data' => $schools
        ]);
    }

    public function show($id)
    {
        $school = School::where('active', true)
            ->findOrFail($id);

        return response()->json([
            'school' => $school
        ]);
    }
}