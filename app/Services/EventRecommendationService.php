<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EventRecommendationService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
    }

    /**
     * Get AI-powered event recommendations for a user
     * 
     * @param User $user
     * @param Collection $upcomingEvents
     * @return Collection
     */
    public function getRecommendations(User $user, Collection $upcomingEvents): Collection
    {
        try {
            // If no API key, return empty collection
            if (empty($this->apiKey)) {
                Log::warning('Gemini API key not configured');
                return collect();
            }

            // If no upcoming events, return empty
            if ($upcomingEvents->isEmpty()) {
                return collect();
            }

            // Fetch user's past event history from event_registrations table
            // Only include approved/confirmed registrations (successful participation)
            $pastRegistrations = $user->eventRegistrations()
                ->with('event:id,title,description,event_type,category_id')
                ->whereIn('status', ['approved', 'confirmed'])
                ->whereHas('event', function($query) {
                    $query->where('end_date', '<', now());
                })
                ->latest()
                ->take(10)
                ->get();

            // Build user profile data
            $userProfile = $this->buildUserProfile($user, $pastRegistrations);

            // Prepare upcoming events data for Gemini
            $eventsData = $upcomingEvents->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->short_description ?? $event->description ?? '',
                    'category' => $event->category->name ?? 'General',
                    'event_type' => $event->event_type,
                    'start_date' => $event->start_date?->format('Y-m-d'),
                ];
            })->toArray();

            // Call Gemini API
            $recommendedIds = $this->callGeminiAPI($userProfile, $eventsData);

            // Filter and return recommended events
            return $upcomingEvents->whereIn('id', $recommendedIds)->take(3);

        } catch (\Exception $e) {
            Log::error('Event recommendation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Build user profile from past registrations
     */
    private function buildUserProfile(User $user, Collection $pastRegistrations): array
    {
        $pastEvents = $pastRegistrations->map(function($registration) {
            return [
                'title' => $registration->event->title ?? '',
                'role' => $registration->role,
                'event_type' => $registration->event->event_type ?? '',
                'category' => $registration->event->category->name ?? '',
            ];
        })->toArray();

        return [
            'name' => $user->name,
            'job_title' => $user->job_title,
            'organization' => $user->organization,
            'past_events' => $pastEvents,
        ];
    }

    /**
     * Call Gemini API to get recommendations
     */
    private function callGeminiAPI(array $userProfile, array $eventsData): array
    {
        $prompt = $this->buildPrompt($userProfile, $eventsData);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 256,
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Extract the generated text from Gemini response
                $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Parse the JSON from the generated text
                return $this->parseRecommendedIds($generatedText);
            }

            Log::error('Gemini API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [];

        } catch (\Exception $e) {
            Log::error('Gemini API call exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Build the prompt for Gemini
     */
    private function buildPrompt(array $userProfile, array $eventsData): string
    {
        $userInfo = json_encode($userProfile, JSON_PRETTY_PRINT);
        $events = json_encode($eventsData, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are an intelligent event recommendation system. Analyze the user's profile and past event participation to recommend the TOP 3 most relevant upcoming events.

User Profile:
{$userInfo}

Available Upcoming Events:
{$events}

TASK: Recommend exactly 3 events that best match this user's interests, professional background, and past event preferences.

Consider:
- Event type and category alignment with past participation
- Professional relevance (job title, organization)
- Diversity in recommendations (don't recommend only similar events)
- Recency and importance of the event

IMPORTANT: Return ONLY a valid JSON array of event IDs (integers), nothing else.
Example format: [5, 12, 8]

Your response:
PROMPT;
    }

    /**
     * Parse recommended event IDs from Gemini response
     */
    private function parseRecommendedIds(string $text): array
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/```json?\s*|\s*```/', '', $text);
        $text = trim($text);

        // Try to decode JSON
        $decoded = json_decode($text, true);
        
        if (is_array($decoded)) {
            // Filter to ensure we only have integers
            return array_filter($decoded, function($id) {
                return is_numeric($id);
            });
        }

        // Fallback: try to extract numbers from text
        preg_match_all('/\d+/', $text, $matches);
        return array_map('intval', $matches[0] ?? []);
    }
}
