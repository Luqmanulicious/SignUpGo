<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    /**
     * Display a listing of user's registrations with QR codes.
     */
    public function index()
    {
        $registrations = EventRegistration::with(['event.category', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Load papers manually for each registration
        foreach ($registrations as $registration) {
            $registration->paper = \App\Models\EventPaper::where('user_id', $registration->user_id)
                ->where('event_id', $registration->event_id)
                ->first();
        }

        return view('dashboard.registrations.index', compact('registrations'));
    }

    /**
     * Display a single registration with QR code.
     */
    public function show(EventRegistration $registration)
    {
        // Check if user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $registration->load(['event.category', 'user']);
        
        // Load paper manually
        $registration->paper = \App\Models\EventPaper::where('user_id', $registration->user_id)
            ->where('event_id', $registration->event_id)
            ->first();

        return view('dashboard.registrations.show', compact('registration'));
    }
}
