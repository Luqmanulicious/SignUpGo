@extends('layouts.app')

@section('styles')
<style>
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

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="mb-3">
                <a href="{{ route('feedback.index') }}" class="btn btn-secondary">
                    ← Back to Feedback List
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">✓ Your Submitted Feedback</h4>
                </div>
                <div class="card-body">
                    <!-- Event Info -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="mb-2">{{ $registration->event->title }}</h5>
                        <p class="mb-1 text-muted">
                            <strong>Date:</strong> {{ $registration->event->start_date->format('M d, Y') }}
                        </p>
                        <p class="mb-1 text-muted">
                            <strong>Your Role:</strong> {{ ucfirst($registration->role) }}
                        </p>
                        <p class="mb-0 text-muted">
                            <strong>Feedback Submitted:</strong> {{ \Carbon\Carbon::parse($feedbackData['submitted_at'])->format('M d, Y h:i A') }}
                        </p>
                    </div>

                    <!-- Ratings Section -->
                    <div class="mb-4">
                        <h5 class="mb-3">Your Ratings</h5>

                        <!-- Overall Rating -->
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Overall Experience</strong>
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedbackData->overall_rating ? 'filled' : '' }}">★</span>
                                    @endfor
                                    <span class="ms-2 text-muted">({{ $feedbackData->overall_rating }}/5)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Rating -->
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Content Quality</strong>
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedbackData->content_rating ? 'filled' : '' }}">★</span>
                                    @endfor
                                    <span class="ms-2 text-muted">({{ $feedbackData->content_rating }}/5)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Organization Rating -->
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Organization & Coordination</strong>
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedbackData->organization_rating ? 'filled' : '' }}">★</span>
                                    @endfor
                                    <span class="ms-2 text-muted">({{ $feedbackData->organization_rating }}/5)</span>
                                </div>
                            </div>
                        </div>

                        @if($feedbackData->platform_rating)
                            <!-- Platform Rating -->
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Platform/Technology</strong>
                                    <div class="rating-display">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $feedbackData->platform_rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                        <span class="ms-2 text-muted">({{ $feedbackData->platform_rating }}/5)</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($feedbackData->venue_rating)
                            <!-- Venue Rating -->
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Venue & Facilities</strong>
                                    <div class="rating-display">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $feedbackData->venue_rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                        <span class="ms-2 text-muted">({{ $feedbackData->venue_rating }}/5)</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Comments Section -->
                    @if($feedbackData->comments)
                        <div class="mb-4">
                            <h5 class="mb-3">What You Liked Most</h5>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $feedbackData->comments }}</p>
                            </div>
                        </div>
                    @endif

                    @if($feedbackData->suggestions)
                        <div class="mb-4">
                            <h5 class="mb-3">Suggestions for Improvement</h5>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $feedbackData->suggestions }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Recommendation -->
                    <div class="mb-4">
                        <h5 class="mb-3">Would You Recommend This Event?</h5>
                        <div class="p-3 bg-light rounded">
                            @if($feedbackData->would_recommend)
                                <span class="badge bg-success fs-6">👍 Yes, I would recommend this event</span>
                            @else
                                <span class="badge bg-danger fs-6">👎 No, I would not recommend this event</span>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <strong>Note:</strong> Your feedback helps event organizers improve future events. Thank you for your valuable input!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-display {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.star {
    color: #ddd;
    font-size: 1.5rem;
}

.star.filled {
    color: #ffc107;
}
</style>
@endsection
