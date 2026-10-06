<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentDocumentRequirement;
use Illuminate\Http\Request;

class EnrollmentDocumentRequirementController extends Controller
{
    /**
     * Get all document requirements for an enrollment.
     */
    public function index($enrollmentId)
    {
        $requirements = EnrollmentDocumentRequirement::where(
            'enrollment_id',
            $enrollmentId
        )
        ->orderBy('document_type')
        ->get();

        return response()->json([
            'success' => true,
            'requirements' => $requirements,
        ]);
    }

    /**
     * Approve a promissory undertaking.
     */
    public function approve(Request $request, $id)
    {
        $requirement = EnrollmentDocumentRequirement::findOrFail($id);

        if ($requirement->submission_type !== 'Promissory') {
            return response()->json([
                'success' => false,
                'message' => 'Only promissory requests can be approved.',
            ], 422);
        }

        if ($requirement->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'This promissory request has already been reviewed.',
            ], 422);
        }

        $requirement->update([
            'status' => 'Approved',
            'remarks' => $request->input('remarks'),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promissory request approved successfully.',
            'requirement' => $requirement->fresh(),
        ]);
    }

    /**
     * Reject a promissory undertaking.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|min:5|max:1000',
        ]);

        $requirement = EnrollmentDocumentRequirement::findOrFail($id);

        if ($requirement->submission_type !== 'Promissory') {
            return response()->json([
                'success' => false,
                'message' => 'Only promissory requests can be rejected.',
            ], 422);
        }

        if ($requirement->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'This promissory request has already been reviewed.',
            ], 422);
        }

        $requirement->update([
            'status' => 'Rejected',
            'remarks' => $request->remarks,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promissory request rejected successfully.',
            'requirement' => $requirement->fresh(),
        ]);
    }
}
