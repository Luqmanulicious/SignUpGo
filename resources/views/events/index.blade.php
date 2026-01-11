@extends('layouts.app')

@section('title', 'Events | SignUpGo')

@section('styles')
<style>
    .container { 
        max-width: 1200px;
        width: 100%;
    }
    
    .search-box {
        background: rgb(255, 255, 255);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }
    
    .search-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group label {
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }
    
    .form-group input,
    .form-group select {
        padding: 0.6rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3498db;
    }
    
    .btn-search {
        padding: 0.6rem 1.5rem;
        background: #3aa93a;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-search:hover {
        background: #2c8a2c;
    }
    
    .btn-clear {
        padding: 0.6rem 1.5rem;
        background: #95a5a6;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .btn-clear:hover {
        background: #7f8c8d;
    }
    
    .event { 
        background: white; 
        padding: 1.5rem; 
        border-radius: 8px; 
        margin-bottom: 1rem; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        position: relative;
    } 
    
    .event:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .event.past-event {
        opacity: 0.7;
    }
    
    .event.past-event img,
    .event.past-event > div[style*="aspect-ratio"] {
        filter: grayscale(50%) blur(2px);
    }
    
    .event.past-event::after {
        content: '🕐 Event Ended';
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 10;
    }
    
    .event h3 { 
        margin: 0 0 0.5rem 0; 
        color: #2c3e50;
    }
    
    .meta { 
        color: #7f8c8d; 
        font-size: 0.9rem; 
        margin-bottom: 0.8rem;
    }
    
    .event-category {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        background: #9b59b6;
        color: white;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }
    
    .event-category.innovation {
        background: #9b59b6;
    }
    
    .event-category.conference {
        background: #c59526;
    }
    
    .btn { 
        display: inline-block; 
        padding: 0.6rem 1.2rem; 
        background: #3498db; 
        color: #fff; 
        border-radius: 6px; 
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        background: #2980b9;
        transform: translateY(-1px);
    }
    
    .pagination {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }
    
    .pagination nav {
        display: flex;
        justify-content: center;
    }
    
    .pagination nav > div {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .pagination nav svg {
        width: 18px;
        height: 18px;
    }
    
    .pagination nav a,
    .pagination nav span {
        min-width: 45px;
        height: 45px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }
    
    .pagination nav a:hover {
        background: #3498db;
        color: white;
        border-color: #3498db;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .pagination nav span[aria-current="page"] {
        background: #3498db;
        color: white;
        border-color: #3498db;
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
    }
    
    .pagination nav span[aria-disabled="true"] {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f5f5f5;
    }
    
    .per-page {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 1rem;
    }
    
    .per-page select {
        padding: 0.5rem 0.8rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        cursor: pointer;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }
    
    .per-page select:focus {
        outline: none;
        border-color: #3498db;
    }
    
    .search-results-info {
        margin-bottom: 1rem;
        color: #7f8c8d;
    }

    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin: 2rem 0;
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .empty-state-icon::before {
        content: '';
        position: absolute;
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        top: 30%;
        left: 30%;
    }

    .empty-state-icon::after {
        content: 'SEARCH';
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
        letter-spacing: 2px;
    }

    .empty-state h2 {
        color: #2c3e50;
        font-size: 1.8rem;
        margin: 0 0 1rem 0;
        font-weight: 600;
    }

    .empty-state p {
        color: #7f8c8d;
        font-size: 1.1rem;
        line-height: 1.6;
        margin: 0 0 2rem 0;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .empty-state-suggestions {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 2rem;
        text-align: left;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .empty-state-suggestions h3 {
        color: #34495e;
        font-size: 1rem;
        margin: 0 0 1rem 0;
        font-weight: 600;
    }

    .empty-state-suggestions ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .empty-state-suggestions li {
        color: #5a6c7d;
        padding: 0.5rem 0;
        padding-left: 1.5rem;
        position: relative;
    }

    .empty-state-suggestions li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #3498db;
        font-weight: bold;
    }

    /* Profile Section */
    .profile-section {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1000;
    }

    .profile-button {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 50px;
        padding: 0.5rem 1rem 0.5rem 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .profile-button:hover {
        border-color: #3498db;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        transform: translateY(-2px);
    }

    .profile-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .profile-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .profile-email {
        font-size: 0.75rem;
        color: #7f8c8d;
        line-height: 1.2;
    }

    .profile-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .profile-section.active .profile-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .profile-dropdown-item:first-child {
        border-radius: 12px 12px 0 0;
    }

    .profile-dropdown-item:last-child {
        border-bottom: none;
        border-radius: 0 0 12px 12px;
    }

    .profile-dropdown-item:hover {
        background: #f8f9fa;
        padding-left: 1.25rem;
    }

    .profile-dropdown-item.logout {
        color: #e74c3c;
    }

    .profile-dropdown-item.logout:hover {
        background: #fee;
    }

    .profile-dropdown-icon {
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .profile-section {
            top: 0.5rem;
            right: 0.5rem;
        }

        .profile-info {
            display: none;
        }

        .profile-button {
            padding: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Profile Section -->
    @auth
    <div class="profile-section" id="profileSection">
        <div class="profile-button" onclick="toggleProfile()">
            <div class="profile-avatar">
                @if(Auth::user()->profile_picture)
                    <img src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->name }}">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ Auth::user()->name }}</div>
                <div class="profile-email">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <div class="profile-dropdown">
            <a href="{{ route('dashboard') }}" class="profile-dropdown-item">
                <span class="profile-dropdown-icon">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('account.index') }}" class="profile-dropdown-item">
                <span class="profile-dropdown-icon">👤</span>
                <span>My Account</span>
            </a>
            <a href="{{ route('registrations.index') }}" class="profile-dropdown-item">
                <span class="profile-dropdown-icon">📝</span>
                <span>My Registrations</span>
            </a>
            <a href="{{ route('events.index') }}" class="profile-dropdown-item">
                <span class="profile-dropdown-icon">🎫</span>
                <span>Browse Events</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="profile-dropdown-item logout" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: 1rem; font-family: inherit;">
                    <span class="profile-dropdown-icon">🚪</span>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleProfile() {
            const profileSection = document.getElementById('profileSection');
            profileSection.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileSection = document.getElementById('profileSection');
            if (!profileSection.contains(event.target)) {
                profileSection.classList.remove('active');
            }
        });
    </script>
    @endauth

    <h1>Browse Events</h1>

    <!-- Search and Filter Form -->
    <div class="search-box">
        <!-- AI Recommended Events Section (inside search container) -->
        @if(isset($recommendedEvents) && $recommendedEvents->isNotEmpty())
            <div class="recommended-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                    <span style="font-size: 1.5rem; margin-right: 0.5rem;">✨</span>
                    <h3 style="color: white; margin: 0; font-size: 1.2rem; font-weight: 600;">AI Recommended for You</h3>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                    @foreach($recommendedEvents as $event)
                        @php
                            try {
                                $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'TBA';
                            } catch (\Exception $e) {
                                $startDate = 'TBA';
                            }
                            $venue = $event->venue_name ?: 'Online';
                        @endphp
                        
                        <div style="background: white; border-radius: 8px; padding: 1.2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <!-- AI Badge -->
                            <div style="position: absolute; top: 8px; right: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.3rem 0.6rem; border-radius: 15px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 6px rgba(102, 126, 234, 0.4);">
                                ✨ AI Pick
                            </div>
                            
                            @if($event->poster_url)
                                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 6px; margin-bottom: 0.8rem;" loading="lazy" onerror="this.style.display='none';">
                            @else
                                <div style="width: 100%; height: 120px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 6px; margin-bottom: 0.8rem; display: flex; align-items: center; justify-content: center; color: #7f8c8d; font-size: 2rem;">📅</div>
                            @endif
                            
                            @if($event->category)
                                @php
                                    $categoryClass = '';
                                    $categoryName = $event->category->name ?? 'Event';
                                    if (stripos($categoryName, 'innovation') !== false || stripos($categoryName, 'competition') !== false) {
                                        $categoryClass = 'innovation';
                                    } elseif (stripos($categoryName, 'conference') !== false) {
                                        $categoryClass = 'conference';
                                    }
                                @endphp
                                <span class="event-category {{ $categoryClass }}" style="display: inline-block; padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.7rem; font-weight: 500; margin-bottom: 0.6rem;">
                                    {{ $categoryName }}
                                </span>
                            @endif
                            
                            <h4 style="color: #2c3e50; margin: 0 0 0.4rem 0; font-size: 1rem; line-height: 1.3;">{{ $event->title }}</h4>
                            <p style="color: #7f8c8d; font-size: 0.8rem; margin-bottom: 0.6rem;">
                                📅 {{ $startDate }} • {{ $venue }}
                            </p>
                            <p style="color: #5a6c7d; font-size: 0.85rem; margin-bottom: 0.8rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ \Illuminate\Support\Str::limit($event->short_description ?? $event->description ?? '', 100) }}
                            </p>
                            <a class="btn" href="{{ route('events.show', $event->id) }}" style="display: inline-block; padding: 0.5rem 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.85rem; transition: all 0.3s ease;">
                                View Details
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <p style="color: rgba(255, 255, 255, 0.85); font-size: 0.75rem; margin-top: 0.8rem; text-align: center; margin-bottom: 0;">
                    <span style="opacity: 0.9;">🤖 Powered by Google Gemini AI</span>
                </p>
            </div>
        @endif
        
        <form method="GET" action="{{ route('events.index') }}" class="search-form">
            <div class="form-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" placeholder="Search events..." value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="">All Types</option>
                    <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Free Events</option>
                    <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>Paid Events</option>
                </select>
            </div>

            <div class="form-group">
                <label for="date_from">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label for="date_to">To Date</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn-search">Search</button>
            </div>

            @if(request()->hasAny(['search', 'category', 'type', 'date_from', 'date_to']))
                <div class="form-group">
                    <label>&nbsp;</label>
                    <a href="{{ route('events.index') }}" class="btn-clear">Clear</a>
                </div>
            @endif
        </form>
    </div>

    @if(request()->hasAny(['search', 'category', 'type', 'date_from', 'date_to']))
        <div class="search-results-info">
            Showing {{ $events->total() }} result(s) for your search
        </div>
    @endif

    @if($events->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"></div>
            @if(request()->hasAny(['search', 'category', 'type', 'date_from', 'date_to']))
                {{-- User applied filters but no results --}}
                <h2>No Events Found</h2>
                <p>We couldn't find any events matching your criteria. Try adjusting your search filters or explore all available events.</p>
                
                <div class="empty-state-suggestions">
                    <h3>Suggestions:</h3>
                    <ul>
                        <li>Try using different keywords</li>
                        <li>Remove some filters to broaden your search</li>
                        <li>Check the date range you've selected</li>
                        <li>Browse all categories for more options</li>
                    </ul>
                </div>
            @else
                {{-- No events in database at all --}}
                <h2>No Events Available Yet</h2>
                <p>There are currently no events scheduled. Check back soon for exciting upcoming events and opportunities.</p>
                
                <div class="empty-state-suggestions">
                    <h3>What's Next:</h3>
                    <ul>
                        <li>New events are added regularly</li>
                        <li>Check back later for updates</li>
                        <li>Follow us for event announcements</li>
                        <li>Contact us to create your own event</li>
                    </ul>
                </div>
            @endif
        </div>
    @else
        @foreach($events as $event)
            @php
                $eventEnded = $event->end_date && \Carbon\Carbon::now()->isAfter($event->end_date);
            @endphp
            <div class="event {{ $eventEnded ? 'past-event' : '' }}">
                @if($event->poster_url)
                    <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" style="width: 100%; aspect-ratio: 16/9; object-fit: cover; border-radius: 8px; margin-bottom: 1rem; background: #f5f5f5;" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div style="width: 100%; aspect-ratio: 16/9; background: #ecf0f1; border-radius: 8px; margin-bottom: 1rem; display: none; align-items: center; justify-content: center; color: #95a5a6; font-size: 3rem;">📅</div>
                @else
                    <div style="width: 100%; aspect-ratio: 16/9; background: #ecf0f1; border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; color: #95a5a6; font-size: 3rem;">📅</div>
                @endif
                
                @if($event->category)
                    @php
                        $categoryClass = '';
                        $categoryName = $event->category->name ?? 'Event';
                        if (stripos($categoryName, 'innovation') !== false || stripos($categoryName, 'competition') !== false) {
                            $categoryClass = 'innovation';
                        } elseif (stripos($categoryName, 'conference') !== false) {
                            $categoryClass = 'conference';
                        }
                    @endphp
                    <span class="event-category {{ $categoryClass }}">{{ $categoryName }}</span>
                @endif
                
                <h3>{{ $event->title }}</h3>
                @php
                    try {
                        $startDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('F d, Y') : 'TBA';
                    } catch (\Exception $e) {
                        $startDate = 'TBA';
                    }
                    $venue = $event->venue_name ?: 'Online';
                    $summary = \Illuminate\Support\Str::limit($event->short_description ?? $event->description ?? '', 180);
                    
                    // Check for paper submission deadline
                    $paperDeadline = null;
                    if ($event->f2f_paper_deadline) {
                        try {
                            $paperDeadline = \Carbon\Carbon::parse($event->f2f_paper_deadline)->format('M d, Y');
                        } catch (\Exception $e) {
                            $paperDeadline = null;
                        }
                    } elseif ($event->online_paper_deadline) {
                        try {
                            $paperDeadline = \Carbon\Carbon::parse($event->online_paper_deadline)->format('M d, Y');
                        } catch (\Exception $e) {
                            $paperDeadline = null;
                        }
                    }
                @endphp
                <p class="meta">{{ $startDate }} • {{ $venue }}</p>
                @if($paperDeadline)
                    <p class="meta" style="color: #e74c3c; font-weight: 500;">
                        📄 Paper Submission: {{ $paperDeadline }}
                    </p>
                @endif
                <p style="margin-bottom: 1.5rem;">{{ $summary }}</p>
                <a class="btn" href="{{ route('events.show', $event->id) }}">View details</a>
            </div>
        @endforeach

        <div class="per-page">
            <label for="per_page">Show:</label>
            <select id="per_page" name="per_page" onchange="updatePerPage(this.value)">
                <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
            </select>
            <span>per page</span>
        </div>
        
        <div class="pagination">
            {{ $events->appends(request()->except('page'))->links() }}
        </div>

        <script>
        function updatePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        </script>
    @endif
</div>
@endsection
