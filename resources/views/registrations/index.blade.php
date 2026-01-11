@extends('layouts.app')

@section('title', 'My Registrations | SignUpGo')

@section('styles')
    <style>
        .container {
            max-width: 1200px;
            width: 100%;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        /* Profile Section */
        .profile-section {
            position: fixed !important;
            top: 1rem !important;
            right: 1rem !important;
            left: auto !important;
            z-index: 10000 !important;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            padding: 0.4rem 0.8rem 0.4rem 0.4rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 200px;
        }

        .profile-button:hover {
            border-color: #3498db;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
            transform: translateY(-2px);
        }

        .profile-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
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
            min-width: 0;
            flex: 1;
        }

        .profile-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.85rem;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }

        .profile-email {
            font-size: 0.7rem;
            color: #7f8c8d;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
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

        .page-header h1 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
        }

        .page-header p {
            margin: 0;
            color: #7f8c8d;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }

        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        /* Custom modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 90%;
            text-align: center;
        }

        .modal-box h3 {
            margin: 0 0 1rem 0;
            color: #2c3e50;
            font-size: 1.5rem;
        }

        .modal-box p {
            margin: 0 0 1.5rem 0;
            color: #555;
            font-size: 1rem;
            line-height: 1.5;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .modal-btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-btn-yes {
            background: #dc3545;
            color: white;
        }

        .modal-btn-yes:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .modal-btn-no {
            background: #6c757d;
            color: white;
        }

        .modal-btn-no:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .registration-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .registration-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .registration-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .registration-title {
            flex: 1;
        }

        .registration-title h3 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
        }

        .registration-title h3 a {
            color: #2c3e50;
            text-decoration: none;
        }

        .registration-title h3 a:hover {
            color: #3498db;
        }

        .registration-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .badge-jury {
            background: #822fe7;
            color: #f0eaf8;
        }

        .badge-reviewer {
            background: #c62e72;
            color: #dacad1;
        }

        .badge-participant {
            background: #024f58;
            color: #cffafe;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-approved {
            background: #d4edda;
            color: #155724;
        }

        .badge-rejected {
            background: #dc3545;
            color: #ffffff;
        }

        .badge-cancelled {
            background: #dc3545;
            color: #ffffff;
        }

        .badge-confirmed {
            background: #d4edda;
            color: #155724;
        }

        /* Conference Participant Status Badges */
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-primary {
            background: #cfe2ff;
            color: #084298;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .registration-details {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .registration-details p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: #495057;
        }

        .registration-details p strong {
            color: #2c3e50;
        }

        .registration-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .empty-state h3 {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #95a5a6;
            margin-bottom: 2rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #3498db;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination .active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        /* Registration Overview Styles */
        .registration-overview {
            background: linear-gradient(135deg, #059332 0%, #030161 100%);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }

        .registration-overview h2 {
            margin: 0 0 1.5rem 0;
            color: #ffffff;
            font-size: 1.5rem;
        }

        .overview-grid {
            display: grid;
            /* Forces two equal columns on desktop, stacks on mobile */
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            align-items: start;
            /* Ensures boxes align at the top if content height differs */
        }

        /* Responsive adjustment: Stack them only on mobile screens (less than 768px) */
        @media (max-width: 768px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }
        }

        .overview-category {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .overview-category.conference {
            border-left-color: #c59526;
        }

        .overview-category.innovation {
            border-left-color: #9b59b6;
        }

        .overview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .overview-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .overview-count {
            background: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            color: #2c3e50;
        }

        .overview-roles {
            margin-bottom: 1rem;
        }

        .overview-role {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            color: #495057;
            font-size: 0.95rem;
        }

        .overview-role-name {
            font-weight: 500;
        }

        .overview-role-count {
            font-weight: 600;
            color: #2c3e50;
        }

        .btn-view-details {
            width: 100%;
            padding: 0.75rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-view-details:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }

        .btn-view-details.active {
            background: #27ae60;
        }

        .overview-events {
            display: none;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed #dee2e6;
        }

        .overview-events.active {
            display: block;
        }

        .overview-event-item {
            background: white;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 6px;
            border-left: 3px solid;
            transition: all 0.3s ease;
        }

        .overview-event-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateX(4px);
        }

        .overview-event-item.conference {
            border-left-color: #c59526;
        }

        .overview-event-item.innovation {
            border-left-color: #9b59b6;
        }

        .overview-event-link {
            display: block;
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .overview-event-link:hover {
            color: #3498db;
        }

        .overview-event-meta {
            font-size: 0.85rem;
            color: #7f8c8d;
        }

        .overview-event-role {
            display: inline-block;
            margin-top: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #08942d;
        }

        /* Modal Overlay: Darkened background */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            /* Semi-transparent black */
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        /* Modal is shown when this class is added */
        .modal-overlay.active {
            display: flex;
        }

        /* Modal Box: The "Small Page" */
        .modal-content {
            background: #9b9b9b;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            padding: 2rem;
            border: 1px solid #3498db;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('content')
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

    <div class="container">
        <div class="page-header">
            <h1>My Registrations</h1>
            <p>View and manage your event registrations</p>
        </div>

        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        @if ($registrations->count() > 0)
            {{-- Registration Overview --}}
            @php
                $conferenceRegistrations = [];
                $innovationRegistrations = [];

                foreach ($registrations as $registration) {
                    $eventType = strtolower($registration->event->event_type ?? '');

                    if (stripos($eventType, 'conference') !== false) {
                        $conferenceRegistrations[] = $registration;
                    } elseif (stripos($eventType, 'innovation') !== false) {
                        $innovationRegistrations[] = $registration;
                    }
                }

                // Count by roles for Conference
                $conferenceParticipants = collect($conferenceRegistrations)
                    ->filter(function ($r) {
                        return in_array($r->role, ['participant', 'both']);
                    })
                    ->count();

                $conferenceReviewers = collect($conferenceRegistrations)
                    ->filter(function ($r) {
                        return in_array($r->role, ['reviewer', 'both']);
                    })
                    ->count();

                $conferenceJury = collect($conferenceRegistrations)
                    ->filter(function ($r) {
                        return in_array($r->role, ['jury', 'both']);
                    })
                    ->count();

                // Count by roles for Innovation
                $innovationParticipants = collect($innovationRegistrations)
                    ->filter(function ($r) {
                        return in_array($r->role, ['participant', 'both']);
                    })
                    ->count();

                $innovationReviewers = collect($innovationRegistrations)
                    ->filter(function ($r) {
                        return in_array($r->role, ['reviewer', 'both']);
                    })
                    ->count();

                $innovationJury = collect($innovationRegistrations)
                    ->filter(function ($r) {
                        return in_array($r->role, ['jury', 'both']);
                    })
                    ->count();
            @endphp

            @if (count($conferenceRegistrations) >= 0 || count($innovationRegistrations) >= 0)
                <div class="registration-overview">
                    <h2>Registration Overview</h2>
                    <div class="overview-grid">
                        {{-- Conference Overview --}}
                        @if (count($conferenceRegistrations) >= 0)
                            <div class="overview-category conference">
                                <div class="overview-header">
                                    <span class="overview-title">Conference</span>
                                    <span class="overview-count">({{ count($conferenceRegistrations) }})</span>
                                </div>
                                <div class="overview-roles">
                                    @if ($conferenceParticipants >= 0)
                                        <div class="overview-role">
                                            <span class="overview-role-name">Participant</span>
                                            <span class="overview-role-count">({{ $conferenceParticipants }})</span>
                                        </div>
                                    @endif
                                    @if ($conferenceReviewers >= 0)
                                        <div class="overview-role">
                                            <span class="overview-role-name">Reviewer</span>
                                            <span class="overview-role-count">({{ $conferenceReviewers }})</span>
                                        </div>
                                    @endif
                                </div>
                                <button class="btn-view-details" onclick="toggleDetails('conference')">
                                    View Details
                                </button>
                                <div class="overview-events" id="conference-events">
                                    @foreach ($conferenceRegistrations as $reg)
                                        <div class="overview-event-item conference">
                                            <a href="{{ route('event.dashboard', [$reg->event, $reg]) }}"
                                                class="overview-event-link">
                                                {{ $reg->event->title }}
                                            </a>
                                            <div class="overview-event-meta">
                                                @php
                                                    try {
                                                        $startDate = $reg->event->start_date
                                                            ? \Carbon\Carbon::parse($reg->event->start_date)->format(
                                                                'M d, Y',
                                                            )
                                                            : 'TBA';
                                                    } catch (\Exception $e) {
                                                        $startDate = 'TBA';
                                                    }
                                                    
                                                    // Determine role badge color
                                                    $overviewRoleBadgeColor = '#28a745'; // Green for successful
                                                    $isConfParticipant = in_array($reg->role, ['participant', 'both']);
                                                    if (in_array($reg->status, ['rejected', 'cancelled']) || 
                                                        ($isConfParticipant && $reg->presentation_status === 'rejected')) {
                                                        $overviewRoleBadgeColor = '#dc3545'; // Red
                                                    } elseif ($reg->status === 'pending' || 
                                                              ($isConfParticipant && empty($reg->presentation_status) && $reg->paper)) {
                                                        $overviewRoleBadgeColor = '#ffc107'; // Yellow
                                                    }
                                                @endphp
                                                📅 {{ $startDate }}
                                            </div>
                                            <span class="overview-event-role" style="background: {{ $overviewRoleBadgeColor }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">{{ ucfirst($reg->role) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Innovation Overview --}}
                        @if (count($innovationRegistrations) >= 0)
                            <div class="overview-category innovation">
                                <div class="overview-header">
                                    <span class="overview-title">Innovation</span>
                                    <span class="overview-count">({{ count($innovationRegistrations) }})</span>
                                </div>
                                <div class="overview-roles">
                                    @if ($innovationParticipants >= 0)
                                        <div class="overview-role">
                                            <span class="overview-role-name">Participant</span>
                                            <span class="overview-role-count">({{ $innovationParticipants }})</span>
                                        </div>
                                    @endif
                                    @if ($innovationJury >= 0)
                                        <div class="overview-role">
                                            <span class="overview-role-name">Jury</span>
                                            <span class="overview-role-count">({{ $innovationJury }})</span>
                                        </div>
                                    @endif
                                </div>
                                <button class="btn-view-details" onclick="toggleDetails('innovation')">
                                    View Details
                                </button>
                                <div class="overview-events" id="innovation-events">
                                    @foreach ($innovationRegistrations as $reg)
                                        <div class="overview-event-item innovation">
                                            <a href="{{ route('event.dashboard', [$reg->event, $reg]) }}"
                                                class="overview-event-link">
                                                {{ $reg->event->title }}
                                            </a>
                                            <div class="overview-event-meta">
                                                @php
                                                    try {
                                                        $startDate = $reg->event->start_date
                                                            ? \Carbon\Carbon::parse($reg->event->start_date)->format(
                                                                'M d, Y',
                                                            )
                                                            : 'TBA';
                                                    } catch (\Exception $e) {
                                                        $startDate = 'TBA';
                                                    }
                                                    
                                                    // Determine role badge color
                                                    $overviewRoleBadgeColor = '#28a745'; // Green for successful
                                                    if (in_array($reg->status, ['rejected', 'cancelled'])) {
                                                        $overviewRoleBadgeColor = '#dc3545'; // Red
                                                    } elseif ($reg->status === 'pending') {
                                                        $overviewRoleBadgeColor = '#ffc107'; // Yellow
                                                    }
                                                @endphp
                                                📅 {{ $startDate }}
                                            </div>
                                            <span class="overview-event-role" style="background: {{ $overviewRoleBadgeColor }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">{{ ucfirst($reg->role) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="modal-overlay" id="eventModal" onclick="closeModalOnOutsideClick(event)">
                            <div class="modal-content">
                                <div class="modal-header"
                                    style="display:flex; justify-content:space-between; margin-bottom:1rem;">
                                    <h2 id="modalTitle" style="color:#3498db; margin:0;">Event List</h2>
                                    <button onclick="closeModal()"
                                        style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
                                </div>
                                <div id="modalBody">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @foreach ($registrations as $registration)
                @php
                    $event = $registration->event;
                    try {
                        $startDate = $event->start_date
                            ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y')
                            : 'TBA';
                        $endDate = $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('M d, Y') : 'TBA';
                    } catch (\Exception $e) {
                        $startDate = 'TBA';
                        $endDate = 'TBA';
                    }
                    $venue = $event->venue_name ?: 'Online';

                    // Determine event type color
                    $isConferenceType = stripos($event->event_type, 'conference') !== false;
                    $isInnovationType = stripos($event->event_type, 'innovation') !== false;

                    $accentColor = '#3498db'; // default blue

                    if ($isConferenceType) {
                        $accentColor = '#c59526'; // conference gold
                    } elseif ($isInnovationType) {
                        $accentColor = '#9b59b6'; // innovation purple
                    }
                @endphp

                <div class="registration-card" style="border-left: 4px solid {{ $accentColor }};">
                    <div class="registration-header">
                        <div class="registration-title">
                            <h3>
                                <a href="{{ route('events.show', $event) }}" style="color: {{ $accentColor }};">
                                    {{ $event->title }}
                                </a>
                            </h3>
                            <p class="registration-meta">
                                {{ $startDate }} @if ($startDate !== $endDate)
                                    - {{ $endDate }}
                                @endif • {{ $venue }}
                            </p>
                        </div>
                        <div class="badges">
                            @php
                                $isConference = strtolower($event->event_type) === 'conference';
                                $isParticipant = in_array($registration->role, ['participant', 'both']);

                                // Check if paper is assigned to reviewers
                                $hasReviewerAssignment = false;
                                if ($registration->paper) {
                                    $hasReviewerAssignment = \DB::table('jury_mappings')
                                        ->where('participant_registration_id', $registration->id)
                                        ->exists();
                                }
                            @endphp
                            
                            <!-- Role Badge with fixed colors per role -->
                            @if($registration->role === 'participant')
                                <span class="badge" style="background: #17a2b8; color: white; border: none;">
                                    Participant
                                </span>
                            @elseif($registration->role === 'jury')
                                <span class="badge" style="background: #6f42c1; color: white; border: none;">
                                    Jury
                                </span>
                            @elseif($registration->role === 'reviewer')
                                <span class="badge" style="background: #e83e8c; color: white; border: none;">
                                    Reviewer
                                </span>
                            @elseif($registration->role === 'both')
                                <span class="badge" style="background: #fd7e14; color: white; border: none;">
                                    Both
                                </span>
                            @else
                                <span class="badge" style="background: #6c757d; color: white; border: none;">
                                    {{ ucfirst($registration->role) }}
                                </span>
                            @endif

                            @if ($isConference && $isParticipant)
                                <!-- Conference Participant Status - Check presentation_status first -->
                                @if ($registration->status === 'cancelled')
                                    <span class="badge badge-danger">
                                        ✗ Not Selected by EO
                                    </span>
                                @elseif($registration->presentation_status === 'selected')
                                    <span class="badge badge-success">
                                        ✓ Selected
                                    </span>
                                @elseif($registration->presentation_status === 'rejected')
                                    <span class="badge badge-danger">
                                        ✗ Rejected
                                    </span>
                                @elseif($registration->rejected_at)
                                    <span class="badge badge-danger">
                                        ✗ Rejected by EO
                                    </span>
                                @elseif($registration->paper)
                                    @if ($registration->paper->status === 'approved')
                                        <span class="badge badge-success">
                                            Paper Approved
                                        </span>
                                    @elseif($registration->paper->status === 'rejected')
                                        <span class="badge badge-danger">
                                            Paper Rejected
                                        </span>
                                    @elseif($registration->paper->status === 'under_review' || $hasReviewerAssignment)
                                        <span class="badge badge-info">
                                            In Review
                                        </span>
                                    @elseif($registration->paper->status === 'submitted')
                                        <span class="badge badge-warning">
                                            Pending Review
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            Paper Draft
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-{{ $registration->status }}">
                                        @if ($registration->status === 'cancelled')
                                            Registration Rejected
                                        @else
                                            {{ ucfirst($registration->status) }}
                                        @endif
                                    </span>
                                @endif
                            @else
                                <!-- Non-Conference or Jury/Reviewer Status -->
                                <span class="badge badge-{{ $registration->status }}">
                                    @if ($registration->status === 'cancelled')
                                        Registration Rejected
                                    @else
                                        {{ ucfirst($registration->status) }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="registration-details">
                        <p><strong>Registered on:</strong> {{ $registration->created_at->format('M d, Y h:i A') }}</p>

                        @if ($registration->is_jury)
                            <p><strong>Certificate:</strong> {{ $registration->certificate_filename ?: 'Uploaded' }}</p>
                        @endif

                        @if ($registration->status === 'pending')
                            <p style="color: #856404;">
                                <strong>Status:</strong>
                                @if ($registration->is_jury || $registration->is_reviewer)
                                    Your {{ $registration->role }} application is awaiting approval from the event
                                    organizer.
                                @else
                                    Your paper submission is under review by the event organizer. You'll be notified once
                                    approved.
                                @endif
                            </p>
                        @endif

                        @if ($registration->status === 'approved' && $registration->approved_at)
                            <p style="color: #155724;">
                                <strong>Approved:</strong> {{ $registration->approved_at->format('M d, Y h:i A') }}
                            </p>
                            @php
                                $eventStarted = $event->start_date && now()->gte($event->start_date);
                            @endphp
                            @if (!$eventStarted && $event->start_date)
                                <p style="color: #0c5460;">
                                    <strong>Event Dashboard:</strong> Will be accessible on
                                    {{ $event->start_date->format('M d, Y \a\t h:i A') }}
                                </p>
                            @endif
                        @endif

                        @if ($registration->status === 'rejected')
                            @if ($registration->rejected_reason)
                                <p style="color: #721c24;">
                                    <strong>Rejection Reason:</strong> {{ $registration->rejected_reason }}
                                </p>
                            @endif
                            @if ($registration->rejected_at)
                                <p style="color: #721c24;">
                                    <strong>Rejected on:</strong> {{ $registration->rejected_at->format('M d, Y h:i A') }}
                                </p>
                            @endif
                        @endif

                        @if ($isConference && $isParticipant && in_array($registration->presentation_status, ['selected', 'rejected']))
                            @php
                                // Calculate average score from all reviewers
                                $totalScore = 0;
                                $maxScore = 0;
                                $scoreCount = 0;

                                if ($registration->paper) {
                                    $scores = \DB::table('rubric_item_scores as ris')
                                        ->join('rubric_items as ri', 'ris.rubric_item_id', '=', 'ri.id')
                                        ->where('ris.event_paper_id', $registration->paper->id)
                                        ->select('ris.score', 'ri.max_score')
                                        ->get();

                                    foreach ($scores as $score) {
                                        $totalScore += $score->score;
                                        $maxScore += $score->max_score;
                                        $scoreCount++;
                                    }
                                }

                                $finalScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0;
                            @endphp

                            @if ($registration->presentation_status === 'selected')
                                <p style="color: #028020;">
                                    <strong>Evaluation Result:</strong> Your paper has been selected! Congratulations!
                                </p>
                            @else
                                <p style="color: #721c24;">
                                    <strong>Evaluation Result:</strong> Unfortunately, your paper was not selected this
                                    time.
                                </p>
                            @endif

                            @if ($scoreCount > 0)
                                <p style="color: #2c3e50;">
                                    <strong>Final Score:</strong> {{ $finalScore }}/100
                                </p>
                            @endif

                            @if ($registration->rejection_reason)
                                <p
                                    style="color: {{ $registration->presentation_status === 'selected' ? '#155724' : '#721c24' }};">
                                    <strong>EO Comment:</strong> {{ $registration->rejection_reason }}
                                </p>
                            @endif
                        @endif

                        @if ($registration->application_notes)
                            <p><strong>Your Notes:</strong> {{ $registration->application_notes }}</p>
                        @endif

                        @if ($registration->admin_notes)
                            <p><strong>Organizer Notes:</strong> {{ $registration->admin_notes }}</p>
                        @endif
                    </div>

                    <div class="registration-actions">
                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary">
                            View Event
                        </a>

                        {{-- Edit button for pending jury members to update their information --}}
                        @if ($registration->role === 'jury' && $registration->status === 'pending')
                            <a href="{{ route('registrations.edit', $registration) }}" class="btn btn-primary"
                                style="background: #f39c12;">
                                Edit Registration
                            </a>
                        @endif

                        @if ($registration->role === 'participant' && $registration->status === 'pending')
                            @php
                                $paper = \App\Models\EventPaper::where('user_id', $registration->user_id)
                                    ->where('event_id', $registration->event_id)
                                    ->first();
                                $isPastDeadline = false;

                                // Check if past paper submission deadline
                                if ($event->delivery_mode === 'face_to_face' && $event->f2f_paper_submission_deadline) {
                                    $isPastDeadline = now()->gt($event->f2f_paper_submission_deadline);
                                } elseif ($event->delivery_mode === 'online' && $event->online_paper_submission_deadline) {
                                    $isPastDeadline = now()->gt($event->online_paper_submission_deadline);
                                } elseif ($event->delivery_mode === 'hybrid') {
                                    $f2fPast = $event->f2f_paper_submission_deadline
                                        ? now()->gt($event->f2f_paper_submission_deadline)
                                        : false;
                                    $onlinePast = $event->online_paper_submission_deadline
                                        ? now()->gt($event->online_paper_submission_deadline)
                                        : false;
                                    $isPastDeadline = $f2fPast && $onlinePast;
                                }
                            @endphp

                            @if ($paper && !$isPastDeadline)
                                <a href="{{ route('registrations.edit', $registration) }}" class="btn btn-primary"
                                    style="background: #f39c12;">
                                    Edit Paper
                                </a>
                            @elseif($isPastDeadline)
                                <button class="btn" style="background: #95a5a6; cursor: not-allowed;" disabled
                                    title="Submission deadline has passed">
                                    Editing Closed
                                </button>
                            @endif
                        @endif

                        @if (in_array($registration->status, ['approved', 'confirmed']))
                            @php
                                // Access logic:
                                // - Jury/Reviewer: Can access after EO approves their application (not rejected)
                                // - Conference Participants: Can access only if presentation_status = 'selected' (after reviewer evaluation)
                                // - Innovation Participants: Can access when status = 'confirmed' (after EO approval, to make payment and see presentation details)

                                $isConferenceEvent = stripos($event->event_type, 'conference') !== false;
                                $isInnovationEvent = stripos($event->event_type, 'innovation') !== false;
                                
                                // Check if registration or presentation is rejected
                                $isRegistrationRejected = in_array($registration->status, ['rejected', 'cancelled']);

                                if (in_array($registration->role, ['jury', 'reviewer'])) {
                                    // Jury and Reviewers can access after approval, but not if rejected
                                    $canAccessDashboard = !$isRegistrationRejected;
                                } elseif ($isParticipant) {
                                    if ($isConferenceEvent) {
                                        // Conference participants: only selected ones can access (not rejected)
                                        $canAccessDashboard = $registration->presentation_status === 'selected' && !$isRegistrationRejected;
                                    } elseif ($isInnovationEvent) {
                                        // Innovation participants: can access when confirmed and not rejected
                                        $canAccessDashboard = $registration->status === 'confirmed' && !$isRegistrationRejected;
                                    } else {
                                        // Other event types: default to confirmed status and not rejected
                                        $canAccessDashboard = $registration->status === 'confirmed' && !$isRegistrationRejected;
                                    }
                                } else {
                                    // Fallback for any other roles
                                    $canAccessDashboard = false;
                                }
                            @endphp

                            @if ($canAccessDashboard)
                                <a href="{{ route('event.dashboard', [$event, $registration]) }}" class="btn btn-primary"
                                    style="background: #27ae60;">
                                    Go to Event Dashboard
                                    @if ($registration->role === 'jury')
                                        🎓
                                    @elseif($registration->role === 'reviewer')
                                        📝
                                    @else
                                        📄
                                    @endif
                                </a>
                            @elseif($isParticipant && $isConferenceEvent && $registration->presentation_status === 'rejected')
                                <button class="btn btn-primary"
                                    style="background: #dc3545; cursor: not-allowed; opacity: 0.6;" disabled
                                    title="Access denied - Paper not selected">
                                    Dashboard Access Denied ✗
                                </button>
                            @elseif($isParticipant && $isConferenceEvent && empty($registration->presentation_status))
                                <button class="btn btn-primary" style="background: #95a5a6; cursor: not-allowed;" disabled
                                    title="Waiting for reviewer evaluation decision">
                                    Awaiting Evaluation Decision
                                </button>
                            @elseif($isParticipant && $isInnovationEvent && $registration->status !== 'confirmed')
                                <button class="btn btn-primary" style="background: #95a5a6; cursor: not-allowed;" disabled
                                    title="Waiting for EO confirmation">
                                    Awaiting EO Confirmation
                                </button>
                            @else
                                <button class="btn btn-primary" style="background: #95a5a6; cursor: not-allowed;" disabled
                                    title="Dashboard access pending">
                                    Dashboard Not Available
                                </button>
                            @endif
                        @endif

                        @php
                            $eventEnded = $event->end_date && now()->gt($event->end_date);
                            
                            // Check if certificate has been generated for this user and event
                            // BUT exclude rejected registrations or rejected presentations
                            $isRejected = $registration->status === 'rejected' || 
                                          $registration->status === 'cancelled' || 
                                          $registration->presentation_status === 'rejected';
                            
                            $hasCertificate = !$isRejected && \DB::table('generated_certificates')
                                ->where('user_id', $registration->user_id)
                                ->where('event_id', $registration->event_id)
                                ->exists();
                            
                            // Check if user can submit feedback (event ended and registration not rejected)
                            $canSubmitFeedback = $eventEnded && !$isRejected && in_array($registration->status, ['approved', 'confirmed']);
                        @endphp
                        @if ($canSubmitFeedback)
                            <a href="{{ route('feedback.create', $registration) }}" class="btn btn-primary"
                                style="background: #92318d;">
                                💬 Event Feedback
                            </a>
                        @endif

                        @if ($hasCertificate && !$isRejected)
                            <a href="{{ route('certificates.index') }}" class="btn btn-primary"
                                style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); border: none;">
                                🎓 View Certificate
                            </a>
                        @endif

                        @if ($registration->status !== 'rejected')
                            <form id="cancel-form-{{ $registration->id }}"
                                action="{{ route('registrations.destroy', $registration) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger"
                                    onclick="showCancelModal({{ $registration->id }})">
                                    Cancel Registration
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            @if ($registrations->hasPages())
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($registrations->onFirstPage())
                        <span>&laquo; Previous</span>
                    @else
                        <a href="{{ $registrations->previousPageUrl() }}">&laquo; Previous</a>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $registrations->lastPage(); $i++)
                        @if ($i == $registrations->currentPage())
                            <span class="active">{{ $i }}</span>
                        @else
                            <a href="{{ $registrations->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($registrations->hasMorePages())
                        <a href="{{ $registrations->nextPageUrl() }}">Next &raquo;</a>
                    @else
                        <span>Next &raquo;</span>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-state">
                <h3>No Registrations Yet</h3>
                <p>You haven't registered for any events. Browse available events and register to participate.</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    Browse Events
                </a>
            </div>
        @endif
    </div>

    <!-- Custom Cancellation Modal -->
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Cancel Registration</h3>
            <p>Are you really want to cancel your registration?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-yes" onclick="confirmCancel()">Yes</button>
                <button class="modal-btn modal-btn-no" onclick="closeCancelModal()">No</button>
            </div>
        </div>
    </div>

    <script>
        let currentFormId = null;

        function showCancelModal(registrationId) {
            currentFormId = 'cancel-form-' + registrationId;
            document.getElementById('cancelModal').classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.remove('active');
            document.body.style.overflow = 'auto'; // Resume scrolling
            currentFormId = null;
        }

        function confirmCancel() {
            if (currentFormId) {
                document.getElementById(currentFormId).submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });

        // Toggle event details in overview - opens modal with event list
        function toggleDetails(type) {
            const modal = document.getElementById('eventModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');

            // Set Title based on category
            modalTitle.innerText = type.charAt(0).toUpperCase() + type.slice(1) + " Registrations";

            // Grab the list of events from your hidden div (e.g., #conference-events)
            const eventData = document.getElementById(type + '-events').innerHTML;

            // Inject and Show
            modalBody.innerHTML = eventData;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Stop page scroll
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
            document.body.style.overflow = 'auto'; // Resume page scroll
        }

        function closeModalOnOutsideClick(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        }

        // Prevent accidental form submission
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure all cancel forms require confirmation
            document.querySelectorAll('[id^="cancel-form-"]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Only allow submission if it's coming from the confirmation
                    if (!currentFormId) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>

@endsection
