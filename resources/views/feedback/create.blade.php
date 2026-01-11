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

<div class="container-fluid px-4 py-4">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('feedback.index') }}" 
           style="padding: 0.75rem 1.5rem; background: #6c7778; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
            ← Back to Event Feedback
        </a>
    </div>

    <div class="row justify-content-center mb-10">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                {{-- <div class="card-header bg-gradient-primary text-white py-3 mb-3">
                    <h4 class="mb-0">💬 Event Feedback</h4>
                </div> --}}
                
                <div class="card-body p-4 pt-2">
                    <!-- Event Info Header -->
                    <div class="event-info-header mb-4">
                        <h5 class="event-title mb-3">{{ $registration->event->title }}</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="info-label">📅 Date:</span>
                                    <span class="info-value">{{ $registration->event->start_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="info-label">🎭 Your Role:</span>
                                    <span class="info-value">{{ ucfirst($registration->role) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('feedback.store', $registration) }}">
                        @csrf

                        <!-- Rate Your Experience Section -->
                        <div class="section-header mb-4">
                            <h5 class="section-title">⭐ Rate Your Experience</h5>
                            <p class="text-muted small mb-0">Please rate the following aspects of the event</p>
                        </div>

                        <div class="ratings-grid mb-4">
                            <!-- Overall Rating -->
                            <div class="rating-box">
                                <div class="rating-label">Overall Experience <span class="text-danger">*</span></div>
                                <div class="rating-stars">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="overall_rating" value="{{ $i }}" id="overall_{{ $i }}" required>
                                        <label for="overall_{{ $i }}" class="star">★</label>
                                    @endfor
                                </div>
                                @error('overall_rating')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content Rating -->
                            <div class="rating-box">
                                <div class="rating-label">Content Quality <span class="text-danger">*</span></div>
                                <div class="rating-stars">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="content_rating" value="{{ $i }}" id="content_{{ $i }}" required>
                                        <label for="content_{{ $i }}" class="star">★</label>
                                    @endfor
                                </div>
                                @error('content_rating')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Organization Rating -->
                            <div class="rating-box">
                                <div class="rating-label">Organization & Coordination <span class="text-danger">*</span></div>
                                <div class="rating-stars">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="organization_rating" value="{{ $i }}" id="organization_{{ $i }}" required>
                                        <label for="organization_{{ $i }}" class="star">★</label>
                                    @endfor
                                </div>
                                @error('organization_rating')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(in_array($registration->event->delivery_mode, ['online', 'hybrid']))
                                <!-- Platform Rating -->
                                <div class="rating-box">
                                    <div class="rating-label">Platform/Technology</div>
                                    <div class="rating-stars">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="platform_rating" value="{{ $i }}" id="platform_{{ $i }}">
                                            <label for="platform_{{ $i }}" class="star">★</label>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                            
                            @if(in_array($registration->event->delivery_mode, ['face_to_face', 'hybrid']))
                                <!-- Venue Rating -->
                                <div class="rating-box">
                                    <div class="rating-label">Venue & Facilities</div>
                                    <div class="rating-stars">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="venue_rating" value="{{ $i }}" id="venue_{{ $i }}">
                                            <label for="venue_{{ $i }}" class="star">★</label>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Comments Section -->
                        <div class="section-header mb-4">
                            <h5 class="section-title">💭 Share Your Thoughts</h5>
                        </div>

                        <div class="comments-section mb-4">
                            <div class="comment-box">
                                <div class="comment-label">What did you like most about the event?</div>
                                <textarea name="comments" class="form-control" rows="5" placeholder="Share your thoughts...">{{ old('comments') }}</textarea>
                                <div class="form-text">Tell us what made this event memorable for you</div>
                                @error('comments')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="comment-box">
                                <div class="comment-label">What could be improved?</div>
                                <textarea name="suggestions" class="form-control" rows="5" placeholder="Your suggestions for improvement...">{{ old('suggestions') }}</textarea>
                                <div class="form-text">Help us make future events even better</div>
                                @error('suggestions')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="comment-box">
                                <div class="comment-label">System Feedback <span class="text-danger">*</span></div>
                                <textarea name="system_feedback" class="form-control" rows="5" placeholder="Share your experience with our registration and event management system..." required>{{ old('system_feedback') }}</textarea>
                                <div class="form-text">Tell us about your experience using our platform</div>
                                @error('system_feedback')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Recommendation Section -->
                        <div class="section-header mb-4">
                            <h5 class="section-title">👥 Recommendation</h5>
                        </div>

                        <div class="recommendation-box mb-4">
                            <div class="recommend-label">Would you recommend this event to others? <span class="text-danger">*</span></div>
                            <div class="recommendation-options">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="would_recommend" id="recommend_yes" value="1" required>
                                    <label class="form-check-label" for="recommend_yes">
                                        <span class="recommend-icon">👍</span>
                                        <span class="recommend-text">Yes, I would recommend</span>
                                    </label>
                                </div>
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="would_recommend" id="recommend_no" value="0" required>
                                    <label class="form-check-label" for="recommend_no">
                                        <span class="recommend-icon">👎</span>
                                        <span class="recommend-text">No, I would not recommend</span>
                                    </label>
                                </div>
                            </div>
                            @error('would_recommend')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="submit-button d-flex gap-3 border-top custom-submit">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                ✓ Submit Feedback
                            </button>
                            <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Gradient Header */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Event Info Header */
.event-info-header {
    margin-top: 0.5rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid #667eea;
}

.event-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.95rem;
}

.info-value {
    color: #2d3748;
    font-size: 0.95rem;
}

/* Section Headers */
.section-header {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.section-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.15rem;
    margin-bottom: 0.25rem;
}

/* Ratings Grid */
.ratings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.rating-box {
    background: #ffffff;
    padding: 2rem;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    text-align: center;
    transition: all 0.3s ease;
}

.rating-box:hover {
    border-color: #cbd5e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.rating-label {
    display: block;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    text-align: center;
}

/* Star Rating System */
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    gap: 0.75rem;
    align-items: center;
    justify-content: center;
}

.rating-stars input[type="radio"] {
    display: none;
}

.rating-stars .star {
    font-size: 3rem;
    color: #e2e8f0;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.rating-stars .star:hover,
.rating-stars .star:hover ~ .star {
    color: #fbbf24;
    transform: scale(1.15);
}

.rating-stars input[type="radio"]:checked ~ .star {
    color: #fbbf24;
    filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.4));
}

/* Comments Section */
.comments-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.comment-box {
    background: #ffffff;
    padding: 2rem;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.comment-box:hover {
    border-color: #cbd5e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.comment-label {
    display: block;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1.25rem;
    font-size: 1.1rem;
}

/* Form Controls */
.comment-box .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8fafc;
    width: 100%;
    resize: vertical;
}

.comment-box .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    background: #ffffff;
    outline: none;
}

.comment-box .form-control::placeholder {
    color: #a0aec0;
    font-style: italic;
}

.comment-box .form-text {
    color: #718096;
    font-size: 0.875rem;
    margin-top: 0.75rem;
    font-style: italic;
    display: block;
}

/* Recommendation Box */
.recommendation-box {
    background: #ffffff;
    padding: 2rem;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.recommendation-box:hover {
    border-color: #cbd5e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.recommend-label {
    display: block;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    text-align: center;
}

.recommendation-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}

.form-check-custom {
    width: 100%;
}

.form-check-custom .form-check-input {
    display: none;
}

.form-check-custom .form-check-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.875rem;
    padding: 1.25rem 1.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
    width: 100%;
}

.form-check-custom .form-check-label:hover {
    border-color: #667eea;
    background: #f7fafc;
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.form-check-custom .form-check-input:checked ~ .form-check-label {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    font-weight: 600;
}

.recommend-icon {
    font-size: 2rem;
    line-height: 1;
}

.recommend-text {
    font-weight: 600;
    color: #2d3748;
    font-size: 1.05rem;
}

/* Buttons */
.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #cbd5e0;
    color: #4a5568;
}

.btn-outline-secondary:hover {
    background: #f7fafc;
    border-color: #a0aec0;
    color: #2d3748;
}

/* Card Styling */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

/* Responsive */
@media (max-width: 768px) {
    .ratings-grid {
        grid-template-columns: 1fr;
    }
    
    .rating-stars .star {
        font-size: 2.5rem;
        gap: 0.5rem;
    }
    
    .recommendation-options {
        grid-template-columns: 1fr;
    }
    
    .event-info-header {
        padding: 1.25rem;
    }
    
    .rating-box,
    .comment-box,
    .recommendation-box {
        padding: 1.5rem;
    }

    .custom-submit {
    margin-top: 5rem; /* adjust as needed */
    }
    
</style>
@endsection
