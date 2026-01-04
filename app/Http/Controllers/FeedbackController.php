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
        // Get all registrations for the authenticated user
        $registrations = EventRegistration::with(['event', 'feedback'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'confirmed'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter based on role-specific conditions
        $registrations = $registrations->filter(function ($registration) {
            $event = $registration->event;
            $eventEnded = now()->isAfter($event->end_date);
            
            // Event must have ended for all roles
            if (!$eventEnded) {
                return false;
            }

            $role = $registration->role;
            $isConference = !empty($event->conference_categories);

            // Conference Participants & Jury: Must have checked in
            if (($role === 'participant' && $isConference) || $role === 'jury') {
                return $registration->checked_in_at !== null;
            }

            // Innovation Participants: Must have approved payment
            if ($role === 'participant' && !$isConference) {
                return $registration->payment_status === 'approved';
            }

            // Reviewers: Must have completed all evaluations
            if ($role === 'reviewer') {
                $assignedCount = \DB::table('jury_mappings')
                    ->where('event_id', $event->id)
                    ->where('reviewer_registration_id', $registration->id)
                    ->count();
                
                $completedCount = \DB::table('jury_mappings')
                    ->where('event_id', $event->id)
                    ->where('reviewer_registration_id', $registration->id)
                    ->where('status', 'completed')
                    ->count();
                
                return $assignedCount > 0 && $assignedCount === $completedCount;
            }

            return false;
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

        // Load the event relationship
        $registration->load('event');
        $event = $registration->event;
        
        // Check if event has ended
        $eventEnded = now()->isAfter($event->end_date);
        if (!$eventEnded) {
            return redirect()->route('feedback.index')
                ->with('error', 'You can only submit feedback after the event has ended.');
        }

        // Role-based access validation
        $role = $registration->role;
        $isConference = !empty($event->conference_categories);

        // Conference Participants & Jury: Must have checked in
        if (($role === 'participant' && $isConference) || $role === 'jury') {
            if (!$registration->checked_in_at) {
                return redirect()->route('feedback.index')
                    ->with('error', 'You must check in to the event before submitting feedback.');
            }
        }

        // Innovation Participants: Must have approved payment
        if ($role === 'participant' && !$isConference) {
            if ($registration->payment_status !== 'approved') {
                return redirect()->route('feedback.index')
                    ->with('error', 'Your payment must be approved before submitting feedback.');
            }
        }

        // Reviewers: Must have completed all evaluations
        if ($role === 'reviewer') {
            $assignedCount = \DB::table('jury_mappings')
                ->where('event_id', $event->id)
                ->where('reviewer_registration_id', $registration->id)
                ->count();
            
            $completedCount = \DB::table('jury_mappings')
                ->where('event_id', $event->id)
                ->where('reviewer_registration_id', $registration->id)
                ->where('status', 'completed')
                ->count();
            
            if ($assignedCount === 0 || $assignedCount !== $completedCount) {
                return redirect()->route('feedback.index')
                    ->with('error', 'You must complete all evaluations before submitting feedback.');
            }
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

        // Load the event relationship
        $registration->load('event');
        $event = $registration->event;
        
        // Check if event has ended
        $eventEnded = now()->isAfter($event->end_date);
        if (!$eventEnded) {
            return redirect()->route('feedback.index')
                ->with('error', 'You can only submit feedback after the event has ended.');
        }

        // Role-based access validation (same as create method)
        $role = $registration->role;
        $isConference = !empty($event->conference_categories);

        // Conference Participants & Jury: Must have checked in
        if (($role === 'participant' && $isConference) || $role === 'jury') {
            if (!$registration->checked_in_at) {
                return redirect()->route('feedback.index')
                    ->with('error', 'You must check in to the event before submitting feedback.');
            }
        }

        // Innovation Participants: Must have approved payment
        if ($role === 'participant' && !$isConference) {
            if ($registration->payment_status !== 'approved') {
                return redirect()->route('feedback.index')
                    ->with('error', 'Your payment must be approved before submitting feedback.');
            }
        }

        // Reviewers: Must have completed all evaluations
        if ($role === 'reviewer') {
            $assignedCount = \DB::table('jury_mappings')
                ->where('event_id', $event->id)
                ->where('reviewer_registration_id', $registration->id)
                ->count();
            
            $completedCount = \DB::table('jury_mappings')
                ->where('event_id', $event->id)
                ->where('reviewer_registration_id', $registration->id)
                ->where('status', 'completed')
                ->count();
            
            if ($assignedCount === 0 || $assignedCount !== $completedCount) {
                return redirect()->route('feedback.index')
                    ->with('error', 'You must complete all evaluations before submitting feedback.');
            }
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
            'system_feedback' => 'required|string|max:2000',
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
