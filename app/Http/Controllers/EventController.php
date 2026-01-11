<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected $recommendationService;

    public function __construct(EventRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index(Request $request)
    {
        $query = Event::query()->published();

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        // Filter by event type (free/paid)
        if ($request->filled('type')) {
            if ($request->type === 'free') {
                $query->whereRaw("is_free = 'true'");
            } elseif ($request->type === 'paid') {
                $query->whereRaw("is_free = 'false'");
            }
        }

        // Get per_page value from request, default to 10, max 50
        $perPage = min((int) $request->input('per_page', 10), 50);
        
        // Sort by newest first (descending order)
        $events = $query->orderBy('start_date', 'desc')->paginate($perPage)->withQueryString();
        
        // Get categories for filter dropdown
        $categories = \App\Models\EventCategory::orderBy('name')->get();
        
        // Get AI recommendations for logged-in users (only if no search/filters applied)
        $recommendedEvents = collect();
        if (Auth::check() && !$request->hasAny(['search', 'category', 'type', 'date_from', 'date_to'])) {
            try {
                Log::info('Starting AI recommendation fetch for user: ' . Auth::id());
                
                // Fetch upcoming events (events that haven't ended yet)
                $upcomingEvents = Event::query()
                    ->published()
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    })
                    ->where('start_date', '>=', now())
                    ->with('category')
                    ->orderBy('start_date', 'asc')
                    ->get();

                Log::info('Upcoming events count: ' . $upcomingEvents->count());

                // Get recommendations from AI service
                if ($upcomingEvents->isNotEmpty()) {
                    $recommendedEvents = $this->recommendationService->getRecommendations(
                        Auth::user(),
                        $upcomingEvents
                    );
                    Log::info('Recommended events count: ' . $recommendedEvents->count());
                }
            } catch (\Exception $e) {
                Log::warning('Failed to get event recommendations: ' . $e->getMessage());
                // Continue without recommendations
            }
        }
        
        return view('events.index', compact('events', 'categories', 'recommendedEvents'));
    }

    public function show(Event $event)
    {
        // Refresh the event from database to get latest data
        $event = $event->fresh(['category', 'organizer']);
        
        return view('events.show', compact('event'));
    }
}
