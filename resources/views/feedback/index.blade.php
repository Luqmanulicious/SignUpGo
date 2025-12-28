@extends('layouts.app')

@section('content')
<div class="feedback-index-page">
    <div class="container py-4">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <span class="title-icon">💬</span>
                Event Feedback
            </h1>
            <p class="page-subtitle">Share your experience and help us improve future events</p>
        </div>

        <!-- Events Grid -->
        <div class="events-grid">
            @forelse($registrations as $registration)
                @php
                    $event = $registration->event;
                    $hasFeedback = $registration->feedback !== null;
                @endphp

                <div class="event-card {{ $hasFeedback ? 'has-feedback' : '' }}">
                    <!-- Poster Section -->
                    <div class="poster-section">
                        @if($event->poster_url || $event->featured_image)
                            <img src="{{ $event->poster_url ?? $event->featured_image }}" 
                                 alt="{{ $event->title }}"
                                 class="poster-image">
                        @else
                            <div class="poster-placeholder">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span>No Poster</span>
                            </div>
                        @endif
                        
                        @if($hasFeedback)
                            <div class="status-badge submitted">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Feedback Submitted
                            </div>
                        @else
                            <div class="status-badge pending">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                Awaiting Feedback
                            </div>
                        @endif
                    </div>

                    <!-- Details Section -->
                    <div class="details-section">
                        <h3 class="event-title">{{ $event->title }}</h3>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span class="meta-text"><strong>Date:</strong> {{ $event->start_date->format('M d, Y') }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">🎭</span>
                                <span class="meta-text"><strong>Role:</strong> {{ ucfirst($registration->role) }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">✅</span>
                                <span class="meta-text"><strong>Checked In:</strong> {{ $registration->checked_in_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>

                        @if($hasFeedback)
                            <div class="feedback-info">
                                <div class="rating-row">
                                    <span class="rating-label">Your Rating:</span>
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $registration->feedback->overall_rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <span class="submit-date">Submitted {{ $registration->feedback->submitted_at->format('M d, Y') }}</span>
                            </div>
                            <a href="{{ route('feedback.show', $registration) }}" class="btn-action btn-view">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                View Feedback
                            </a>
                        @else
                            <a href="{{ route('feedback.create', $registration) }}" class="btn-action btn-submit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                Submit Feedback
                            </a>
                        @endif
                    </div>
                </div>

            @empty
                <div class="empty-state">
                    <div class="empty-icon">😊</div>
                    <h3 class="empty-title">No Events Available for Feedback</h3>
                    <p class="empty-text">You don't have any completed events yet. Feedback can only be submitted after you've checked in and the event has ended.</p>
                    <a href="{{ route('events.index') }}" class="btn-action btn-browse">
                        Browse Events
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
/* Page Base */
.feedback-index-page {
    background: #f1f5f9;
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* Page Header */
.page-header {
    text-align: center;
    margin-bottom: 2.5rem;
    padding-top: 1rem;
}

.page-title {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 2rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.title-icon {
    font-size: 1.75rem;
}

.page-subtitle {
    color: #64748b;
    font-size: 1rem;
    margin: 0;
}

/* Alerts */
.alert-custom {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
}

.alert-error {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #991b1b;
}

.alert-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.alert-success .alert-icon {
    background: #10b981;
    color: #fff;
}

.alert-error .alert-icon {
    background: #ef4444;
    color: #fff;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    opacity: 0.6;
}

.alert-close:hover {
    opacity: 1;
}

/* Events Grid */
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Event Card - Vertical Layout */
.event-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.event-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
}

.event-card.has-feedback {
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.15), 0 2px 4px -1px rgba(16, 185, 129, 0.08);
}

/* Poster Section */
.poster-section {
    position: relative;
    width: 100%;
    background: #f8fafc;
}

.poster-image {
    width: 100%;
    height: auto;
    display: block;
}

.poster-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    color: #94a3b8;
    font-size: 0.9rem;
}

/* Status Badge */
.status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.status-badge.submitted {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
}

.status-badge.pending {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff;
}

/* Details Section */
.details-section {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.event-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 1rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #475569;
}

.meta-icon {
    font-size: 0.9rem;
    width: 18px;
    text-align: center;
}

.meta-text strong {
    color: #64748b;
    font-weight: 600;
}

/* Feedback Info */
.feedback-info {
    background: #f0fdf4;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid #bbf7d0;
}

.rating-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.rating-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #166534;
}

.stars {
    display: flex;
    gap: 2px;
}

.star {
    font-size: 1rem;
    color: #d1d5db;
}

.star.filled {
    color: #fbbf24;
}

.submit-date {
    font-size: 0.8rem;
    color: #16a34a;
    font-style: italic;
}

/* Buttons */
.btn-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-top: auto;
}

.btn-submit {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    color: #fff;
}

.btn-view {
    background: #fff;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-view:hover {
    background: #667eea;
    color: #fff;
    transform: translateY(-2px);
}

.btn-browse {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.btn-browse:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    color: #fff;
}

/* Empty State */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #475569;
    margin: 0 0 0.5rem 0;
}

.empty-text {
    color: #64748b;
    font-size: 1rem;
    max-width: 400px;
    margin: 0 auto 1.5rem auto;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 640px) {
    .events-grid {
        grid-template-columns: 1fr;
        padding: 0 0.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
    }
}
</style>
@endsection
