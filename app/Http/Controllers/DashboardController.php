<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Disable query cache and fetch fresh data
        $upcomingEvents = Event::with(['category'])
            ->published()
            ->upcoming()
            ->orderBy('start_date')
            ->take(5)
            ->get()
            ->each(function ($event) {
                $event->refresh();
            });

        $activeEvents = Event::with(['category'])
            ->published()
            ->active()
            ->orderBy('end_date')
            ->take(5)
            ->get()
            ->each(function ($event) {
                $event->refresh();
            });

        // Get all approved/confirmed registrations with their events (fresh data)
        $myRoles = EventRegistration::with(['event', 'event.category'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'confirmed'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->each(function ($registration) {
                $registration->event->refresh();
            });

        return view('dashboard', compact('upcomingEvents', 'activeEvents', 'myRoles'));
    }
}