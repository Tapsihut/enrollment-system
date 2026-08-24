<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\CurriculumSubject;

class CurriculumController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get All Courses
    |--------------------------------------------------------------------------
    */

    public function courses()
    {
        return response()->json(
            Course::orderBy('name')->get()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Subjects
    |--------------------------------------------------------------------------
    */

    public function allSubjects()
    {
        return response()->json(
            Subject::orderBy('code')->get()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Curricula
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Curriculum::with('course')
            ->orderBy('name');


        /*
        |--------------------------------------------------------------------------
        | Filter by Course
        |--------------------------------------------------------------------------
        */

        if ($request->filled('course_id')) {

            $query->where(
                'course_id',
                $request->course_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Active Only
        |--------------------------------------------------------------------------
        */

        if ($request->has('active')) {

            $query->where(
                'active',
                $request->active
            );
        }


        return response()->json(
            $query->get()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get One Curriculum
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $curriculum = Curriculum::with([
            'course',
            'curriculumSubjects.subject',
        ])->findOrFail($id);


        return response()->json(
            $curriculum
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Subjects Assigned to Curriculum
    |--------------------------------------------------------------------------
    */

    public function subjects($id)
    {
        $curriculum = Curriculum::findOrFail($id);


        $subjects = CurriculumSubject::with('subject')
            ->where(
                'curriculum_id',
                $curriculum->id
            )
            ->orderBy('year_level')
            ->orderBy('semester')
            ->get();


        return response()->json(
            $subjects
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add Subject to Curriculum
    |--------------------------------------------------------------------------
    */

    public function addSubject(
        Request $request,
        $id
    ) {

        $request->validate([

            'subject_id' => [
                'required',
                'exists:subjects,id'
            ],

            'year_level' => [
                'required',
                'integer'
            ],

            'semester' => [
                'required',
                'integer'
            ],

        ]);


        $curriculum = Curriculum::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Subject
        |--------------------------------------------------------------------------
        */

        $existing = CurriculumSubject::where(
            'curriculum_id',
            $curriculum->id
        )
        ->where(
            'subject_id',
            $request->subject_id
        )
        ->where(
            'year_level',
            $request->year_level
        )
        ->where(
            'semester',
            $request->semester
        )
        ->first();


        if ($existing) {

            return response()->json([

                'message' =>
                    'This subject is already assigned to this curriculum.'

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Create Curriculum Subject
        |--------------------------------------------------------------------------
        */

        $curriculumSubject =
            CurriculumSubject::create([

                'curriculum_id' =>
                    $curriculum->id,

                'subject_id' =>
                    $request->subject_id,

                'year_level' =>
                    $request->year_level,

                'semester' =>
                    $request->semester,

            ]);


        /*
        |--------------------------------------------------------------------------
        | Return Subject Information
        |--------------------------------------------------------------------------
        */

        $curriculumSubject->load('subject');


        return response()->json([

            'message' =>
                'Subject added to curriculum successfully.',

            'data' =>
                $curriculumSubject

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Remove Subject from Curriculum
    |--------------------------------------------------------------------------
    */

    public function removeSubject($id)
    {
        $curriculumSubject =
            CurriculumSubject::findOrFail($id);


        $curriculumSubject->delete();


        return response()->json([

            'message' =>
                'Subject removed from curriculum successfully.'

        ]);
    }
}