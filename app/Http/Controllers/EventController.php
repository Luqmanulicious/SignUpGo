<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
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
        
        $events = $query->orderBy('start_date')->paginate($perPage)->withQueryString();
        
        // Get categories for filter dropdown
        $categories = \App\Models\EventCategory::orderBy('name')->get();
        
        return view('events.index', compact('events', 'categories'));
    }

    public function show(Event $event)
    {
        // Refresh the event from database to get latest data
        $event = $event->fresh(['category', 'organizer']);
        
        return view('events.show', compact('event'));
    }
}
