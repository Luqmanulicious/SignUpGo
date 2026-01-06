@extends('layouts.app')

@section('title', 'Reviewer Dashboard - ' . $event->title)

@section('content')
    <!-- Include Toast Notification Component -->
    @include('components.toast-notification')

    <style>
        .toast {
            min-width: 300px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideIn 0.3s ease-out;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            pointer-events: auto;
        }
        
        .toast-success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .toast-error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .toast-warning {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #2c3e50;
        }
        
        .toast-info {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: #2c3e50;
        }
        
        .toast-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        
        .toast-message {
            font-size: 0.9rem;
            opacity: 0.95;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .toast-closing {
            animation: slideOut 0.3s ease-out forwards;
        }
        
        /* Score Button Styles */
        .score-selector {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .score-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #4b5563;
            min-width: 60px;
        }
        
        .score-options {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .score-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            background: white;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            color: #6b7280;
        }
        
        .score-btn:hover {
            border-color: #9b59b6;
            background: #f3e5f5;
            transform: scale(1.1);
        }
        
        .score-btn.selected {
            background: #9b59b6;
            border-color: #9b59b6;
            color: white;
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(155, 89, 182, 0.3);
        }
        
        /* Rubric Table Styles */
        .view-rubric-btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .view-rubric-btn:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .view-rubric-btn .arrow {
            transition: transform 0.3s;
        }
        
        .view-rubric-btn.active .arrow {
            transform: rotate(180deg);
        }
        
        .rubric-table-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
            margin-top: 1rem;
        }
        
        .rubric-table-container.active {
            max-height: 1000px;
            transition: max-height 0.5s ease-in;
        }
        
        .rubric-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            margin-top: 1rem;
        }
        
        .rubric-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .rubric-table th {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .rubric-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.9rem;
            color: #4b5563;
        }
        
        .rubric-table tr:last-child td {
            border-bottom: none;
        }
        
        .rubric-table tr:hover {
            background: #f9fafb;
        }
        
        .rubric-table .score-column {
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            width: 80px;
            background: #f8f9fa;
        }
        
        .rubric-table .score-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #9b59b6;
            color: white;
            border-radius: 50%;
            font-size: 1.1rem;
            font-weight: 700;
            min-width: 45px;
            text-align: center;
        }
        
        .rubric-table-header {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>

    <a href="{{ route('registrations.index') }}"
        style="padding: 0.75rem 1.5rem; background: #6c7778; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
        ← Back to My Registrations
    </a>
    <div class="container" style="max-width: 1400px; padding: 2rem;">
        <div
            style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="margin: 0 0 0.5rem 0; color: #2c3e50;"> Reviewer Dashboard</h1>
                    <h2 style="margin: 0; color: #7f8c8d; font-size: 1.3rem; font-weight: 500;">{{ $event->title }}</h2>
                </div>
            </div>

            @php
                $reviewDeadline = null;
                $reviewDeadlineText = '';
                $isDeadlinePassed = false;
                
                if ($event->delivery_mode === 'face_to_face' && $event->f2f_review_deadline) {
                    $reviewDeadline = \Carbon\Carbon::parse($event->f2f_review_deadline);
                    $reviewDeadlineText = $reviewDeadline->format('F d, Y h:i A');
                    $isDeadlinePassed = now()->gt($reviewDeadline);
                } elseif ($event->delivery_mode === 'online' && $event->online_review_deadline) {
                    $reviewDeadline = \Carbon\Carbon::parse($event->online_review_deadline);
                    $reviewDeadlineText = $reviewDeadline->format('F d, Y h:i A');
                    $isDeadlinePassed = now()->gt($reviewDeadline);
                } elseif ($event->delivery_mode === 'hybrid') {
                    if ($event->f2f_review_deadline && $event->online_review_deadline) {
                        $f2fDeadline = \Carbon\Carbon::parse($event->f2f_review_deadline);
                        $onlineDeadline = \Carbon\Carbon::parse($event->online_review_deadline);
                        $reviewDeadline = $f2fDeadline->lt($onlineDeadline) ? $onlineDeadline : $f2fDeadline;
                        $reviewDeadlineText = 'F2F: ' . $f2fDeadline->format('M d, Y h:i A') . ' | Online: ' . $onlineDeadline->format('M d, Y h:i A');
                        $isDeadlinePassed = now()->gt($reviewDeadline);
                    } elseif ($event->f2f_review_deadline) {
                        $reviewDeadline = \Carbon\Carbon::parse($event->f2f_review_deadline);
                        $reviewDeadlineText = $reviewDeadline->format('F d, Y h:i A');
                        $isDeadlinePassed = now()->gt($reviewDeadline);
                    } elseif ($event->online_review_deadline) {
                        $reviewDeadline = \Carbon\Carbon::parse($event->online_review_deadline);
                        $reviewDeadlineText = $reviewDeadline->format('F d, Y h:i A');
                        $isDeadlinePassed = now()->gt($reviewDeadline);
                    }
                } else {
                    // For events without delivery_mode set
                    if ($event->f2f_review_deadline) {
                        $reviewDeadline = \Carbon\Carbon::parse($event->f2f_review_deadline);
                        $reviewDeadlineText = $reviewDeadline->format('F d, Y h:i A');
                        $isDeadlinePassed = now()->gt($reviewDeadline);
                    } elseif ($event->online_review_deadline) {
                        $reviewDeadline = \Carbon\Carbon::parse($event->online_review_deadline);
                        $reviewDeadlineText = $reviewDeadline->format('F d, Y h:i A');
                        $isDeadlinePassed = now()->gt($reviewDeadline);
                    }
                }
            @endphp

            @if($reviewDeadline)
                @php
                    $totalAssigned = $assignedParticipants->count();
                    $completedReviews = $assignedParticipants->where('review_status', 'completed')->count();
                    $percentage = $totalAssigned > 0 ? round(($completedReviews / $totalAssigned) * 100) : 0;
                    
                    // HCI-friendly color scheme
                    if ($percentage == 0) {
                        $progressColor = '#95a5a6'; // Gray for not started
                    } elseif ($percentage <= 25) {
                        $progressColor = '#e74c3c'; // Red
                    } elseif ($percentage <= 60) {
                        $progressColor = '#f39c12'; // Orange
                    } elseif ($percentage <= 80) {
                        $progressColor = '#f1c40f'; // Yellow
                    } else {
                        $progressColor = '#27ae60'; // Green
                    }
                @endphp
                
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    @if ($isDeadlinePassed)
                        {{-- Deadline Passed: Show Progress Bar --}}
                        <div style="background: white; padding: 1.5rem; border-radius: 8px; flex: 1; border: 2px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <div style="font-size: 2rem;">⚠️</div>
                                <div style="flex: 1;">
                                    <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Review Deadline (Passed)</div>
                                    <div style="font-size: 1.1rem; font-weight: 700; color: #2c3e50;">{{ $reviewDeadlineText }}</div>
                                    <div style="font-size: 0.8rem; color: #e74c3c; margin-top: 0.25rem;">⚠️ Deadline has passed</div>
                                </div>
                            </div>
                            
                            {{-- Progress Bar: Green (Completed) + Red (Missed) --}}
                            <div style="margin-top: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span style="font-size: 0.85rem; color: #27ae60; font-weight: 600;">✓ Completed: {{ $completedReviews }}</span>
                                    <span style="font-size: 0.85rem; color: #e74c3c; font-weight: 600;">✗ Missed: {{ $totalAssigned - $completedReviews }}</span>
                                </div>
                                <div style="width: 100%; height: 30px; background: #f8f9fa; border-radius: 15px; overflow: hidden; display: flex; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                    @if($percentage > 0)
                                        <div style="width: {{ $percentage }}%; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem; transition: width 0.5s ease;">
                                            {{ $percentage }}%
                                        </div>
                                    @endif
                                    @if($percentage < 100)
                                        <div style="width: {{ 100 - $percentage }}%; background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem; transition: width 0.5s ease;">
                                            {{ 100 - $percentage }}%
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align: center; margin-top: 0.5rem; font-size: 0.9rem; color: #6c757d;">
                                    <strong>{{ $completedReviews }}/{{ $totalAssigned }}</strong> reviews completed
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Deadline Active: Show Countdown --}}
                        <div style="background: #2c3e50; padding: 1rem; border-radius: 8px; flex: 1; color: white; display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem;">⏰</div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.25rem;">Review Deadline</div>
                                <div style="font-size: 1.1rem; font-weight: bold;">{{ $reviewDeadlineText }}</div>
                                <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 0.25rem;">
                                    Please submit all evaluations before this deadline
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 180px;">
                        <div style="position: relative; width: 100px; height: 100px;">
                            <svg width="100" height="100" style="transform: rotate(-90deg);">
                                <circle cx="50" cy="50" r="42" fill="none" stroke="#ecf0f1" stroke-width="8"></circle>
                                <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $progressColor }}" stroke-width="8" 
                                    stroke-dasharray="{{ 2 * 3.14159 * 42 }}" 
                                    stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $percentage / 100) }}" 
                                    stroke-linecap="round"
                                    style="transition: stroke-dashoffset 0.5s ease;"></circle>
                            </svg>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: {{ $progressColor }};">{{ $percentage }}%</div>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem; text-align: center; color: #7f8c8d; font-size: 0.9rem; font-weight: 600;">
                            Review Progress
                        </div>
                        {{-- <div style="margin-top: 0.25rem; text-align: center; color: #95a5a6; font-size: 0.8rem;">
                            {{ $completedReviews }}/{{ $totalAssigned }} Reviews
                        </div> --}}
                    </div>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1.5rem;">
                <div
                    style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); padding: 1.5rem; border-radius: 8px; color: #2c3e50;">
                    <div style="font-size: 0.9rem; opacity: 0.8;">Your Role</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">Reviewer</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); padding: 1.5rem; border-radius: 8px; color: #2c3e50;">
                    <div style="font-size: 0.9rem; opacity: 0.8;">Participants Assigned</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">{{ $assignedParticipants->count() }}</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); padding: 1.5rem; border-radius: 8px; color: #2c3e50;">
                    <div style="font-size: 0.9rem; opacity: 0.8;">Reviews Completed</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">
                        {{ $assignedParticipants->where('review_status', 'completed')->count() }}/{{ $assignedParticipants->count() }}
                    </div>
                </div>
            </div>

            @php
                $eventEnded = \Carbon\Carbon::now()->isAfter($event->end_date);
                $allEvaluationsCompleted = $assignedParticipants->count() > 0 && 
                    $assignedParticipants->where('review_status', 'completed')->count() === $assignedParticipants->count();
            @endphp

            {{-- Feedback Section for Reviewers (After All Evaluations Completed) --}}
            @if($eventEnded && $allEvaluationsCompleted)
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-top: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; color: white;">
                        <div style="font-size: 3rem;">💬</div>
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 700;">Share Your Feedback</h3>
                            <p style="margin: 0 0 1rem 0; opacity: 0.95; font-size: 0.95rem;">
                                You've completed all your evaluations! Help us improve by sharing your experience.
                            </p>
                            <a href="{{ route('feedback.create', $registration) }}" 
                                style="display: inline-block; background: white; color: #059669; padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s ease;">
                                💬 Submit Event Feedback
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; color: #2c3e50; font-size: 1.3rem;">Participants to Evaluate</h3>
                {{-- <button onclick="showToast('success', '✅ Test', 'Toast system working!', 3000); console.log('Test button clicked');" 
                        style="background: #3498db; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                    Test Toast
                </button> --}}
            </div>

                @if($assignedParticipants->isEmpty())
                    <div
                        style="background: #f8f9fa; padding: 3rem 2rem; border-radius: 8px; text-align: center; color: #7f8c8d;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50;">No Participants Assigned Yet</h4>
                        <p style="margin: 0;">Participants assigned to you for evaluation will appear here.</p>
                    </div>
                @else
                    @foreach($assignedParticipants as $assignment)
                        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid {{ $assignment->review_status === 'completed' ? '#27ae60' : '#3498db' }};">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50; font-size: 1.1rem;">{{ $assignment->participant_name }}</h4>
                                    <p style="margin: 0; color: #7f8c8d; font-size: 0.9rem;">{{ $assignment->participant_email }}</p>
                                    <p style="margin: 0.25rem 0 0 0; color: #95a5a6; font-size: 0.85rem;">Code: {{ $assignment->participant_code }}</p>
                                </div>
                                @if($assignment->review_status === 'completed')
                                    <span style="background: #27ae60; color: white; padding: 0.35rem 0.75rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                        ✓ Reviewed
                                    </span>
                                @else
                                    <span style="background: #f39c12; color: white; padding: 0.35rem 0.75rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            @if($assignment->paper_id)
                                <div style="background: white; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <span style="font-size: 1.2rem;">📄</span>
                                        <strong style="color: #2c3e50;">{{ $assignment->paper_title }}</strong>
                                    </div>
                                    @if($assignment->product_category || $assignment->paper_theme)
                                        <span style="background: #e8f5e9; color: #2e7d32; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; display: inline-block; margin-bottom: 0.5rem;">
                                            {{ $assignment->product_category ?? $assignment->paper_theme }}
                                        </span>
                                    @endif
                                    <p style="margin: 0.5rem 0 0 0; color: #555; font-size: 0.9rem; line-height: 1.5;">
                                        {{ \Illuminate\Support\Str::limit($assignment->paper_abstract, 150) }}
                                    </p>
                                    @if($assignment->poster_path || $assignment->paper_path || $assignment->video_url)
                                        <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                                            @if($assignment->poster_path || $assignment->paper_path)
                                                <a href="{{ $assignment->poster_path ?? $assignment->paper_path }}" target="_blank" style="padding: 0.4rem 0.8rem; background: #ae17ab; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem;">
                                                     View Paper
                                                </a>
                                            @endif
                                            @if($assignment->video_url)
                                                <a href="{{ $assignment->video_url }}" target="_blank" style="padding: 0.4rem 0.8rem; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem;">
                                                     Watch Video
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div style="background: #fff3cd; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <p style="margin: 0; color: #856404; font-size: 0.9rem;">⚠️ No paper submitted yet</p>
                                </div>
                            @endif

                            @if($assignment->review_status === 'completed')
                                <div style="background: #f3e5f5; padding: 1rem; border-radius: 6px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <strong style="color: #6a1b9a;">Your Review:</strong>
                                        <span style="background: #6a1b9a; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-weight: 600;">
                                            Score: {{ $assignment->score }}/100
                                        </span>
                                    </div>
                                    
                                    <p style="margin: 0.5rem 0; color: #7f8c8d; font-size: 0.85rem;">
                                        Reviewed: {{ \Carbon\Carbon::parse($assignment->reviewed_at)->format('M d, Y h:i A') }}
                                    </p>
                                    
                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                        <button onclick="toggleReviewDetails({{ $assignment->mapping_id }}, event)" 
                                            style="flex: 1; padding: 0.6rem; background: #7e57c2; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;"
                                            onmouseover="this.style.background='#673ab7'" 
                                            onmouseout="this.style.background='#7e57c2'">
                                            👁️ View Details
                                        </button>
                                        
                                        @if(!$isDeadlinePassed)
                                            <button onclick="editReviewModal({{ $assignment->mapping_id }}, '{{ addslashes($assignment->participant_name) }}', {{ $assignment->paper_id ? 'true' : 'false' }}, {{ $assignment->paper_id ? json_encode(['title' => $assignment->paper_title, 'abstract' => $assignment->paper_abstract, 'category' => $assignment->product_category ?? $assignment->paper_theme ?? null, 'paper' => $assignment->poster_path ?? $assignment->paper_path ?? null, 'video' => $assignment->video_url]) : 'null' }})" 
                                                style="flex: 1; padding: 0.6rem; background: #f39c12; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;"
                                                onmouseover="this.style.background='#e67e22'" 
                                                onmouseout="this.style.background='#f39c12'">
                                                ✏️ Edit
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div id="reviewDetails{{ $assignment->mapping_id }}" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e1bee7;">
                                        @php
                                            // Get submitted scores for this assignment
                                            $scores = $submittedScores->get($assignment->mapping_id, collect());
                                            
                                            // Group scores by category
                                            $scoresByCategory = $scores->groupBy('rubric_category_id');
                                        @endphp
                                        
                                        @if($scores->isNotEmpty())
                                            <strong style="color: #6a1b9a; font-size: 0.9rem; display: block; margin-bottom: 0.75rem;">Detailed Scores:</strong>
                                            
                                            @foreach($rubricCategories as $category)
                                                @php
                                                    $categoryScores = $scoresByCategory->get($category->id, collect());
                                                @endphp
                                                
                                                @if($categoryScores->isNotEmpty())
                                                    <div style="background: #fff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 2px solid #e1bee7;">
                                                        <h4 style="margin: 0 0 0.75rem 0; color: #6a1b9a; font-size: 1rem; border-bottom: 2px solid #e1bee7; padding-bottom: 0.5rem;">
                                                            {{ $category->name }}
                                                        </h4>
                                                        
                                                        @foreach($categoryScores as $scoreItem)
                                                            <div style="background: #f3e5f5; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.5rem; border-left: 3px solid #7e57c2;">
                                                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                    <span style="color: #4a148c; font-weight: 600; font-size: 0.9rem;">{{ $scoreItem->rubric_item_name }}</span>
                                                                    <span style="background: #7e57c2; color: white; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem; font-weight: 600;">
                                                                        {{ $scoreItem->score }}/{{ $scoreItem->max_score }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        
                                                        @php
                                                            // Get the comment from the first score item in this category (they should all have the same comment)
                                                            $categoryComment = $categoryScores->first()->comment ?? null;
                                                        @endphp
                                                        
                                                        @if(!empty(trim($categoryComment)))
                                                            <div style="margin-top: 0.75rem; padding: 0.75rem; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #9b59b6;">
                                                                <strong style="color: #6a1b9a; font-size: 0.85rem; display: block; margin-bottom: 0.25rem;">Comment:</strong>
                                                                <p style="margin: 0; color: #555; font-size: 0.85rem; line-height: 1.4; font-style: italic;">
                                                                    "{{ $categoryComment }}"
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        
                                        @php
                                            $reviewNotes = $assignment->review_notes;
                                            $overallNotes = '';
                                            
                                            // Extract only the "Overall Notes:" part
                                            if (!empty($reviewNotes)) {
                                                if (strpos($reviewNotes, 'Overall Notes:') !== false) {
                                                    $parts = explode('Overall Notes:', $reviewNotes, 2);
                                                    $overallNotes = trim($parts[1]);
                                                }
                                            }
                                        @endphp
                                        
                                        @if(!empty($overallNotes))
                                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e1bee7;">
                                                <strong style="color: #8934be; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Additional Comments:</strong>
                                                <p style="margin: 0; color: #555; font-size: 0.9rem; line-height: 1.5; background: #f3e5f5; padding: 0.75rem; border-radius: 4px; border-left: 3px solid #7e57c2;">{{ $overallNotes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @if($isDeadlinePassed)
                                    <div style="background: #ffebee; padding: 1rem; border-radius: 6px; border-left: 4px solid #e74c3c;">
                                        <p style="margin: 0; color: #c62828; font-weight: 600; font-size: 0.9rem;">Review Deadline Has Passed</p>
                                        <p style="margin: 0.5rem 0 0 0; color: #d32f2f; font-size: 0.85rem;">Evaluation is no longer available for this participant.</p>
                                    </div>
                                @else
                                    <button onclick="openReviewModal(
                                        {{ $assignment->mapping_id }}, 
                                        '{{ addslashes($assignment->participant_name) }}', 
                                        {{ $assignment->paper_id ? 'true' : 'false' }},
                                        {{ $assignment->paper_id ? json_encode([
                                            'title' => $assignment->paper_title,
                                            'abstract' => $assignment->paper_abstract,
                                            'category' => $assignment->product_category ?? $assignment->paper_theme ?? null,
                                            'paper' => $assignment->poster_path ?? $assignment->paper_path ?? null,
                                            'video' => $assignment->video_url
                                        ]) : 'null' }}
                                    )" 
                                        style="width: 100%; padding: 0.75rem; background: #3498db; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                                        Evaluate Participant
                                    </button>
                                @endif
                            @endif
                        </div>
                    @endforeach
                @endif
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; max-width: 1600px; width: 95%; max-height: 95vh; display: flex; flex-direction: column;">
            <div style="padding: 1.5rem 2rem; border-bottom: 2px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #2c3e50; font-size: 1.5rem;">Submit Review</h3>
                <button type="button" onclick="closeReviewModal()" style="background: none; border: none; color: #e74c3c; font-size: 1.1rem; font-weight: 600; cursor: pointer; padding: 0.5rem; display: flex; align-items: center; gap: 0.25rem; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                    ✕ Close
                </button>
            </div>
            
            <form id="reviewForm" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                <input type="hidden" id="mappingId" name="mapping_id">
                <input type="hidden" id="isEditMode" value="false">
                
                <div style="padding: 1rem 2rem; background: #f8f9fa; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: inline; color: #2c3e50; font-weight: 600; margin-right: 0.5rem;">Participant:</label>
                    <span id="participantName" style="color: #555; font-size: 1.05rem; font-weight: 600;"></span>
                </div>

                <!-- Two Column Layout -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; flex: 1; overflow: hidden;">
                    
                    <!-- Left Column: Paper Details (Sticky) -->
                    <div style="border-right: 3px solid #ff9966; padding: 0; overflow-y: auto; background: linear-gradient(to bottom, #fff5f0 0%, #ffe8db 100%);">
                        <!-- Column Header -->
                        <div style="background: linear-gradient(135deg, #ff9966 0%, #ff6b35 100%); color: white; padding: 1rem 1.5rem; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Paper Submission</h4>
                            </div>
                        </div>
                        
                        <div style="padding: 1.5rem;">
                        <!-- Paper Details Section -->
                        <div id="paperDetailsSection" style="display: none; background: #fff; border: 2px solid #ff9966; border-radius: 8px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(255, 153, 102, 0.1);">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e0e0e0;">
                        <h5 style="margin: 0; color: #ff6b35; font-size: 1rem; font-weight: 700;">Details</h5>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.25rem; color: #7f8c8d; font-weight: 600; font-size: 0.85rem;">TITLE</label>
                        <p id="paperTitle" style="margin: 0; color: #2c3e50; font-size: 1.05rem; font-weight: 600; line-height: 1.4;"></p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.25rem; color: #7f8c8d; font-weight: 600; font-size: 0.85rem;">CATEGORY</label>
                        <span id="paperCategory" style=" color: black; padding: 0.35rem 0.75rem; border-radius: 4px; font-size: 0.9rem; font-weight: 600; display: inline-block;"></span>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #7f8c8d; font-weight: 600; font-size: 0.85rem;">ABSTRACT</label>
                        <p id="paperAbstract" style="margin: 0; color: #555; font-size: 0.95rem; line-height: 1.6; text-align: justify;"></p>
                    </div>
                    
                    <!-- Paper Preview -->
                    <div id="paperPreviewContainer" style="display: none; margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #7f8c8d; font-weight: 600; font-size: 0.85rem;">PAPER PREVIEW</label>
                        <div style="border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: #f8f9fa;">
                            <iframe id="paperPreviewFrame" style="width: 100%; height: 500px; border: none; display: none;"></iframe>
                            <img id="paperPreviewImage" style="width: 100%; height: auto; display: none;" alt="Paper Preview">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 0.75rem;">
                        <a id="paperVideoLink" href="#" target="_blank" style="padding: 0.75rem 1.5rem; background: #e74c3c; color: white; text-decoration: none; border-radius: 6px; font-size: 1rem; font-weight: 600; display: none;">
                            🎥 Watch Video
                        </a>
                    </div>
                </div>

                        <!-- No Paper Warning -->
                        <div id="noPaperWarning" style="display: none; background: #fff3cd; padding: 1rem; border-radius: 8px; border-left: 4px solid #f39c12; box-shadow: 0 2px 4px rgba(243, 156, 18, 0.2);">
                            <p style="margin: 0; color: #856404; font-weight: 600;">⚠️ This participant has not submitted a paper yet.</p>
                            <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.9rem;">You can still provide feedback based on other evaluation criteria.</p>
                        </div>
                        </div>
                    </div>

                    <!-- Right Column: Evaluation Form (Scrollable) -->
                    <div style="padding: 0; overflow-y: auto; background: #ffffff;">
                        <!-- Column Header -->
                        <div style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 1rem 1.5rem; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Evaluation Form</h4>
                            </div>
                        </div>
                        
                        <div style="padding: 1.5rem;">
                        @if($rubricCategories->isEmpty())
                            <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #f39c12;">
                                <p style="margin: 0; color: #856404;">⚠️ No evaluation rubric has been set up for this event. Please contact the event organizer.</p>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label for="reviewNotes" style="display: block; margin-bottom: 0.5rem; color: #2c3e50; font-weight: 600;">Review Notes:</label>
                                <textarea id="reviewNotes" name="review_notes" rows="6" 
                                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; resize: vertical;"
                                    placeholder="Provide detailed feedback..."></textarea>
                            </div>
                        @else
                            <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #2196f3;">
                                <p style="margin: 0; color: #0d47a1; font-weight: 600;">📊 Evaluation Rubric</p>
                                <p style="margin: 0.5rem 0 0 0; color: #1565c0; font-size: 0.9rem;">Rate each criterion from 1-5 based on the descriptions provided.</p>
                            </div>

                            <div id="rubricItems" style="margin-bottom: 1.5rem;">
                        @foreach($rubricCategories as $category)
                            <!-- STEP A: Criteria (Parent) Header -->
                            <div style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white; padding: 1rem 1.25rem; border-radius: 8px 8px 0 0; margin-top: {{ $loop->first ? '0' : '1.5rem' }};">
                                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">{{ $category->name }}</h3>
                                @if($category->description)
                                    <p style="margin: 0.25rem 0 0 0; opacity: 0.9; font-size: 0.9rem;">{{ $category->description }}</p>
                                @endif
                                <div style="margin-top: 0.5rem;">
                                    <span style="background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.85rem;">
                                        Max Score: {{ $category->max_score }} | Weight: {{ $category->weight }}%
                                    </span>
                                </div>
                            </div>

                            <!-- STEP B: Criterions (Children) Loop -->
                            @if(isset($rubricsByCategory[$category->id]))
                                @foreach($rubricsByCategory[$category->id] as $index => $item)
                                    <div style="background: #f8f9fa; padding: 1.25rem; border-left: 4px solid #9b59b6; border-right: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                            <div style="flex: 1;">
                                                <h4 style="margin: 0 0 0.25rem 0; color: #2c3e50; font-size: 1.05rem; font-weight: 600;">{{ $item->name }}</h4>
                                                @if($item->description)
                                                    <p style="margin: 0; color: #7f8c8d; font-size: 0.9rem; line-height: 1.5;">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                            <div style="background: #9b59b6; color: white; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; white-space: nowrap; margin-left: 1rem;">
                                                Max: {{ $item->max_score }}
                                            </div>
                                        </div>
                                        
                                        <!-- Dynamic Score Selector based on max_score -->
                                        <div class="score-selector">
                                            <div class="score-label">Score:</div>
                                            <div class="score-options">
                                                @for ($i = 0; $i <= $item->max_score; $i++)
                                                    <button type="button" class="score-btn" data-item-id="{{ $item->id }}" data-score="{{ $i }}" onclick="selectScore({{ $item->id }}, {{ $i }})">
                                                        {{ $i }}
                                                    </button>
                                                @endfor
                                            </div>
                                        </div>
                                        <input type="hidden" name="rubric_scores[{{ $item->id }}]" id="score_{{ $item->id }}" class="rubric-score" data-max="{{ $item->max_score }}" data-category-id="{{ $category->id }}" required>
                                        
                                        <!-- View Rubric Table Button -->
                                        <button type="button" class="view-rubric-btn" onclick="toggleRubricTable({{ $item->id }}, event)">
                                            📋 View Rubric Table
                                            <span class="arrow">▼</span>
                                        </button>
                                        
                                        <!-- Rubric Table (Initially Hidden) -->
                                        <div class="rubric-table-container" id="rubric_table_{{ $item->id }}">
                                            <div class="rubric-table">
                                                <div class="rubric-table-header">
                                                    <span>📊</span>
                                                    <span>Score Levels for {{ $item->name }}</span>
                                                </div>
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Score</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $scoreLevels = DB::table('rubric_score_levels')
                                                                ->where('rubric_item_id', $item->id)
                                                                ->orderBy('level', 'asc')
                                                                ->get();
                                                        @endphp
                                                        @if($scoreLevels->isEmpty())
                                                            @for ($i = 0; $i <= $item->max_score; $i++)
                                                                <tr>
                                                                    <td class="score-column">
                                                                        <span class="score-badge">{{ $i }}</span>
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $percentage = $item->max_score > 0 ? ($i / $item->max_score) * 100 : 0;
                                                                        @endphp
                                                                        @if($percentage == 0)
                                                                            Not Applicable / No Score
                                                                        @elseif($percentage <= 20)
                                                                            Poor - Does not meet criteria
                                                                        @elseif($percentage <= 40)
                                                                            Below Average - Partially meets criteria
                                                                        @elseif($percentage <= 60)
                                                                            Average - Meets basic criteria
                                                                        @elseif($percentage <= 80)
                                                                            Good - Exceeds criteria
                                                                        @else
                                                                            Excellent - Significantly exceeds criteria
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endfor
                                                        @else
                                                            @foreach($scoreLevels as $level)
                                                                <tr>
                                                                    <td class="score-column">
                                                                        <span class="score-badge">{{ $level->level }}</span>
                                                                    </td>
                                                                    <td>{{ $level->description }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- STEP C: Criteria-Level Comment (After all criterions) -->
                                <div style="background: #fff8e1; padding: 1.25rem; border-left: 4px solid #ffa726; border-right: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0; border-radius: 0 0 8px 8px; margin-bottom: 0.5rem;">
                                    <label style="display: block; margin-bottom: 0.5rem; color: #e65100; font-weight: 600; font-size: 0.95rem;">
                                        💬 Comments for "{{ $category->name }}" Criteria:
                                    </label>
                                    <textarea name="category_comments[{{ $category->id }}]" class="category-comment" rows="3"
                                        style="width: 100%; padding: 0.75rem; border: 2px solid #ffa726; border-radius: 6px; font-size: 0.9rem; resize: vertical; font-family: inherit;"
                                        placeholder="Provide feedback for the entire {{ $category->name }} criteria..."></textarea>
                                </div>
                            @endif
                            @endforeach
                            </div>

                            <div style="background: #e8f5e9; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #27ae60;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;">
                                        <span style="color: #2e7d32; font-weight: 600;">Total Score:</span>
                                        <span id="totalScore" style="color: #2e7d32; font-size: 1.3rem; font-weight: bold; margin-left: 0.5rem;">0</span>
                                        <span style="color: #2e7d32; font-weight: 600;">/ {{ $rubricItems->sum('max_score') }}</span>
                                    </div>
                                    <div>
                                        <span style="background: #27ae60; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 1.1rem; font-weight: bold;">
                                            <span id="percentage">0</span>%
                                        </span>
                                    </div>
                        </div>
                                </div>
                            </div>

                            <!-- STEP D: Overall Evaluation Comment -->
                            <div style="background: #e3f2fd; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #2196f3;">
                                <label for="reviewNotes" style="display: block; margin-bottom: 0.5rem; color: #0d47a1; font-weight: 700; font-size: 1rem;">
                                    📝 Overall Evaluation Comment:
                                </label>
                                <p style="margin: 0 0 0.75rem 0; color: #1565c0; font-size: 0.85rem;">Provide your overall assessment and final remarks for this participant's submission.</p>
                                <textarea id="reviewNotes" name="review_notes" rows="5" 
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #2196f3; border-radius: 6px; font-size: 1rem; resize: vertical; font-family: inherit;"
                                    placeholder="Write your overall evaluation comment here..."></textarea>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Buttons Footer -->
                <div style="padding: 1.5rem 2rem; border-top: 2px solid #e0e0e0; background: #f8f9fa; display: flex; gap: 1rem; border-radius: 0 0 12px 12px;">
                    <button type="submit" style="flex: 1; padding: 0.75rem; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                        ✓ Submit Review
                    </button>
                    <button type="button" onclick="closeReviewModal()" style="flex: 1; padding: 0.75rem; background: #95a5a6; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toast Notification System
        function showToast(type, title, message, duration = 4000) {
            console.log(`[Toast] Showing ${type} toast:`, title, message);
            const container = document.getElementById('toastContainer');
            if (!container) {
                console.error('[Toast] Container not found!');
                return;
            }
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            toast.innerHTML = `
                <div class="toast-icon">${icons[type]}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
            `;
            
            container.appendChild(toast);
            console.log('[Toast] Toast appended to DOM');
            
            setTimeout(() => {
                toast.classList.add('toast-closing');
                setTimeout(() => {
                    if (container.contains(toast)) {
                        container.removeChild(toast);
                    }
                }, 300);
            }, duration);
        }

        // Calculate total score and percentage when rubric scores change
        document.addEventListener('DOMContentLoaded', function() {
            const rubricScores = document.querySelectorAll('.rubric-score');
            const maxScore = {{ $rubricItems->sum('max_score') ?? 0 }};
            
            rubricScores.forEach(input => {
                // Listen to changes on hidden inputs
            });
            
            // Function to recalculate total whenever a score changes
            window.recalculateTotal = function() {
                let total = 0;
                document.querySelectorAll('.rubric-score').forEach(input => {
                    if (input.value) {
                        total += parseInt(input.value);
                    }
                });
                
                document.getElementById('totalScore').textContent = total;
                
                const percentage = maxScore > 0 ? Math.round((total / maxScore) * 100) : 0;
                document.getElementById('percentage').textContent = percentage;
            };
        });

        // Score Button Selection
        function selectScore(itemId, score) {
            // Remove selected class from all buttons for this item
            const buttons = document.querySelectorAll(`.score-btn[data-item-id="${itemId}"]`);
            buttons.forEach(btn => btn.classList.remove('selected'));
            
            // Add selected class to clicked button
            const clickedBtn = document.querySelector(`.score-btn[data-item-id="${itemId}"][data-score="${score}"]`);
            if (clickedBtn) {
                clickedBtn.classList.add('selected');
            }
            
            // Update hidden input
            document.getElementById(`score_${itemId}`).value = score;
            
            // Recalculate total
            if (typeof recalculateTotal === 'function') {
                recalculateTotal();
            }
        }
        
        // Toggle rubric table visibility
        function toggleRubricTable(itemId, event) {
            const table = document.getElementById(`rubric_table_${itemId}`);
            const button = event.target.closest('.view-rubric-btn');
            
            if (table.classList.contains('active')) {
                table.classList.remove('active');
                button.classList.remove('active');
                button.querySelector('.arrow').textContent = '▼';
            } else {
                table.classList.add('active');
                button.classList.add('active');
                button.querySelector('.arrow').textContent = '▲';
            }
        }
        
        // Review Modal Functions
        function openReviewModal(mappingId, participantName, hasPaper, paperData = null) {
            document.getElementById('reviewModal').style.display = 'flex';
            document.getElementById('mappingId').value = mappingId;
            document.getElementById('participantName').textContent = participantName;
            document.getElementById('reviewNotes').value = '';
            
            // Display or hide paper details
            const paperDetailsSection = document.getElementById('paperDetailsSection');
            const noPaperWarning = document.getElementById('noPaperWarning');
            
            if (hasPaper && paperData) {
                paperDetailsSection.style.display = 'block';
                noPaperWarning.style.display = 'none';
                
                document.getElementById('paperTitle').textContent = paperData.title || 'No title';
                
                // Handle category display
                const categoryElement = document.getElementById('paperCategory');
                if (paperData.category) {
                    categoryElement.textContent = paperData.category;
                    categoryElement.parentElement.style.display = 'block';
                } else {
                    categoryElement.parentElement.style.display = 'none';
                }
                
                document.getElementById('paperAbstract').textContent = paperData.abstract || 'No abstract provided';
                
                // Handle paper preview and link
                const paperPreviewContainer = document.getElementById('paperPreviewContainer');
                const paperPreviewFrame = document.getElementById('paperPreviewFrame');
                const paperPreviewImage = document.getElementById('paperPreviewImage');
                
                if (paperData.paper) {
                    // Show paper preview
                    paperPreviewContainer.style.display = 'block';
                    
                    // Check if it's a PDF or document based on file extension or .tmp format
                    const paperUrl = paperData.paper.toLowerCase();
                    const isPDF = paperUrl.endsWith('.pdf') || 
                                  paperUrl.endsWith('.tmp') || 
                                  paperUrl.includes('.pdf') ||
                                  paperUrl.endsWith('.doc') ||
                                  paperUrl.endsWith('.docx');
                    
                    if (isPDF) {
                        // Display PDF in iframe with #view=FitH to prevent auto-download
                        paperPreviewFrame.src = paperData.paper + '#toolbar=0&navpanes=0&scrollbar=1';
                        paperPreviewFrame.style.display = 'block';
                        paperPreviewImage.style.display = 'none';
                    } else {
                        // Display image
                        paperPreviewImage.src = paperData.paper;
                        paperPreviewImage.style.display = 'block';
                        paperPreviewFrame.style.display = 'none';
                    }
                } else {
                    paperPreviewContainer.style.display = 'none';
                }
                
                // Handle video link
                const videoLink = document.getElementById('paperVideoLink');
                if (paperData.video) {
                    videoLink.href = paperData.video;
                    videoLink.style.display = 'inline-block';
                } else {
                    videoLink.style.display = 'none';
                }
            } else {
                paperDetailsSection.style.display = 'none';
                noPaperWarning.style.display = 'block';
            }
            
            // Reset all rubric scores (hidden inputs and buttons)
            const rubricScores = document.querySelectorAll('.rubric-score');
            rubricScores.forEach(input => {
                input.value = '';
            });
            
            // Reset all score buttons
            const allScoreButtons = document.querySelectorAll('.score-btn');
            allScoreButtons.forEach(btn => {
                btn.classList.remove('selected');
            });
            
            // Reset all rubric comments (category-level)
            const categoryComments = document.querySelectorAll('.category-comment');
            categoryComments.forEach(textarea => {
                textarea.value = '';
            });
            
            // Reset total display
            if (document.getElementById('totalScore')) {
                document.getElementById('totalScore').textContent = '0';
                document.getElementById('percentage').textContent = '0';
            }
            
            // Close all rubric tables
            document.querySelectorAll('.rubric-table-container.active').forEach(table => {
                table.classList.remove('active');
            });
            document.querySelectorAll('.view-rubric-btn.active').forEach(btn => {
                btn.classList.remove('active');
                btn.querySelector('.arrow').textContent = '▼';
            });
        }

        // Edit Review Modal Function
        async function editReviewModal(mappingId, participantName, hasPaper, paperData = null) {
            // First, open the modal with paper data
            openReviewModal(mappingId, participantName, hasPaper, paperData);
            
            // Set edit mode
            document.getElementById('isEditMode').value = 'true';
            
            // Change modal title
            document.querySelector('#reviewModal h3').textContent = 'Edit Review';
            
            // Load existing scores from the server
            try {
                const response = await fetch(`{{ url('/events') }}/${{{ $event->id }}}/review/${mappingId}/scores`);
                const data = await response.json();
                
                if (data.success && data.scores) {
                    // Track which categories have had comments loaded
                    const loadedCategoryComments = new Set();
                    
                    // Load scores and comments
                    data.scores.forEach(scoreData => {
                        const rubricItemId = scoreData.rubric_item_id;
                        const score = scoreData.score;
                        const comment = scoreData.comment;
                        
                        // Set the hidden input value
                        const scoreInput = document.getElementById(`score_${rubricItemId}`);
                        if (scoreInput) {
                            scoreInput.value = score;
                            
                            // Select the corresponding button
                            const button = document.querySelector(`.score-btn[data-item-id="${rubricItemId}"][data-score="${score}"]`);
                            if (button) {
                                button.classList.add('selected');
                            }
                            
                            // Load category comment once per category
                            if (comment && scoreInput.dataset.categoryId) {
                                const categoryId = scoreInput.dataset.categoryId;
                                
                                // Only set the comment if we haven't already set it for this category
                                if (!loadedCategoryComments.has(categoryId)) {
                                    const categoryCommentTextarea = document.querySelector(`textarea[name="category_comments[${categoryId}]"]`);
                                    if (categoryCommentTextarea) {
                                        categoryCommentTextarea.value = comment;
                                        loadedCategoryComments.add(categoryId);
                                    }
                                }
                            }
                        }
                    });
                    
                    // Load review notes
                    if (data.review_notes) {
                        document.getElementById('reviewNotes').value = data.review_notes;
                    }
                    
                    // Recalculate total
                    if (typeof recalculateTotal === 'function') {
                        recalculateTotal();
                    }
                    
                    console.log('Loaded existing evaluation data successfully');
                    showToast('info', '📋 Edit Mode', `Loaded previous evaluation with ${data.scores.length} scores`, 2500);
                } else {
                    console.warn('No scores found for this evaluation');
                    showToast('warning', '⚠️ No Data Found', 'No previous evaluation data found', 3000);
                }
            } catch (error) {
                console.error('Failed to load existing scores:', error);
                showToast('error', '❌ Load Failed', 'Could not load existing evaluation data. You can still submit a new evaluation.', 4000);
            }
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.getElementById('isEditMode').value = 'false';
            document.querySelector('#reviewModal h3').textContent = 'Submit Review';
        }

        // Handle review form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const jsonData = {
                mapping_id: formData.get('mapping_id'),
                rubric_scores: {},
                category_comments: {},
                review_notes: formData.get('review_notes')
            };
            
            // Collect rubric scores
            const rubricScores = document.querySelectorAll('.rubric-score');
            // Collect category-level comments
            const categoryComments = document.querySelectorAll('.category-comment');
            
            let allScored = true;
            rubricScores.forEach(input => {
                if (!input.value) {
                    allScored = false;
                } else {
                    const rubricId = input.name.match(/\[(\d+)\]/)[1];
                    jsonData.rubric_scores[rubricId] = parseInt(input.value);
                }
            });
            
            // Collect category-level comments (one per criteria)
            categoryComments.forEach(textarea => {
                const categoryId = textarea.name.match(/\[(\d+)\]/)[1];
                if (textarea.value.trim()) {
                    jsonData.category_comments[categoryId] = textarea.value.trim();
                }
            });
            
            // Validate all criteria are scored
            if (rubricScores.length > 0 && !allScored) {
                showToast('warning', '⚠️ Incomplete Evaluation', 'Please score all evaluation criteria before submitting. All scores are required.', 4000);
                return;
            }

            console.log('Submitting review data:', jsonData);

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const isEditMode = document.getElementById('isEditMode').value === 'true';
            const originalBtnText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = isEditMode ? '⏳ Updating...' : '⏳ Submitting...';
            
            // Show brief processing notification
            showToast('info', '⏳ Processing', isEditMode ? 'Updating your evaluation...' : 'Submitting your evaluation...', 1000);

            fetch('{{ route("event.submit-review", [$event, $registration]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json().then(data => ({ status: response.status, data }));
            })
            .then(({ status, data }) => {
                console.log('Response data:', data);
                if (data.success) {
                    // Close modal immediately to show page behind
                    closeReviewModal();
                    
                    const message = isEditMode 
                        ? `✓ Evaluation updated successfully! Score: ${data.percentage || 0}%` 
                        : `✓ Evaluation submitted successfully! Score: ${data.percentage || 0}%`;
                    
                    showToast('success', isEditMode ? '📝 Evaluation Updated' : '✅ Evaluation Submitted', message, 2500);
                    
                    // Wait longer for user to see the success message
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);
                } else {
                    throw new Error(data.message || 'Failed to submit review');
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                const errorMsg = error.message || 'An unexpected error occurred';
                showToast('error', '❌ Submission Failed', errorMsg, 5000);
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });

        // Toggle Review Details
        function toggleReviewDetails(mappingId, event) {
            const detailsDiv = document.getElementById('reviewDetails' + mappingId);
            const button = event.target;
            
            if (detailsDiv.style.display === 'none') {
                detailsDiv.style.display = 'block';
                button.innerHTML = '👁️ Hide Evaluation Details';
            } else {
                detailsDiv.style.display = 'none';
                button.innerHTML = '👁️ View Evaluation Details';
            }
        }

        // Reviewer attendance check-in removed - reviewers do not need to check in
    </script>

    <!-- Toast Notification Container (at end for highest stacking) -->
    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>
@endsection
