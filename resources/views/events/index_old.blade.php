<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | SignUpGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f5f6fa; 
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar styles (copied from dashboard for consistent layout) */
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: white;
            padding: 2rem;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: #34495e;
        }

        .nav-link.active {
            background: #3498db;
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
            width: calc(100% - 260px);
        }

        .container { 
            max-width: 1200px;
            width: 100%;
        }
        .event { background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.06);} 
        .event h3 { margin: 0 0 0.5rem 0; }
        .meta { color: #7f8c8d; font-size: 0.9rem; }
        .btn { display:inline-block; padding:0.4rem 0.8rem; background:#3498db; color:#fff; border-radius:6px; text-decoration:none; }
        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 1rem; }
            .main-content { margin-left: 70px; }
        }
    </style>
</head>
<body>
    @include('partials.sidebar')
    <div class="main-content">
        <div class="container">
        <h1>Browse Events</h1>

        @if($events->isEmpty())
            <p>No events found.</p>
        @else
            @foreach($events as $event)
                <div class="event">
                    <h3>{{ $event->title }}</h3>
                    @php
                        try {
                            $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('F d, Y') : 'TBA';
                        } catch (\Exception $e) {
                            $startDate = 'TBA';
                        }
                        $venue = $event->venue_name ?: 'Online';
                        $summary = \Illuminate\Support\Str::limit($event->short_description ?? $event->description ?? '', 180);
                    @endphp
                    <p class="meta">{{ $startDate }} • {{ $venue }}</p>
                    <p>{{ $summary }}</p>
                    <a class="btn" href="{{ route('events.show', $event->id) }}">View details</a>
                </div>
            @endforeach

            <div class="pagination">
                {{ $events->links() }}
            </div>
        @endif
        </div>
    </div>
</body>
</html>
