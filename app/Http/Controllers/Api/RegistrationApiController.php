<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationApiController extends Controller
{
    /**
     * Get all registrations for a specific event.
     * 
     * @param int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventRegistrations($eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        $registrations = EventRegistration::where('event_id', $eventId)
            ->with(['user:id,name,email', 'event:id,title'])
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'event' => [
                        'id' => $registration->event->id,
                        'title' => $registration->event->title,
                    ],
                    'user' => [
                        'id' => $registration->user->id,
                        'name' => $registration->user->name,
                        'email' => $registration->user->email,
                    ],
                    'role' => $registration->role,
                    'status' => $registration->status,
                    'phone' => $registration->phone,
                    'organization' => $registration->organization,
                    'emergency_contact' => [
                        'name' => $registration->emergency_contact_name,
                        'phone' => $registration->emergency_contact_phone,
                    ],
                    'application_notes' => $registration->application_notes,
                    'admin_notes' => $registration->admin_notes,
                    'approved_at' => $registration->approved_at?->toIso8601String(),
                    'registered_at' => $registration->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $eventId,
                'event_title' => $event->title,
                'total_registrations' => $registrations->count(),
                'registrations' => $registrations,
            ],
        ]);
    }

    /**
     * Get registrations with filters.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegistrations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|integer|exists:events,id',
            'role' => 'nullable|string|in:participant,reviewer,jury',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = EventRegistration::with(['user:id,name,email', 'event:id,title']);

        // Apply filters
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $perPage = $request->input('per_page', 50);
        $registrations = $query->latest()->paginate($perPage);

        $data = $registrations->map(function ($registration) {
            return [
                'id' => $registration->id,
                'event' => [
                    'id' => $registration->event->id,
                    'title' => $registration->event->title,
                ],
                'user' => [
                    'id' => $registration->user->id,
                    'name' => $registration->user->name,
                    'email' => $registration->user->email,
                ],
                'role' => $registration->role,
                'status' => $registration->status,
                'phone' => $registration->phone,
                'organization' => $registration->organization,
                'emergency_contact' => [
                    'name' => $registration->emergency_contact_name,
                    'phone' => $registration->emergency_contact_phone,
                ],
                'application_notes' => $registration->application_notes,
                'admin_notes' => $registration->admin_notes,
                'approved_at' => $registration->approved_at?->toIso8601String(),
                'registered_at' => $registration->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $registrations->currentPage(),
                'per_page' => $registrations->perPage(),
                'total' => $registrations->total(),
                'last_page' => $registrations->lastPage(),
            ],
        ]);
    }

    /**
     * Get registration statistics.
     * 
     * @param int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegistrationStats($eventId = null)
    {
        $query = EventRegistration::query();

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found',
                ], 404);
            }
            $query->where('event_id', $eventId);
        }

        $stats = [
            'total_registrations' => $query->count(),
            'by_status' => [
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
            ],
            'by_role' => [
                'participant' => (clone $query)->where('role', 'participant')->count(),
                'reviewer' => (clone $query)->where('role', 'reviewer')->count(),
                'jury' => (clone $query)->where('role', 'jury')->count(),
            ],
        ];

        if ($eventId) {
            $stats['event'] = [
                'id' => $event->id,
                'title' => $event->title,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
