<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== AI RECOMMENDATION DEBUG ===\n\n";

// Check future events
$futureEvents = App\Models\Event::where('start_date', '>=', now())->get();
echo "Future events (start_date >= today): " . $futureEvents->count() . "\n";
foreach ($futureEvents as $event) {
    echo "  - {$event->title} | Status: {$event->status} | Date: {$event->start_date}\n";
}
echo "\n";

// Check published future events
$publishedFutureEvents = App\Models\Event::published()->where('start_date', '>=', now())->get();
echo "Published future events: " . $publishedFutureEvents->count() . "\n";
foreach ($publishedFutureEvents as $event) {
    echo "  - {$event->title} | Date: {$event->start_date}\n";
}
echo "\n";

// Check if user is logged in (simulate)
$user = App\Models\User::first();
if ($user) {
    echo "Test user: {$user->name} (ID: {$user->id})\n";
    
    // Check past registrations
    $pastRegs = $user->eventRegistrations()
        ->whereIn('status', ['approved', 'confirmed'])
        ->whereHas('event', function($q) {
            $q->where('end_date', '<', now());
        })
        ->count();
    
    echo "Past approved/confirmed registrations: {$pastRegs}\n\n";
}

// Check if AI would trigger
echo "=== AI TRIGGER CONDITIONS ===\n";
echo "✓ User logged in: " . ($user ? "YES" : "NO") . "\n";
echo ($publishedFutureEvents->count() > 0 ? "✓" : "✗") . " Published future events exist: " . $publishedFutureEvents->count() . "\n";
echo "✓ No search filters: YES (assumed)\n\n";

if ($publishedFutureEvents->count() == 0) {
    echo "⚠️  AI WON'T WORK: No published future events!\n";
    echo "\nTo fix: Publish your future events by setting status='published'\n";
} else {
    echo "✅ AI SHOULD WORK if user is logged in and not searching!\n";
}
