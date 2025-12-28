<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of events available for feedback
     */
    public function index()
    {
        // Get all registrations for the authenticated user where they've checked in
        $registrations = EventRegistration::with(['event', 'feedback'])
            ->where('user_id', Auth::id())
            ->whereNotNull('checked_in_at')
            ->orderBy('checked_in_at', 'desc')
            ->get();

        // Filter to only show events that have ended
        $registrations = $registrations->filter(function ($registration) {
            return now()->isAfter($registration->event->end_date);
        });

        return view('feedback.index', compact('registrations'));
    }

    /**
     * Show the feedback form for an event registration
     */
    public function create(EventRegistration $registration)
    {
        // Verify the user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if feedback already exists
        if ($registration->feedback) {
            return redirect()->route('feedback.show', $registration)
                ->with('info', 'You have already submitted feedback for this event.');
        }

        // Verify user has checked in
        if (!$registration->checked_in_at) {
            return redirect()->route('feedback.index')
                ->with('error', 'You must check in to the event before submitting feedback.');
        }

        // Load the event relationship
        $registration->load('event');
        
        // Check if event has ended
        $event = $registration->event;
        $eventEnded = now()->isAfter($event->end_date);
        
        if (!$eventEnded) {
            return redirect()->route('feedback.index')
                ->with('error', 'You can only submit feedback after the event has ended.');
        }

        return view('feedback.create', compact('registration'));
    }

    /**
     * Store the feedback for an event
     */
    public function store(Request $request, EventRegistration $registration)
    {
        // Verify the user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if feedback already exists
        if ($registration->feedback) {
            return redirect()->route('feedback.show', $registration)
                ->with('error', 'You have already submitted feedback for this event.');
        }

        // Verify user has checked in
        if (!$registration->checked_in_at) {
            return redirect()->route('feedback.index')
                ->with('error', 'You must check in to the event before submitting feedback.');
        }

        // Validate feedback data
        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'content_rating' => 'required|integer|min:1|max:5',
            'organization_rating' => 'required|integer|min:1|max:5',
            'platform_rating' => 'nullable|integer|min:1|max:5',
            'venue_rating' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string|max:2000',
            'suggestions' => 'nullable|string|max:2000',
            'system_feedback' => 'nullable|string|max:2000',
            'would_recommend' => 'required|boolean',
        ]);

        // Create feedback record
        $feedback = Feedback::create([
            'event_registration_id' => $registration->id,
            'overall_rating' => $validated['overall_rating'],
            'content_rating' => $validated['content_rating'],
            'organization_rating' => $validated['organization_rating'],
            'platform_rating' => $validated['platform_rating'] ?? null,
            'venue_rating' => $validated['venue_rating'] ?? null,
            'comments' => $validated['comments'] ?? null,
            'suggestions' => $validated['suggestions'] ?? null,
            'system_feedback' => $validated['system_feedback'] ?? null,
            'would_recommend' => $validated['would_recommend'],
            'submitted_at' => now(),
        ]);

        return redirect()->route('feedback.index')
            ->with('success', 'Thank you for your feedback! Your response has been submitted.');
    }

    /**
     * Display the submitted feedback for a registration
     */
    public function show(EventRegistration $registration)
    {
        // Verify the user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Load feedback relationship
        $registration->load(['event', 'feedback']);

        if (!$registration->feedback) {
            return redirect()->route('feedback.index')
                ->with('error', 'No feedback has been submitted for this event yet.');
        }

        $feedbackData = $registration->feedback;

        return view('feedback.show', compact('registration', 'feedbackData'));
    }
}
