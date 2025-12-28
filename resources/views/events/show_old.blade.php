<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} | SignUpGo</title>
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
            width: 260px;
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
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
        .meta { color: #7f8c8d; }
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
            <a href="{{ route('events.index') }}">← Back to events</a>
            <div class="card">
            <h1>{{ $event->title }}</h1>
            @php
                try {
                    $start = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('F d, Y') : 'TBA';
                } catch (\Exception $e) {
                    $start = 'TBA';
                }
                try {
                    $end = $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('F d, Y') : 'TBA';
                } catch (\Exception $e) {
                    $end = 'TBA';
                }
                $venue = $event->venue_name ?: 'Online';
            @endphp
            <p class="meta">{{ $start }} - {{ $end }} • {{ $venue }}</p>
            <hr>
            <div>
                {!! nl2br(e($event->description)) !!}
            </div>

            <hr>
            <p><strong>Registration:</strong>
                @php
                    $isFree = false;
                    try { $isFree = (bool) $event->is_free; } catch (\Exception $e) { $isFree = false; }
                @endphp
                @if($isFree) Free @else {{ $event->currency ?? 'RM' }} {{ number_format($event->registration_fee ?? 0,2) }} @endif
            </p>

            <div style="margin-top:1rem;">
                <a class="btn" href="#">Register / Apply</a>
            </div>
            </div>
        </div>
    </div>
</body>
</html>
