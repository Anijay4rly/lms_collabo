<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LeaveApiController extends Controller
{
   public function index()
   {
$leave = LeaveRequest::with('user:id,fname,lname')->latest()->limit(2)->get();
return response()->json([
    'status' => 'success',
    'message' => 'Latest leave requests retrieved successfully',
    'data' => $leave
], 200);
   }



    // ... your index() method is up here ...

    public function store(Request $request)
    {
        // 1. Validation (Notice we added user_id just for today's testing)
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'request_type'     => 'required|string',
            'start_date'       => 'required|date|after_or_equal:today',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'emp_signature'    => 'required|string', // The Base64 string
            'reasons'          => 'nullable|string',
        ]);

        // 2. Check Maximum Days Rule
        $days = Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'])) + 1;
        if ($days > 14) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum allowed leave is 14 days.'
            ], 422);
        }

        // 3. Overlap Check Rule
        $overlap = LeaveRequest::where('user_id', $validated['user_id'])
            ->whereIn('status', ['submitted', 'pending', 'approved'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date',   [$validated['start_date'], $validated['end_date']])
                  ->orWhere(fn($q2) => $q2->where('start_date', '<=', $validated['start_date'])
                                           ->where('end_date',   '>=', $validated['end_date']));
            })->exists();

        if ($overlap) {
            return response()->json([
                'status' => 'error',
                'message' => 'You already have an overlapping leave request.'
            ], 422);
        }

        // 4. Handle the Base64 Signature
        $signaturePath = null;
        if ($request->filled('emp_signature')) {
            $image_parts = explode(";base64,", $request->emp_signature);

            if (count($image_parts) == 2) {
                $image_type = explode("image/", $image_parts[0])[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'signature_employee_' . $validated['user_id'] . '_' . time() . '.' . $image_type;
                $signaturePath = 'signatures/leaves/' . $fileName;
                Storage::disk('public')->put($signaturePath, $image_base64);
            }
        }

        // 5. Save to Database
        $leave = LeaveRequest::create([
            'user_id'          => $validated['user_id'],
            'request_type'     => $validated['request_type'],
            'start_date'       => $validated['start_date'],
            'end_date'         => $validated['end_date'],
            'reasons'          => $validated['reasons'],
            'emp_signature'    => $signaturePath,
            'status'           => 'submitted',
        ]);

        // 6. Return Success JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Leave request submitted successfully.',
            'data' => $leave
        ], 201);
    }

    public function show($id)
    {
        $leave = LeaveRequest::with('user')->find($id);

        if (!$leave) {
            return response()->json(['status' => 'error', 'message' => 'Leave request not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $leave], 200);
    }

    public function update(Request $request, $id)
    {
        $leave = LeaveRequest::find($id);

        if (!$leave) {
            return response()->json(['status' => 'error', 'message' => 'Leave request not found'], 404);
        }

        // Validate only the fields that the user is trying to change
        $validated = $request->validate([
            'request_type' => 'sometimes|string',
            'reasons'      => 'sometimes|string',
        ]);

        $leave->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request updated successfully.',
            'data' => $leave
        ], 200);
    }

    public function destroy($id)
    {
        $leave = LeaveRequest::find($id);

        if (!$leave) {
            return response()->json(['status' => 'error', 'message' => 'Leave request not found'], 404);
        }

        $leave->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request deleted successfully.'
        ], 200);
    }

}
