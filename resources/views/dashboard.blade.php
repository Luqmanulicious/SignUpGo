<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SignUpGo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f6fa;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
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

        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-welcome {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .card-content {
            color: #34495e;
        }

        .event-item {
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
            transition: all 0.3s ease;
            display: flex;
            gap: 1rem;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-item:hover {
            background: #f8f9fa;
        }

        .event-image {
            width: 120px;
            height: 68px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
            background: #ecf0f1;
        }

        .event-details {
            flex: 1;
        }

        .event-item h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            color: #2c3e50;
        }

        .event-category {
            font-size: 0.8rem;
            color: #7f8c8d;
            margin: 0.2rem 0;
        }

        .event-date {
            font-size: 0.9rem;
            color: #2980b9;
            margin: 0.2rem 0;
        }

        .event-location {
            font-size: 0.9rem;
            color: #27ae60;
            margin: 0.2rem 0;
        }

        .event-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .event-badge.free {
            background: #2ecc71;
            color: white;
        }

        .event-badge.paid {
            background: #e74c3c;
            color: white;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #2ecc71;
            color: white;
        }

        .status-pending {
            background: #f1c40f;
            color: white;
        }

        /* Event Type Category Cards */
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .category-card.conference {
            border-color: #c59526;
        }

        .category-card.innovation {
            border-color: #9b59b6;
        }

        .category-card.workshop {
            border-color: #e67e22;
        }

        .category-card.seminar {
            border-color: #16a085;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .category-icon {
            font-size: 2.5rem;
        }

        .category-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .role-breakdown {
            margin: 1rem 0;
        }

        .role-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            color: #34495e;
            font-size: 0.95rem;
        }

        .role-count {
            font-weight: 600;
            color: #2c3e50;
        }

        .see-details-btn {
            width: 100%;
            padding: 0.75rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .see-details-btn:hover {
            background: #2980b9;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            padding: 2rem;
            position: relative;
            animation: slideUp 0.3s ease;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ecf0f1;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #7f8c8d;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: #ecf0f1;
            color: #2c3e50;
        }

        .modal-section {
            margin-bottom: 1.5rem;
        }

        .modal-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .event-list-item {
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            color: #34495e;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .event-list-item:hover {
            background: #ecf0f1;
            transform: translateX(5px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #7f8c8d;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* Carousel Styles for Auto-Slide */
        .carousel-container {
            position: relative;
            overflow: hidden;
        }

        .carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

    .carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #2c3e50;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        z-index: 10;
    }

    .carousel-arrow:hover {
        background: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .carousel-arrow.left {
        left: 20px;
    }

    .carousel-arrow.right {
        right: 20px;
    }

    .carousel-arrow:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #bdc3c7;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-dot.active {
            background: #3498db;
            width: 24px;
            border-radius: 4px;
        }

        /* Event Image Container with Category Badge */
        .event-image-container {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 12px;
            overflow: hidden;
            background: #ecf0f1;
        }

        .event-image-container .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            z-index: 2;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .event-category-badge.conference { background: #c59526; }
        .event-category-badge.innovation { background: #9b59b6; }

        .event-item-link {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .event-item-link:hover .event-image-container {
            transform: scale(1.02);
        }

        .carousel-slide .event-item {
            padding: 0;
            border: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 1rem;
            }

            .sidebar-header span,
            .nav-link span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="user-welcome">
                Welcome, {{ auth()->user()->name }}!
            </div>
            <div class="header-actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Event Type Category Cards -->
            @php
                // Group registrations by event type
                $eventTypeGroups = $myRoles->groupBy(function($registration) {
                    return strtolower($registration->event->event_type);
                });
                
                // Define event type properties
                $eventTypeConfig = [
                    'conference' => ['icon' => '📚', 'name' => 'Conference', 'color' => '#c59526'],
                    'innovation' => ['icon' => '💡', 'name' => 'Innovation', 'color' => '#9b59b6'],
                ];
                
                // Fixed order: Conference then Innovation
                $orderedTypes = ['conference', 'innovation'];
            @endphp

            @foreach($orderedTypes as $eventType)
                @php
                    $config = $eventTypeConfig[$eventType];
                    $registrations = $eventTypeGroups->get($eventType, collect());
                    
                    // Count roles - always calculate even if 0
                    $juryCount = $registrations->filter(fn($r) => in_array($r->role, ['jury', 'both']))->count();
                    $reviewerCount = $registrations->filter(fn($r) => in_array($r->role, ['reviewer', 'both']))->count();
                    
                    // Count only successful participants (exclude rejected)
                    $participantCount = $registrations->filter(function($r) use ($eventType) {
                        // Must be participant role
                        if (!in_array($r->role, ['participant', 'both'])) {
                            return false;
                        }
                        
                        // For conference events, exclude rejected presentations
                        if ($eventType === 'conference' && $r->presentation_status === 'rejected') {
                            return false;
                        }
                        
                        return true;
                    })->count();
                @endphp
                
                <div class="category-card {{ $eventType }}" onclick="openModal('{{ $eventType }}')">
                    <div class="category-header">
                        <span class="category-icon">{{ $config['icon'] }}</span>
                        <h3 class="category-title">{{ $config['name'] }}</h3>
                    </div>
                    
                    <div class="role-breakdown">
                        @if($eventType === 'conference')
                            <div class="role-item">
                                <span>📝 Reviewer</span>
                                <span class="role-count">{{ $reviewerCount }} {{ Str::plural('event', $reviewerCount) }}</span>
                            </div>
                            <div class="role-item">
                                <span>👤 Participant</span>
                                <span class="role-count">{{ $participantCount }} {{ Str::plural('event', $participantCount) }}</span>
                            </div>
                        @elseif($eventType === 'innovation')
                            <div class="role-item">
                                <span>🎓 Jury</span>
                                <span class="role-count">{{ $juryCount }} {{ Str::plural('event', $juryCount) }}</span>
                            </div>
                            <div class="role-item">
                                <span>👤 Participant</span>
                                <span class="role-count">{{ $participantCount }} {{ Str::plural('event', $participantCount) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <button class="see-details-btn" onclick="event.stopPropagation(); openModal('{{ $eventType }}')">
                        See details
                    </button>
                </div>
            @endforeach

        </div>

        <!-- Upcoming Events Section Below -->
        <div style="margin-top: 1.5rem;">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Events</h3>
                    <span class="status-badge status-active">{{ $upcomingEvents->count() }} Events</span>
                </div>
                <div class="card-content">
                    @if($upcomingEvents->isNotEmpty())
                        <div class="carousel-container">
                            @if($upcomingEvents->count() > 1)
                                <button class="carousel-arrow left" id="prevBtn" onclick="prevSlide()">‹</button>
                                <button class="carousel-arrow right" id="nextBtn" onclick="nextSlide()">›</button>
                            @endif
                            <div class="carousel-wrapper" id="carousel-wrapper">
                                @foreach($upcomingEvents as $event)
                                    <div class="carousel-slide">
                                        <a href="{{ route('events.show', $event) }}" class="event-item-link">
                                            <div class="event-item">
                                                <div class="event-image-container">
                                                    @if($event->featured_image)
                                                        <img src="{{ $event->featured_image }}" alt="{{ $event->title }}" class="event-image" loading="lazy">
                                                    @else
                                                        <div class="event-image" style="display: flex; align-items: center; justify-content: center; background: #ecf0f1; color: #95a5a6; font-size: 4rem;">📅</div>
                                                    @endif
                                                    @php
                                                        // Determine if conference or innovation
                                                        $eventTypeLower = strtolower($event->event_type);
                                                        if (str_contains($eventTypeLower, 'conference')) {
                                                            $badgeClass = 'conference';
                                                            $badgeLabel = 'Conference';
                                                        } elseif (str_contains($eventTypeLower, 'innovation')) {
                                                            $badgeClass = 'innovation';
                                                            $badgeLabel = 'Innovation';
                                                        } else {
                                                            // Default to conference if not specified
                                                            $badgeClass = 'conference';
                                                            $badgeLabel = 'Conference';
                                                        }
                                                    @endphp
                                                    <span class="event-category-badge {{ $badgeClass }}">
                                                        {{ $badgeLabel }}
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            @if($upcomingEvents->count() > 1)
                                <div class="carousel-indicators">
                                    @foreach($upcomingEvents as $index => $event)
                                        <span class="carousel-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <p>No upcoming events at the moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Event Details -->
    @foreach(['conference', 'innovation'] as $eventType)
        @php
            $config = $eventTypeConfig[$eventType];
            $registrations = $eventTypeGroups->get($eventType, collect());
            
            // Group by role
            $juryEvents = $registrations->filter(fn($r) => in_array($r->role, ['jury', 'both']));
            $reviewerEvents = $registrations->filter(fn($r) => in_array($r->role, ['reviewer', 'both']));
            
            // Filter successful participants only (exclude rejected)
            $participantEvents = $registrations->filter(function($r) use ($eventType) {
                // Must be participant role
                if (!in_array($r->role, ['participant', 'both'])) {
                    return false;
                }
                
                // For conference events, exclude rejected presentations
                if ($eventType === 'conference' && $r->presentation_status === 'rejected') {
                    return false;
                }
                
                return true;
            });
        @endphp
        
        <div class="modal-overlay" id="modal-{{ $eventType }}" onclick="closeModalOnOutsideClick(event, '{{ $eventType }}')">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <span>{{ $config['icon'] }}</span>
                        <span>{{ $config['name'] }} Events</span>
                    </h2>
                    <button class="modal-close" onclick="closeModal('{{ $eventType }}')">&times;</button>
                </div>
                
                @if($eventType === 'innovation')
                    <div class="modal-section">
                        <h3 class="modal-section-title">🎓 Jury</h3>
                        @if($juryEvents->isNotEmpty())
                            <ul class="event-list">
                                @foreach($juryEvents as $registration)
                                    <li class="event-list-item">
                                        <a href="{{ route('event.dashboard', [$registration->event, $registration]) }}" style="text-decoration: none; color: inherit; display: block;">
                                            {{ $registration->event->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">No jury events</p>
                        @endif
                    </div>
                @endif
                
                @if($eventType === 'conference')
                    <div class="modal-section">
                        <h3 class="modal-section-title">📝 Reviewer</h3>
                        @if($reviewerEvents->isNotEmpty())
                            <ul class="event-list">
                                @foreach($reviewerEvents as $registration)
                                    <li class="event-list-item">
                                        <a href="{{ route('event.dashboard', [$registration->event, $registration]) }}" style="text-decoration: none; color: inherit; display: block;">
                                            {{ $registration->event->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">No reviewer events</p>
                        @endif
                    </div>
                @endif
                
                <div class="modal-section">
                    <h3 class="modal-section-title">👤 Participant</h3>
                    @if($participantEvents->isNotEmpty())
                        <ul class="event-list">
                            @foreach($participantEvents as $registration)
                                <li class="event-list-item">
                                    <a href="{{ route('event.dashboard', [$registration->event, $registration]) }}" style="text-decoration: none; color: inherit; display: block;">
                                        {{ $registration->event->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p style="color: #7f8c8d; font-size: 0.9rem; margin: 0;">No participant events</p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <script>
        // Modal Functions
        function openModal(eventType) {
            document.getElementById('modal-' + eventType).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal(eventType) {
            document.getElementById('modal-' + eventType).classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        function closeModalOnOutsideClick(event, eventType) {
            if (event.target === event.currentTarget) {
                closeModal(eventType);
            }
        }
        
        // Auto-slide Carousel for Upcoming Events
        const carouselWrapper = document.getElementById('carousel-wrapper');
        const carouselDots = document.querySelectorAll('.carousel-dot');
        const totalSlides = {{ $upcomingEvents->count() }};
        let currentSlide = 0;
        let autoSlideInterval;
        
        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            const offset = -slideIndex * 100;
            carouselWrapper.style.transform = `translateX(${offset}%)`;
            
            // Update dots
            carouselDots.forEach((dot, index) => {
                if (index === slideIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
        
        function nextSlide() {
            const next = (currentSlide + 1) % totalSlides;
            goToSlide(next);
        }
        
        function prevSlide() {
            const prev = (currentSlide - 1 + totalSlides) % totalSlides;
            stopAutoSlide();
            goToSlide(prev);
            startAutoSlide();
        }
        
        // Override nextSlide for manual click to restart auto-slide
        window.nextSlide = function() {
            const next = (currentSlide + 1) % totalSlides;
            stopAutoSlide();
            goToSlide(next);
            startAutoSlide();
        };
        
        function startAutoSlide() {
            if (totalSlides > 1) {
                autoSlideInterval = setInterval(nextSlide, 3000); // Slide every 3 seconds
            }
        }
        
        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }
        
        // Add click handlers to dots
        carouselDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                stopAutoSlide();
                goToSlide(index);
                startAutoSlide(); // Restart auto-slide after manual interaction
            });
        });
        
        // Start auto-slide on page load
        if (totalSlides > 1) {
            startAutoSlide();
            
            // Pause on hover
            const carouselContainer = document.querySelector('.carousel-container');
            if (carouselContainer) {
                carouselContainer.addEventListener('mouseenter', stopAutoSlide);
                carouselContainer.addEventListener('mouseleave', startAutoSlide);
            }
        }
    </script>
</body>
</html>