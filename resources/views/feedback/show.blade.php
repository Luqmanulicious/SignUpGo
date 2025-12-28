@extends('layouts.app')

@section('content')
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
