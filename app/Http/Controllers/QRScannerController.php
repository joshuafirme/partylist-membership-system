<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;

class QRScannerController extends Controller
{
    /**
     * Load the QR Scanner interface.
     */
    public function index()
    {
        // Fetch only active events for the admin to select from
        $activeEvents = Event::where('status', 1)
            ->orderBy('event_date', 'asc')
            ->get();

        return view('core.scanner.index', compact('activeEvents'));
    }

    /**
     * Process the scanned QR token via AJAX.
     */
    public function process(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        // 1. Find the user by their secure QR Token
        $user = User::with(['role', 'team'])->where('qr_token', $request->qr_token)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid QR Code. Member not found in the system.'
            ], 404);
        }

        // 2. Check for duplicate attendance
        $alreadyAttended = Attendance::where('event_id', $request->event_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Member has already been scanned for this event.',
                'user' => [
                    'name' => $user->name,
                    'team' => $user->team->name ?? 'No Team',
                ]
            ], 400); // 400 Bad Request
        }

        // 3. Record Attendance
        Attendance::create([
            'event_id' => $request->event_id,
            'user_id' => $user->id,
            'scanned_by' => auth()->id(),
            'time_in' => now(),
        ]);

        // 4. Return Success
        return response()->json([
            'status' => 'success',
            'message' => 'Attendance successfully recorded!',
            'user' => [
                'name' => $user->name,
                'role' => $user->role->name ?? 'Member',
                'team' => $user->team->name ?? 'No Team',
                'membership_number' => $user->membership_number ?? 'N/A'
            ]
        ]);
    }
}