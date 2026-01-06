@extends('layouts.app')

@section('title', $event->title . ' | SignUpGo')

@section('styles')
    <style>
        .container {
            max-width: 1200px;
            width: 100%;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.6rem 1.2rem;
            background: #6c7778;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .back-link:hover {
            background: #7f8c8d;
            transform: translateY(-1px);
            text-decoration: none;
        }

        .event-header {
            background: white;
            padding: 2rem;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            margin-bottom: 0;
        }

        .event-poster {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: #f5f5f5;
        }

        .event-poster-placeholder {
            width: 100%;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .event-header h1 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
            font-size: 2rem;
        }

        .event-category {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            background: #3498db;
            color: white;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .meta {
            color: #7f8c8d;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .event-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-top: 0;
        }

        .event-main {
            background: white;
            padding: 2rem;
            border-radius: 0 0 0 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .event-sidebar {
            background: white;
            padding: 2rem;
            border-radius: 0 0 8px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .info-section {
            margin-bottom: 2rem;
        }

        .info-section h3 {
            color: #2c3e50;
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 0.5rem;
        }

        .info-item {
            display: flex;
            align-items: start;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .info-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #555;
            margin: 0;
        }

        .highlight-box {
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .highlight-box h4 {
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .highlight-box .value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .slots-info {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            background: #e8f5e9;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .slots-info .label {
            font-size: 0.85rem;
            color: #2e7d32;
            margin-bottom: 0.25rem;
        }

        .slots-info .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1b5e20;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            color: #555;
        }

        .contact-item a {
            color: #3498db;
            text-decoration: none;
            word-break: break-all;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .card hr {
            border: none;
            border-top: 1px solid #ecf0f1;
            margin: 1.5rem 0;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #3498db;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            text-align: center;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .event-grid {
                grid-template-columns: 1fr;
            }

            .event-main,
            .event-sidebar {
                border-radius: 0 0 8px 8px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <a href="{{ route('events.index') }}" class="back-link">← Back to events</a>

        <div class="event-header">
            @if ($event->poster_url)
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="event-poster"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="event-poster-placeholder" style="display: none;">📅</div>
            @else
                <div class="event-poster-placeholder">📅</div>
            @endif

            @if ($event->category)
                <span class="event-category">{{ $event->category->name ?? 'Event' }}</span>
            @endif

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
            <p class="meta">📅 {{ $start }} @if ($start !== $end)
                    - {{ $end }}
                @endif • 📍 {{ $venue }}</p>

            @if ($event->status)
                <span class="status-badge status-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
            @endif
        </div>

        <div class="event-grid">
            <div class="event-main">
                <div class="info-section">
                    <h3>About This Event</h3>
                    <div style="line-height: 1.6; color: #555;">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                @if ($event->requirements)
                    <div class="info-section">
                        <h3>Requirements</h3>
                        <div style="line-height: 1.6; color: #555;">
                            @if (is_array($event->requirements))
                                <ul style="margin: 0; padding-left: 1.5rem;">
                                    @foreach ($event->requirements as $requirement)
                                        <li>{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            @else
                                {{ $event->requirements }}
                            @endif
                        </div>
                    </div>
                @endif

                <div class="info-section">
                    <h3>Event Details</h3>

                    @if ($event->delivery_mode)
                        <div class="info-item">
                            <div class="info-icon">🌐</div>
                            <div class="info-content">
                                <div class="info-label">Event Delivery Mode</div>
                                <p class="info-value">
                                    @if ($event->delivery_mode === 'face_to_face')
                                        📍 Face-to-Face Event
                                    @elseif($event->delivery_mode === 'online')
                                        💻 Online Event
                                    @elseif($event->delivery_mode === 'hybrid')
                                        🔄 Hybrid Event (Face-to-Face + Online)
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $event->delivery_mode)) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Innovation Categories --}}
                    @php
                        $innovationCategories = $event->innovation_categories;
                        if (is_string($innovationCategories)) {
                            $innovationCategories = json_decode($innovationCategories, true) ?? [];
                        }
                    @endphp
                    @if (is_array($innovationCategories) && count($innovationCategories) > 0)
                        <div class="info-item">
                            <div class="info-icon">🏷️</div>
                            <div class="info-content">
                                <div class="info-label">Innovation Categories</div>
                                <p class="info-value">
                                    @foreach ($innovationCategories as $category)
                                        <span
                                            style="display: inline-block; background: #e8f5e9; color: #2e7d32; padding: 0.25rem 0.75rem; border-radius: 15px; margin: 0.25rem; font-size: 0.9rem;">{{ $category }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Innovation Themes --}}
                    @php
                        $innovationTheme = $event->innovation_theme;
                        if (is_string($innovationTheme)) {
                            $innovationTheme = json_decode($innovationTheme, true) ?? [];
                        }
                    @endphp
                    @if (is_array($innovationTheme) && count($innovationTheme) > 0)
                        <div class="info-item">
                            <div class="info-icon">💡</div>
                            <div class="info-content">
                                <div class="info-label">Innovation Themes</div>
                                <p class="info-value">
                                    @foreach ($innovationTheme as $theme)
                                        <span
                                            style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 0.25rem 0.75rem; border-radius: 15px; margin: 0.25rem; font-size: 0.9rem;">{{ $theme }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Conference Themes --}}
                    @php
                        $conferenceTheme = $event->conference_theme;
                        if (is_string($conferenceTheme)) {
                            $conferenceTheme = json_decode($conferenceTheme, true) ?? [];
                        }
                    @endphp
                    @if (is_array($conferenceTheme) && count($conferenceTheme) > 0)
                        <div class="info-item">
                            <div class="info-icon">📚</div>
                            <div class="info-content">
                                <div class="info-label">Conference Themes</div>
                                <p class="info-value">
                                    @foreach ($conferenceTheme as $theme)
                                        <span
                                            style="display: inline-block; background: #fff3e0; color: #e65100; padding: 0.25rem 0.75rem; border-radius: 15px; margin: 0.25rem; font-size: 0.9rem;">{{ $theme }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-icon">📅</div>
                        <div class="info-content">
                            <div class="info-label">Start Date & Time</div>
                            <p class="info-value">
                                @php
                                    try {
                                        $startDateTime = $event->start_date
                                            ? \Carbon\Carbon::parse($event->start_date)->format('l, F d, Y')
                                            : 'TBA';
                                        if ($event->start_time) {
                                            $startTime = \Carbon\Carbon::parse($event->start_time)->format('h:i A');
                                            $startDateTime .= ' at ' . $startTime;
                                        }
                                    } catch (\Exception $e) {
                                        $startDateTime = 'TBA';
                                    }
                                @endphp
                                {{ $startDateTime }}
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">🏁</div>
                        <div class="info-content">
                            <div class="info-label">End Date & Time</div>
                            <p class="info-value">
                                @php
                                    try {
                                        $endDateTime = $event->end_date
                                            ? \Carbon\Carbon::parse($event->end_date)->format('l, F d, Y')
                                            : 'TBA';
                                        if ($event->end_time) {
                                            $endTime = \Carbon\Carbon::parse($event->end_time)->format('h:i A');
                                            $endDateTime .= ' at ' . $endTime;
                                        }
                                    } catch (\Exception $e) {
                                        $endDateTime = 'TBA';
                                    }
                                @endphp
                                {{ $endDateTime }}
                            </p>
                        </div>
                    </div>

                    @if (in_array($event->delivery_mode, ['face_to_face', 'hybrid']))
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-content">
                                <div class="info-label">Location</div>
                                <p class="info-value">
                                    {{ $event->venue_name ?: 'Online Event' }}
                                    @if ($event->venue_address)
                                        <br>{{ $event->venue_address }}
                                    @endif
                                    @if ($event->city || $event->state || $event->country)
                                        <br>
                                        @if ($event->city)
                                            {{ $event->city }}
                                        @endif
                                        @if ($event->state)
                                            , {{ $event->state }}
                                        @endif
                                        @if ($event->country)
                                            - {{ $event->country }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($event->registration_deadline)
                        <div class="info-item">
                            <div class="info-icon">⏰</div>
                            <div class="info-content">
                                <div class="info-label">Registration Deadline</div>
                                <p class="info-value">
                                    @php
                                        try {
                                            $deadline = \Carbon\Carbon::parse($event->registration_deadline)->format(
                                                'F d, Y h:i A',
                                            );
                                        } catch (\Exception $e) {
                                            $deadline = 'Not specified';
                                        }
                                    @endphp
                                    {{ $deadline }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @php
                        $hasPaperDeadline = false;
                        $paperDeadlineText = '';

                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_paper_submission_deadline) {
                            $hasPaperDeadline = true;
                            $paperDeadlineText = \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'online' && $event->online_paper_submission_deadline) {
                            $hasPaperDeadline = true;
                            $paperDeadlineText = \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'hybrid') {
                            $hasPaperDeadline = true;
                            $f2fDeadline = $event->f2f_paper_submission_deadline
                                ? \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlineDeadline = $event->online_paper_submission_deadline
                                ? \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('F d, Y h:i A')
                                : null;

                            if ($f2fDeadline && $onlineDeadline) {
                                $paperDeadlineText = 'F2F: ' . $f2fDeadline . '<br>Online: ' . $onlineDeadline;
                            } elseif ($f2fDeadline) {
                                $paperDeadlineText = 'F2F: ' . $f2fDeadline;
                            } elseif ($onlineDeadline) {
                                $paperDeadlineText = 'Online: ' . $onlineDeadline;
                            } else {
                                $hasPaperDeadline = false;
                            }
                        } else {
                            // For events without delivery_mode set (e.g., conference events)
                            $f2fDeadline = $event->f2f_paper_submission_deadline
                                ? \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlineDeadline = $event->online_paper_submission_deadline
                                ? \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('F d, Y h:i A')
                                : null;

                            if ($f2fDeadline && $onlineDeadline) {
                                $hasPaperDeadline = true;
                                $paperDeadlineText = 'F2F: ' . $f2fDeadline . '<br>Online: ' . $onlineDeadline;
                            } elseif ($f2fDeadline) {
                                $hasPaperDeadline = true;
                                $paperDeadlineText = $f2fDeadline;
                            } elseif ($onlineDeadline) {
                                $hasPaperDeadline = true;
                                $paperDeadlineText = $onlineDeadline;
                            }
                        }
                    @endphp

                    @if ($hasPaperDeadline)
                        <div class="info-item">
                            <div class="info-icon">📝</div>
                            <div class="info-content">
                                <div class="info-label">Paper Submission Deadline</div>
                                <p class="info-value">{!! $paperDeadlineText !!}</p>
                            </div>
                        </div>
                    @endif

                    @php
                        $hasReviewerDeadline = false;
                        $reviewerDeadlineText = '';

                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_reviewer_registration_deadline) {
                            $hasReviewerDeadline = true;
                            $reviewerDeadlineText = \Carbon\Carbon::parse(
                                $event->f2f_reviewer_registration_deadline,
                            )->format('F d, Y h:i A');
                        } elseif ($event->delivery_mode === 'online' && $event->online_reviewer_registration_deadline) {
                            $hasReviewerDeadline = true;
                            $reviewerDeadlineText = \Carbon\Carbon::parse(
                                $event->online_reviewer_registration_deadline,
                            )->format('F d, Y h:i A');
                        } elseif ($event->delivery_mode === 'hybrid') {
                            $hasReviewerDeadline = true;
                            $f2fReviewerDeadline = $event->f2f_reviewer_registration_deadline
                                ? \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format(
                                    'F d, Y h:i A',
                                )
                                : null;
                            $onlineReviewerDeadline = $event->online_reviewer_registration_deadline
                                ? \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format(
                                    'F d, Y h:i A',
                                )
                                : null;

                            if ($f2fReviewerDeadline && $onlineReviewerDeadline) {
                                $reviewerDeadlineText =
                                    'F2F: ' . $f2fReviewerDeadline . '<br>Online: ' . $onlineReviewerDeadline;
                            } elseif ($f2fReviewerDeadline) {
                                $reviewerDeadlineText = 'F2F: ' . $f2fReviewerDeadline;
                            } elseif ($onlineReviewerDeadline) {
                                $reviewerDeadlineText = 'Online: ' . $onlineReviewerDeadline;
                            } else {
                                $hasReviewerDeadline = false;
                            }
                        } else {
                            // For events without delivery_mode set
                            $f2fReviewerDeadline = $event->f2f_reviewer_registration_deadline
                                ? \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format(
                                    'F d, Y h:i A',
                                )
                                : null;
                            $onlineReviewerDeadline = $event->online_reviewer_registration_deadline
                                ? \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format(
                                    'F d, Y h:i A',
                                )
                                : null;

                            if ($f2fReviewerDeadline && $onlineReviewerDeadline) {
                                $hasReviewerDeadline = true;
                                $reviewerDeadlineText =
                                    'F2F: ' . $f2fReviewerDeadline . '<br>Online: ' . $onlineReviewerDeadline;
                            } elseif ($f2fReviewerDeadline) {
                                $hasReviewerDeadline = true;
                                $reviewerDeadlineText = $f2fReviewerDeadline;
                            } elseif ($onlineReviewerDeadline) {
                                $hasReviewerDeadline = true;
                                $reviewerDeadlineText = $onlineReviewerDeadline;
                            }
                        }
                    @endphp

                    @if ($hasReviewerDeadline)
                        <div class="info-item">
                            <div class="info-icon">👨‍🏫</div>
                            <div class="info-content">
                                <div class="info-label">Reviewer Registration Deadline</div>
                                <p class="info-value">{!! $reviewerDeadlineText !!}</p>
                            </div>
                        </div>
                    @endif

                    @php
                        $hasJuryDeadline = false;
                        $juryDeadlineText = '';

                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_jury_registration_deadline) {
                            $hasJuryDeadline = true;
                            $juryDeadlineText = \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'online' && $event->online_jury_registration_deadline) {
                            $hasJuryDeadline = true;
                            $juryDeadlineText = \Carbon\Carbon::parse(
                                $event->online_jury_registration_deadline,
                            )->format('F d, Y h:i A');
                        } elseif ($event->delivery_mode === 'hybrid') {
                            $hasJuryDeadline = true;
                            $f2fJuryDeadline = $event->f2f_jury_registration_deadline
                                ? \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlineJuryDeadline = $event->online_jury_registration_deadline
                                ? \Carbon\Carbon::parse($event->online_jury_registration_deadline)->format(
                                    'F d, Y h:i A',
                                )
                                : null;

                            if ($f2fJuryDeadline && $onlineJuryDeadline) {
                                $juryDeadlineText = 'F2F: ' . $f2fJuryDeadline . '<br>Online: ' . $onlineJuryDeadline;
                            } elseif ($f2fJuryDeadline) {
                                $juryDeadlineText = 'F2F: ' . $f2fJuryDeadline;
                            } elseif ($onlineJuryDeadline) {
                                $juryDeadlineText = 'Online: ' . $onlineJuryDeadline;
                            } else {
                                $hasJuryDeadline = false;
                            }
                        } else {
                            // For events without delivery_mode set
                            $f2fJuryDeadline = $event->f2f_jury_registration_deadline
                                ? \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlineJuryDeadline = $event->online_jury_registration_deadline
                                ? \Carbon\Carbon::parse($event->online_jury_registration_deadline)->format(
                                    'F d, Y h:i A',
                                )
                                : null;

                            if ($f2fJuryDeadline && $onlineJuryDeadline) {
                                $hasJuryDeadline = true;
                                $juryDeadlineText = 'F2F: ' . $f2fJuryDeadline . '<br>Online: ' . $onlineJuryDeadline;
                            } elseif ($f2fJuryDeadline) {
                                $hasJuryDeadline = true;
                                $juryDeadlineText = $f2fJuryDeadline;
                            } elseif ($onlineJuryDeadline) {
                                $hasJuryDeadline = true;
                                $juryDeadlineText = $onlineJuryDeadline;
                            }
                        }
                    @endphp

                    @if ($hasJuryDeadline)
                        <div class="info-item">
                            <div class="info-icon">🎓</div>
                            <div class="info-content">
                                <div class="info-label">Jury Registration Deadline</div>
                                <p class="info-value">{!! $juryDeadlineText !!}</p>
                            </div>
                        </div>
                    @endif

                    @php
                        $hasReviewDeadline = false;
                        $reviewDeadlineText = '';

                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_review_deadline) {
                            $hasReviewDeadline = true;
                            $reviewDeadlineText = \Carbon\Carbon::parse($event->f2f_review_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'online' && $event->online_review_deadline) {
                            $hasReviewDeadline = true;
                            $reviewDeadlineText = \Carbon\Carbon::parse($event->online_review_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'hybrid') {
                            $hasReviewDeadline = true;
                            $f2fReviewDeadline = $event->f2f_review_deadline
                                ? \Carbon\Carbon::parse($event->f2f_review_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlineReviewDeadline = $event->online_review_deadline
                                ? \Carbon\Carbon::parse($event->online_review_deadline)->format('F d, Y h:i A')
                                : null;

                            if ($f2fReviewDeadline && $onlineReviewDeadline) {
                                $reviewDeadlineText =
                                    'F2F: ' . $f2fReviewDeadline . '<br>Online: ' . $onlineReviewDeadline;
                            } elseif ($f2fReviewDeadline) {
                                $reviewDeadlineText = 'F2F: ' . $f2fReviewDeadline;
                            } elseif ($onlineReviewDeadline) {
                                $reviewDeadlineText = 'Online: ' . $onlineReviewDeadline;
                            } else {
                                $hasReviewDeadline = false;
                            }
                        } else {
                            // For events without delivery_mode set
                            $f2fReviewDeadline = $event->f2f_review_deadline
                                ? \Carbon\Carbon::parse($event->f2f_review_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlineReviewDeadline = $event->online_review_deadline
                                ? \Carbon\Carbon::parse($event->online_review_deadline)->format('F d, Y h:i A')
                                : null;

                            if ($f2fReviewDeadline && $onlineReviewDeadline) {
                                $hasReviewDeadline = true;
                                $reviewDeadlineText =
                                    'F2F: ' . $f2fReviewDeadline . '<br>Online: ' . $onlineReviewDeadline;
                            } elseif ($f2fReviewDeadline) {
                                $hasReviewDeadline = true;
                                $reviewDeadlineText = $f2fReviewDeadline;
                            } elseif ($onlineReviewDeadline) {
                                $hasReviewDeadline = true;
                                $reviewDeadlineText = $onlineReviewDeadline;
                            }
                        }
                    @endphp

                    @if ($hasReviewDeadline)
                        <div class="info-item">
                            <div class="info-icon">📋</div>
                            <div class="info-content">
                                <div class="info-label">Review Deadline</div>
                                <p class="info-value">{!! $reviewDeadlineText !!}</p>
                            </div>
                        </div>
                    @endif

                    @php
                        $hasAcceptanceDate = false;
                        $acceptanceDateText = '';

                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_acceptance_notification_date) {
                            $hasAcceptanceDate = true;
                            $acceptanceDateText = \Carbon\Carbon::parse(
                                $event->f2f_acceptance_notification_date,
                            )->format('F d, Y h:i A');
                        } elseif ($event->delivery_mode === 'online' && $event->online_acceptance_notification_date) {
                            $hasAcceptanceDate = true;
                            $acceptanceDateText = \Carbon\Carbon::parse(
                                $event->online_acceptance_notification_date,
                            )->format('F d, Y h:i A');
                        } elseif ($event->delivery_mode === 'hybrid') {
                            $hasAcceptanceDate = true;
                            $f2fAcceptanceDate = $event->f2f_acceptance_notification_date
                                ? \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format(
                                    'F d, Y h:i A',
                                )
                                : null;
                            $onlineAcceptanceDate = $event->online_acceptance_notification_date
                                ? \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format(
                                    'F d, Y h:i A',
                                )
                                : null;

                            if ($f2fAcceptanceDate && $onlineAcceptanceDate) {
                                $acceptanceDateText =
                                    'F2F: ' . $f2fAcceptanceDate . '<br>Online: ' . $onlineAcceptanceDate;
                            } elseif ($f2fAcceptanceDate) {
                                $acceptanceDateText = 'F2F: ' . $f2fAcceptanceDate;
                            } elseif ($onlineAcceptanceDate) {
                                $acceptanceDateText = 'Online: ' . $onlineAcceptanceDate;
                            } else {
                                $hasAcceptanceDate = false;
                            }
                        } else {
                            // For events without delivery_mode set
                            $f2fAcceptanceDate = $event->f2f_acceptance_notification_date
                                ? \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format(
                                    'F d, Y h:i A',
                                )
                                : null;
                            $onlineAcceptanceDate = $event->online_acceptance_notification_date
                                ? \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format(
                                    'F d, Y h:i A',
                                )
                                : null;

                            if ($f2fAcceptanceDate && $onlineAcceptanceDate) {
                                $hasAcceptanceDate = true;
                                $acceptanceDateText =
                                    'F2F: ' . $f2fAcceptanceDate . '<br>Online: ' . $onlineAcceptanceDate;
                            } elseif ($f2fAcceptanceDate) {
                                $hasAcceptanceDate = true;
                                $acceptanceDateText = $f2fAcceptanceDate;
                            } elseif ($onlineAcceptanceDate) {
                                $hasAcceptanceDate = true;
                                $acceptanceDateText = $onlineAcceptanceDate;
                            }
                        }
                    @endphp

                    @if ($hasAcceptanceDate)
                        <div class="info-item">
                            <div class="info-icon">📢</div>
                            <div class="info-content">
                                <div class="info-label">Acceptance Notification Date</div>
                                <p class="info-value">{!! $acceptanceDateText !!}</p>
                            </div>
                        </div>
                    @endif

                    @php
                        $hasPaymentDeadline = false;
                        $paymentDeadlineText = '';

                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_payment_deadline) {
                            $hasPaymentDeadline = true;
                            $paymentDeadlineText = \Carbon\Carbon::parse($event->f2f_payment_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'online' && $event->online_payment_deadline) {
                            $hasPaymentDeadline = true;
                            $paymentDeadlineText = \Carbon\Carbon::parse($event->online_payment_deadline)->format(
                                'F d, Y h:i A',
                            );
                        } elseif ($event->delivery_mode === 'hybrid') {
                            $hasPaymentDeadline = true;
                            $f2fPaymentDeadline = $event->f2f_payment_deadline
                                ? \Carbon\Carbon::parse($event->f2f_payment_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlinePaymentDeadline = $event->online_payment_deadline
                                ? \Carbon\Carbon::parse($event->online_payment_deadline)->format('F d, Y h:i A')
                                : null;

                            if ($f2fPaymentDeadline && $onlinePaymentDeadline) {
                                $paymentDeadlineText =
                                    'F2F: ' . $f2fPaymentDeadline . '<br>Online: ' . $onlinePaymentDeadline;
                            } elseif ($f2fPaymentDeadline) {
                                $paymentDeadlineText = 'F2F: ' . $f2fPaymentDeadline;
                            } elseif ($onlinePaymentDeadline) {
                                $paymentDeadlineText = 'Online: ' . $onlinePaymentDeadline;
                            } else {
                                $hasPaymentDeadline = false;
                            }
                        } else {
                            // For events without delivery_mode set
                            $f2fPaymentDeadline = $event->f2f_payment_deadline
                                ? \Carbon\Carbon::parse($event->f2f_payment_deadline)->format('F d, Y h:i A')
                                : null;
                            $onlinePaymentDeadline = $event->online_payment_deadline
                                ? \Carbon\Carbon::parse($event->online_payment_deadline)->format('F d, Y h:i A')
                                : null;

                            if ($f2fPaymentDeadline && $onlinePaymentDeadline) {
                                $hasPaymentDeadline = true;
                                $paymentDeadlineText =
                                    'F2F: ' . $f2fPaymentDeadline . '<br>Online: ' . $onlinePaymentDeadline;
                            } elseif ($f2fPaymentDeadline) {
                                $hasPaymentDeadline = true;
                                $paymentDeadlineText = $f2fPaymentDeadline;
                            } elseif ($onlinePaymentDeadline) {
                                $hasPaymentDeadline = true;
                                $paymentDeadlineText = $onlinePaymentDeadline;
                            }
                        }
                    @endphp

                    @if ($hasPaymentDeadline)
                        <div class="info-item">
                            <div class="info-icon">💳</div>
                            <div class="info-content">
                                <div class="info-label">Payment Deadline</div>
                                <p class="info-value">{!! $paymentDeadlineText !!}</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($event->contact_email || $event->contact_phone || $event->website_url)
                    <div class="info-section">
                        <h3>Contact Information</h3>

                        @if ($event->contact_email)
                            <div class="contact-item">
                                <span style="margin-right: 0.5rem;">📧</span>
                                <a href="mailto:{{ $event->contact_email }}">{{ $event->contact_email }}</a>
                            </div>
                        @endif

                        @if ($event->contact_phone)
                            <div class="contact-item">
                                <span style="margin-right: 0.5rem;">📱</span>
                                <a href="tel:{{ $event->contact_phone }}">{{ $event->contact_phone }}</a>
                            </div>
                        @endif

                        @if ($event->website_url)
                            <div class="contact-item">
                                <span style="margin-right: 0.5rem;">🌐</span>
                                <a href="{{ $event->website_url }}" target="_blank"
                                    rel="noopener">{{ $event->website_url }}</a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="event-sidebar">
                <div class="highlight-box">
                    <h4>Registration Fee</h4>
                    <p class="value">
                        @php
                            $isFree = false;
                            try {
                                $isFree = (bool) $event->is_free;
                            } catch (\Exception $e) {
                                $isFree = false;
                            }
                        @endphp
                        @if ($isFree)
                            FREE
                        @else
                            {{ $event->currency ?? 'RM' }} {{ number_format($event->registration_fee ?? 0, 2) }}
                        @endif
                    </p>
                </div>

                @if ($event->max_participants)
                    <div class="slots-info">
                        <div>
                            <div class="label">Total Capacity</div>
                            <div class="value">{{ $event->max_participants }}</div>
                        </div>
                    </div>
                @else
                    <div
                        style="padding: 1rem; background: #e8f5e9; border-radius: 6px; margin-bottom: 1rem; text-align: center;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: #1b5e20;">♾️ Unlimited Slots</div>
                        <div style="font-size: 0.85rem; color: #2e7d32; margin-top: 0.25rem;">Everyone can join!</div>
                    </div>
                @endif

                @php
                    // Get all user registrations for this event (can be multiple roles)
                    $userRegistrations = \App\Models\EventRegistration::where('user_id', Auth::id())
                        ->where('event_id', $event->id)
                        ->get();

                    $availableRoles = $event->available_roles;
                    $registeredRoles = $userRegistrations->pluck('role')->toArray();
                    $remainingRoles = array_diff($availableRoles, $registeredRoles);
                @endphp

                @if ($userRegistrations->count() > 0)
                    {{-- Show all existing registrations --}}
                    @foreach ($userRegistrations as $registration)
                        @if ($registration->status === 'pending')
                            <div
                                style="padding: 1rem; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 6px; margin-bottom: 1rem;">
                                <strong>⏳ {{ ucfirst($registration->role) }} - Application Pending</strong>
                                <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.9rem;">
                                    Your {{ ucfirst($registration->role) }} application is pending approval.
                                </p>
                            </div>
                        @elseif($registration->status === 'approved' || $registration->status === 'confirmed')
                            <div
                                style="padding: 1rem; background: #d4edda; border-left: 4px solid #28a745; border-radius: 6px; margin-bottom: 1rem;">
                                <strong>✓ Registered as {{ ucfirst($registration->role) }}</strong>
                                <p style="margin: 0.5rem 0 0 0; color: #155724; font-size: 0.9rem;">
                                    You are confirmed as <strong>{{ ucfirst($registration->role) }}</strong>.
                                </p>
                            </div>
                        @elseif($registration->status === 'rejected')
                            <div
                                style="padding: 1rem; background: #f8d7da; border-left: 4px solid #dc3545; border-radius: 6px; margin-bottom: 1rem;">
                                <strong>✗ {{ ucfirst($registration->role) }} - Application Rejected</strong>
                                @if ($registration->rejected_reason)
                                    <p style="margin: 0.5rem 0 0 0; color: #721c24; font-size: 0.9rem;">
                                        Reason: {{ $registration->rejected_reason }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endforeach

                    {{-- Show buttons for remaining roles if registration is still open --}}
                    @if ($event->can_register && count($remainingRoles) > 0)
                        <div style="margin: 1rem 0;">
                            <h4 style="color: #2c3e50; margin: 0 0 0.75rem 0; font-size: 1rem; text-align: center;">
                                Register for additional role:</h4>
                            @foreach ($remainingRoles as $role)
                                @php
                                    $now = now();
                                    $deadlinePassed = false;
                                    $deadlineText = '';
                                    $isHybrid = $event->delivery_mode === 'hybrid';
                                    $f2fAvailable = false;
                                    $onlineAvailable = false;
                                    $f2fDeadlineText = '';
                                    $onlineDeadlineText = '';

                                    // Check deadline for participant role
                                    if ($role === 'participant') {
                                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_paper_submission_deadline) {
                                            if ($now->gt($event->f2f_paper_submission_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->f2f_paper_submission_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'online' && $event->online_paper_submission_deadline) {
                                            if ($now->gt($event->online_paper_submission_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->online_paper_submission_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'hybrid') {
                                            $f2fPassed =
                                                $event->f2f_paper_submission_deadline && $now->gt($event->f2f_paper_submission_deadline);
                                            $onlinePassed =
                                                $event->online_paper_submission_deadline &&
                                                $now->gt($event->online_paper_submission_deadline);
                                            $f2fAvailable = !$f2fPassed;
                                            $onlineAvailable = !$onlinePassed;
                                            $f2fDeadlineText = $event->f2f_paper_submission_deadline
                                                ? \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format(
                                                    'M d, Y h:i A',
                                                )
                                                : '';
                                            $onlineDeadlineText = $event->online_paper_submission_deadline
                                                ? \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format(
                                                    'M d, Y h:i A',
                                                )
                                                : '';
                                            if ($f2fPassed && $onlinePassed) {
                                                $deadlinePassed = true;
                                                $deadlineText = 'All deadlines passed';
                                            }
                                        } else {
                                            $f2fPassed =
                                                $event->f2f_paper_submission_deadline && $now->gt($event->f2f_paper_submission_deadline);
                                            $onlinePassed =
                                                $event->online_paper_submission_deadline &&
                                                $now->gt($event->online_paper_submission_deadline);
                                            if ($f2fPassed || $onlinePassed) {
                                                $deadlinePassed = true;
                                                if ($f2fPassed && $event->f2f_paper_submission_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->f2f_paper_submission_deadline,
                                                    )->format('M d, Y h:i A');
                                                } elseif ($onlinePassed && $event->online_paper_submission_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->online_paper_submission_deadline,
                                                    )->format('M d, Y h:i A');
                                                }
                                            }
                                        }
                                    }

                                    // Check deadline for reviewer role
                                    if ($role === 'reviewer') {
                                        if (
                                            $event->delivery_mode === 'face_to_face' &&
                                            $event->f2f_reviewer_registration_deadline
                                        ) {
                                            if ($now->gt($event->f2f_reviewer_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->f2f_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif (
                                            $event->delivery_mode === 'online' &&
                                            $event->online_reviewer_registration_deadline
                                        ) {
                                            if ($now->gt($event->online_reviewer_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->online_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'hybrid') {
                                            $f2fPassed =
                                                $event->f2f_reviewer_registration_deadline &&
                                                $now->gt($event->f2f_reviewer_registration_deadline);
                                            $onlinePassed =
                                                $event->online_reviewer_registration_deadline &&
                                                $now->gt($event->online_reviewer_registration_deadline);
                                            $f2fAvailable = !$f2fPassed;
                                            $onlineAvailable = !$onlinePassed;
                                            $f2fDeadlineText = $event->f2f_reviewer_registration_deadline
                                                ? \Carbon\Carbon::parse(
                                                    $event->f2f_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A')
                                                : '';
                                            $onlineDeadlineText = $event->online_reviewer_registration_deadline
                                                ? \Carbon\Carbon::parse(
                                                    $event->online_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A')
                                                : '';
                                            if ($f2fPassed && $onlinePassed) {
                                                $deadlinePassed = true;
                                                $deadlineText = 'All deadlines passed';
                                            }
                                        } else {
                                            $f2fPassed =
                                                $event->f2f_reviewer_registration_deadline &&
                                                $now->gt($event->f2f_reviewer_registration_deadline);
                                            $onlinePassed =
                                                $event->online_reviewer_registration_deadline &&
                                                $now->gt($event->online_reviewer_registration_deadline);
                                            if ($f2fPassed || $onlinePassed) {
                                                $deadlinePassed = true;
                                                if ($f2fPassed && $event->f2f_reviewer_registration_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->f2f_reviewer_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                } elseif (
                                                    $onlinePassed &&
                                                    $event->online_reviewer_registration_deadline
                                                ) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->online_reviewer_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                }
                                            }
                                        }
                                    }

                                    // Check deadline for jury role
                                    if ($role === 'jury') {
                                        if (
                                            $event->delivery_mode === 'face_to_face' &&
                                            $event->f2f_jury_registration_deadline
                                        ) {
                                            if ($now->gt($event->f2f_jury_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->f2f_jury_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif (
                                            $event->delivery_mode === 'online' &&
                                            $event->online_jury_registration_deadline
                                        ) {
                                            if ($now->gt($event->online_jury_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->online_jury_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'hybrid') {
                                            $f2fPassed =
                                                $event->f2f_jury_registration_deadline &&
                                                $now->gt($event->f2f_jury_registration_deadline);
                                            $onlinePassed =
                                                $event->online_jury_registration_deadline &&
                                                $now->gt($event->online_jury_registration_deadline);
                                            $f2fAvailable = !$f2fPassed;
                                            $onlineAvailable = !$onlinePassed;
                                            $f2fDeadlineText = $event->f2f_jury_registration_deadline
                                                ? \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format(
                                                    'M d, Y h:i A',
                                                )
                                                : '';
                                            $onlineDeadlineText = $event->online_jury_registration_deadline
                                                ? \Carbon\Carbon::parse(
                                                    $event->online_jury_registration_deadline,
                                                )->format('M d, Y h:i A')
                                                : '';
                                            if ($f2fPassed && $onlinePassed) {
                                                $deadlinePassed = true;
                                                $deadlineText = 'All deadlines passed';
                                            }
                                        } else {
                                            $f2fPassed =
                                                $event->f2f_jury_registration_deadline &&
                                                $now->gt($event->f2f_jury_registration_deadline);
                                            $onlinePassed =
                                                $event->online_jury_registration_deadline &&
                                                $now->gt($event->online_jury_registration_deadline);
                                            if ($f2fPassed || $onlinePassed) {
                                                $deadlinePassed = true;
                                                if ($f2fPassed && $event->f2f_jury_registration_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->f2f_jury_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                } elseif ($onlinePassed && $event->online_jury_registration_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->online_jury_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                @if ($deadlinePassed)
                                    <button class="btn" disabled
                                        style="background: #95a5a6; cursor: not-allowed; opacity: 0.6; margin-bottom: 0.75rem; position: relative;">
                                        <span style="text-decoration: line-through;">
                                            {{ ucfirst($role) }}
                                            @if ($role === 'jury')
                                                🎓
                                            @elseif($role === 'reviewer')
                                                📝
                                            @else
                                                👤
                                            @endif
                                        </span>
                                        <span
                                            style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 0.25rem;">
                                            ⏰ Registration Closed
                                        </span>
                                        @if ($deadlineText)
                                            <span
                                                style="font-size: 0.75rem; opacity: 0.8; display: block; margin-top: 0.2rem;">
                                                Deadline: {{ $deadlineText }}
                                            </span>
                                        @endif
                                    </button>
                                @elseif($isHybrid && ($f2fAvailable || $onlineAvailable))
                                    {{-- Hybrid event with partial availability - show mode selection --}}
                                    <div
                                        style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; border: 2px solid #3498db;">
                                        <h5 style="margin: 0 0 0.75rem 0; color: #2c3e50; font-size: 0.95rem;">
                                            {{ ucfirst($role) }}
                                            @if ($role === 'jury')
                                                🎓
                                            @elseif($role === 'reviewer')
                                                📝
                                            @else
                                                👤
                                            @endif
                                            - Choose Mode:
                                        </h5>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            @if ($f2fAvailable)
                                                <a href="{{ route('registrations.create', ['event' => $event, 'role' => $role, 'mode' => 'face_to_face']) }}"
                                                    class="btn"
                                                    style="flex: 1; min-width: 150px; background: #27ae60; margin: 0;">
                                                    🏛️ Face-to-Face
                                                    @if ($f2fDeadlineText)
                                                        <span
                                                            style="display: block; font-size: 0.75rem; margin-top: 0.25rem; opacity: 0.9;">
                                                            Until: {{ $f2fDeadlineText }}
                                                        </span>
                                                    @endif
                                                </a>
                                            @else
                                                <button disabled class="btn"
                                                    style="flex: 1; min-width: 150px; background: #95a5a6; opacity: 0.5; cursor: not-allowed; margin: 0;">
                                                    <span style="text-decoration: line-through;">🏛️ Face-to-Face</span>
                                                    <span
                                                        style="display: block; font-size: 0.75rem; color: #e74c3c; margin-top: 0.25rem;">
                                                        Closed
                                                    </span>
                                                </button>
                                            @endif

                                            @if ($onlineAvailable)
                                                <a href="{{ route('registrations.create', ['event' => $event, 'role' => $role, 'mode' => 'online']) }}"
                                                    class="btn"
                                                    style="flex: 1; min-width: 150px; background: #2980b9; margin: 0;">
                                                    💻 Online
                                                    @if ($onlineDeadlineText)
                                                        <span
                                                            style="display: block; font-size: 0.75rem; margin-top: 0.25rem; opacity: 0.9;">
                                                            Until: {{ $onlineDeadlineText }}
                                                        </span>
                                                    @endif
                                                </a>
                                            @else
                                                <button disabled class="btn"
                                                    style="flex: 1; min-width: 150px; background: #95a5a6; opacity: 0.5; cursor: not-allowed; margin: 0;">
                                                    <span style="text-decoration: line-through;">💻 Online</span>
                                                    <span
                                                        style="display: block; font-size: 0.75rem; color: #e74c3c; margin-top: 0.25rem;">
                                                        Closed
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <a class="btn"
                                        href="{{ route('registrations.create', ['event' => $event, 'role' => $role, 'mode' => 'face_to_face']) }}"
                                        style="margin-bottom: 0.75rem;">
                                        Register as {{ ucfirst($role) }}
                                        @if ($role === 'jury')
                                            🎓
                                        @elseif($role === 'reviewer')
                                            📝
                                        @else
                                            👤
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <a class="btn" href="{{ route('registrations.index') }}" style="background: #95a5a6;">View My
                        Registrations</a>
                @else
                    @if ($event->can_register)
                        <div style="margin-bottom: 1rem;">
                            <h4 style="color: #2c3e50; margin: 0 0 0.75rem 0; font-size: 1rem; text-align: center;">
                                Register as:</h4>
                            @foreach ($availableRoles as $role)
                                @php
                                    $now = now();
                                    $deadlinePassed = false;
                                    $deadlineText = '';
                                    $isHybrid = $event->delivery_mode === 'hybrid';
                                    $f2fAvailable = false;
                                    $onlineAvailable = false;
                                    $f2fDeadlineText = '';
                                    $onlineDeadlineText = '';

                                    // Check deadline for participant role
                                    if ($role === 'participant') {
                                        if ($event->delivery_mode === 'face_to_face' && $event->f2f_paper_submission_deadline) {
                                            if ($now->gt($event->f2f_paper_submission_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->f2f_paper_submission_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'online' && $event->online_paper_submission_deadline) {
                                            if ($now->gt($event->online_paper_submission_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->online_paper_submission_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'hybrid') {
                                            $f2fPassed =
                                                $event->f2f_paper_submission_deadline && $now->gt($event->f2f_paper_submission_deadline);
                                            $onlinePassed =
                                                $event->online_paper_submission_deadline &&
                                                $now->gt($event->online_paper_submission_deadline);
                                            $f2fAvailable = !$f2fPassed;
                                            $onlineAvailable = !$onlinePassed;
                                            $f2fDeadlineText = $event->f2f_paper_submission_deadline
                                                ? \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format(
                                                    'M d, Y h:i A',
                                                )
                                                : '';
                                            $onlineDeadlineText = $event->online_paper_submission_deadline
                                                ? \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format(
                                                    'M d, Y h:i A',
                                                )
                                                : '';
                                            if ($f2fPassed && $onlinePassed) {
                                                $deadlinePassed = true;
                                                $deadlineText = 'All deadlines passed';
                                            }
                                        } else {
                                            $f2fPassed =
                                                $event->f2f_paper_submission_deadline && $now->gt($event->f2f_paper_submission_deadline);
                                            $onlinePassed =
                                                $event->online_paper_submission_deadline &&
                                                $now->gt($event->online_paper_submission_deadline);
                                            if ($f2fPassed || $onlinePassed) {
                                                $deadlinePassed = true;
                                                if ($f2fPassed && $event->f2f_paper_submission_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->f2f_paper_submission_deadline,
                                                    )->format('M d, Y h:i A');
                                                } elseif ($onlinePassed && $event->online_paper_submission_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->online_paper_submission_deadline,
                                                    )->format('M d, Y h:i A');
                                                }
                                            }
                                        }
                                    }

                                    // Check deadline for reviewer role
                                    if ($role === 'reviewer') {
                                        if (
                                            $event->delivery_mode === 'face_to_face' &&
                                            $event->f2f_reviewer_registration_deadline
                                        ) {
                                            if ($now->gt($event->f2f_reviewer_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->f2f_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif (
                                            $event->delivery_mode === 'online' &&
                                            $event->online_reviewer_registration_deadline
                                        ) {
                                            if ($now->gt($event->online_reviewer_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->online_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'hybrid') {
                                            $f2fPassed =
                                                $event->f2f_reviewer_registration_deadline &&
                                                $now->gt($event->f2f_reviewer_registration_deadline);
                                            $onlinePassed =
                                                $event->online_reviewer_registration_deadline &&
                                                $now->gt($event->online_reviewer_registration_deadline);
                                            $f2fAvailable = !$f2fPassed;
                                            $onlineAvailable = !$onlinePassed;
                                            $f2fDeadlineText = $event->f2f_reviewer_registration_deadline
                                                ? \Carbon\Carbon::parse(
                                                    $event->f2f_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A')
                                                : '';
                                            $onlineDeadlineText = $event->online_reviewer_registration_deadline
                                                ? \Carbon\Carbon::parse(
                                                    $event->online_reviewer_registration_deadline,
                                                )->format('M d, Y h:i A')
                                                : '';
                                            if ($f2fPassed && $onlinePassed) {
                                                $deadlinePassed = true;
                                                $deadlineText = 'All deadlines passed';
                                            }
                                        } else {
                                            $f2fPassed =
                                                $event->f2f_reviewer_registration_deadline &&
                                                $now->gt($event->f2f_reviewer_registration_deadline);
                                            $onlinePassed =
                                                $event->online_reviewer_registration_deadline &&
                                                $now->gt($event->online_reviewer_registration_deadline);
                                            if ($f2fPassed || $onlinePassed) {
                                                $deadlinePassed = true;
                                                if ($f2fPassed && $event->f2f_reviewer_registration_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->f2f_reviewer_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                } elseif (
                                                    $onlinePassed &&
                                                    $event->online_reviewer_registration_deadline
                                                ) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->online_reviewer_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                }
                                            }
                                        }
                                    }

                                    // Check deadline for jury role
                                    if ($role === 'jury') {
                                        if (
                                            $event->delivery_mode === 'face_to_face' &&
                                            $event->f2f_jury_registration_deadline
                                        ) {
                                            if ($now->gt($event->f2f_jury_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->f2f_jury_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif (
                                            $event->delivery_mode === 'online' &&
                                            $event->online_jury_registration_deadline
                                        ) {
                                            if ($now->gt($event->online_jury_registration_deadline)) {
                                                $deadlinePassed = true;
                                                $deadlineText = \Carbon\Carbon::parse(
                                                    $event->online_jury_registration_deadline,
                                                )->format('M d, Y h:i A');
                                            }
                                        } elseif ($event->delivery_mode === 'hybrid') {
                                            $f2fPassed =
                                                $event->f2f_jury_registration_deadline &&
                                                $now->gt($event->f2f_jury_registration_deadline);
                                            $onlinePassed =
                                                $event->online_jury_registration_deadline &&
                                                $now->gt($event->online_jury_registration_deadline);
                                            $f2fAvailable = !$f2fPassed;
                                            $onlineAvailable = !$onlinePassed;
                                            $f2fDeadlineText = $event->f2f_jury_registration_deadline
                                                ? \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format(
                                                    'M d, Y h:i A',
                                                )
                                                : '';
                                            $onlineDeadlineText = $event->online_jury_registration_deadline
                                                ? \Carbon\Carbon::parse(
                                                    $event->online_jury_registration_deadline,
                                                )->format('M d, Y h:i A')
                                                : '';
                                            if ($f2fPassed && $onlinePassed) {
                                                $deadlinePassed = true;
                                                $deadlineText = 'All deadlines passed';
                                            }
                                        } else {
                                            $f2fPassed =
                                                $event->f2f_jury_registration_deadline &&
                                                $now->gt($event->f2f_jury_registration_deadline);
                                            $onlinePassed =
                                                $event->online_jury_registration_deadline &&
                                                $now->gt($event->online_jury_registration_deadline);
                                            if ($f2fPassed || $onlinePassed) {
                                                $deadlinePassed = true;
                                                if ($f2fPassed && $event->f2f_jury_registration_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->f2f_jury_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                } elseif ($onlinePassed && $event->online_jury_registration_deadline) {
                                                    $deadlineText = \Carbon\Carbon::parse(
                                                        $event->online_jury_registration_deadline,
                                                    )->format('M d, Y h:i A');
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                @if ($deadlinePassed)
                                    <button class="btn" disabled
                                        style="background: #95a5a6; cursor: not-allowed; opacity: 0.6; margin-bottom: 0.75rem; position: relative;">
                                        <span style="text-decoration: line-through;">
                                            {{ ucfirst($role) }}
                                            @if ($role === 'jury')
                                                🎓
                                            @elseif($role === 'reviewer')
                                                📝
                                            @else
                                                👤
                                            @endif
                                        </span>
                                        <span
                                            style="color: #e74c3c; font-size: 0.85rem; display: block; margin-top: 0.25rem;">
                                            Registration Closed
                                        </span>
                                        @if ($deadlineText)
                                            <span
                                                style="font-size: 0.75rem; opacity: 0.8; display: block; margin-top: 0.2rem;">
                                                Deadline: {{ $deadlineText }}
                                            </span>
                                        @endif
                                    </button>
                                @elseif($isHybrid && ($f2fAvailable || $onlineAvailable))
                                    {{-- Hybrid event with partial availability - show mode selection --}}
                                    <div
                                        style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; border: 2px solid #3498db;">
                                        <h5 style="margin: 0 0 0.75rem 0; color: #2c3e50; font-size: 0.95rem;">
                                            {{ ucfirst($role) }}
                                            @if ($role === 'jury')
                                                🎓
                                            @elseif($role === 'reviewer')
                                                📝
                                            @else
                                                👤
                                            @endif
                                            - Choose Mode:
                                        </h5>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            @if ($f2fAvailable)
                                                <a href="{{ route('registrations.create', ['event' => $event, 'role' => $role, 'mode' => 'face_to_face']) }}"
                                                    class="btn"
                                                    style="flex: 1; min-width: 150px; background: #27ae60; margin: 0;">
                                                    🏛️ Face-to-Face
                                                    @if ($f2fDeadlineText)
                                                        <span
                                                            style="display: block; font-size: 0.75rem; margin-top: 0.25rem; opacity: 0.9;">
                                                            Until: {{ $f2fDeadlineText }}
                                                        </span>
                                                    @endif
                                                </a>
                                            @else
                                                <button disabled class="btn"
                                                    style="flex: 1; min-width: 150px; background: #95a5a6; opacity: 0.5; cursor: not-allowed; margin: 0;">
                                                    <span style="text-decoration: line-through;">🏛️ Face-to-Face</span>
                                                    <span
                                                        style="display: block; font-size: 0.75rem; color: #e74c3c; margin-top: 0.25rem;">
                                                        Closed
                                                    </span>
                                                </button>
                                            @endif

                                            @if ($onlineAvailable)
                                                <a href="{{ route('registrations.create', ['event' => $event, 'role' => $role, 'mode' => 'online']) }}"
                                                    class="btn"
                                                    style="flex: 1; min-width: 150px; background: #2980b9; margin: 0;">
                                                    💻 Online
                                                    @if ($onlineDeadlineText)
                                                        <span
                                                            style="display: block; font-size: 0.75rem; margin-top: 0.25rem; opacity: 0.9;">
                                                            Until: {{ $onlineDeadlineText }}
                                                        </span>
                                                    @endif
                                                </a>
                                            @else
                                                <button disabled class="btn"
                                                    style="flex: 1; min-width: 150px; background: #95a5a6; opacity: 0.5; cursor: not-allowed; margin: 0;">
                                                    <span style="text-decoration: line-through;">💻 Online</span>
                                                    <span
                                                        style="display: block; font-size: 0.75rem; color: #e74c3c; margin-top: 0.25rem;">
                                                        Closed
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <a class="btn"
                                        href="{{ route('registrations.create', ['event' => $event, 'role' => $role, 'mode' => 'face_to_face']) }}"
                                        style="margin-bottom: 0.75rem;">
                                        Register as {{ ucfirst($role) }}
                                        @if ($role === 'jury')
                                            🎓
                                        @elseif($role === 'reviewer')
                                            📝
                                        @else
                                            👤
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <button class="btn" disabled>
                            Registration Closed
                        </button>
                    @endif
                @endif

                @if ($event->tags && is_array($event->tags) && count($event->tags) > 0)
                    <div style="margin-top: 1.5rem;">
                        <h4 style="color: #2c3e50; margin: 0 0 0.75rem 0; font-size: 1rem;">Tags</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach ($event->tags as $tag)
                                <span
                                    style="display: inline-block; padding: 0.35rem 0.75rem; background: #ecf0f1; color: #555; border-radius: 15px; font-size: 0.85rem;">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
