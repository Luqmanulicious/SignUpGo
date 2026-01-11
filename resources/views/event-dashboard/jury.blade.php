@extends('layouts.app')

@section('title', 'Jury Dashboard - ' . $event->title)

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
    {{-- @auth
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
    @endauth --}}

    @php
        // Check if evaluations have been submitted
        $evaluationsSubmitted = !is_null($registration->evaluations_submitted_at);

        // Fetch assigned participants from jury_mapping table
        $assignedMappings = \DB::table('jury_mappings')
            ->where('jury_registration_id', $registration->id)
            ->where('event_id', $event->id)
            ->get();

        $totalAssigned = $assignedMappings->count();

        // Get evaluations completed count (status = 'evaluated' or 'accepted')
        $evaluationsCompleted = \DB::table('jury_mappings')
            ->where('jury_registration_id', $registration->id)
            ->where('event_id', $event->id)
            ->where('status', 'evaluated')
            ->count();

        // Fetch participant details with papers
        $assignedParticipants = [];
        foreach ($assignedMappings as $mapping) {
            $participantReg = \DB::table('event_registrations')
                ->where('id', $mapping->participant_registration_id)
                ->first();

            if ($participantReg) {
                $paper = \DB::table('event_papers')
                    ->where('event_id', $event->id)
                    ->where('user_id', $participantReg->user_id)
                    ->first();

                $user = \DB::table('users')->where('id', $participantReg->user_id)->first();

                if ($paper && $user) {
                    $assignedParticipants[] = [
                        'mapping' => $mapping,
                        'registration' => $participantReg,
                        'paper' => $paper,
                        'user' => $user,
                    ];
                }
            }
        }
    @endphp

    <a href="{{ route('registrations.index') }}"
        style="padding: 0.75rem 1.5rem; background: #6c7778; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
        ← Back to My Registrations
    </a>

    <div class="container" style="max-width: 1400px; padding: 2rem;">
        <div
            style="background: rgb(255, 255, 255); padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="margin: 0 0 0.5rem 0; color: #2c3e50;"> Jury Dashboard</h1>
                    <h2 style="margin: 0; color: #7f8c8d; font-size: 1.3rem; font-weight: 500;">{{ $event->title }}</h2>
                </div>
            </div>

            @php
                $evaluationDeadline = null;
                $evaluationDeadlineText = '';
                $isDeadlinePassed = false;

                // LOGIC CHANGED: Uses Event End Date as the deadline
                if ($event->end_date) {
                    $evaluationDeadline = \Carbon\Carbon::parse($event->end_date);
                }
                // Fallbacks for specific modes if the main end_date is missing
                elseif ($event->delivery_mode === 'face_to_face' && $event->f2f_end_date) {
                    $time = $event->f2f_end_time ?? '23:59:59';
                    $evaluationDeadline = \Carbon\Carbon::parse($event->f2f_end_date . ' ' . $time);
                } elseif ($event->delivery_mode === 'online' && $event->online_end_date) {
                    $time = $event->online_end_time ?? '23:59:59';
                    $evaluationDeadline = \Carbon\Carbon::parse($event->online_end_date . ' ' . $time);
                }

                // Format the text if a date was found
                if ($evaluationDeadline) {
                    $evaluationDeadlineText = $evaluationDeadline->format('F d, Y h:i A');
                    $isDeadlinePassed = now()->gt($evaluationDeadline);
                }
            @endphp

            @php
                $percentage = $totalAssigned > 0 ? round(($evaluationsCompleted / $totalAssigned) * 100) : 0;

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

            @if ($evaluationDeadline)
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    @if ($isDeadlinePassed)
                        {{-- Deadline Passed: Show Progress Bar --}}
                        <div
                            style="background: white; padding: 1.5rem; border-radius: 8px; flex: 1; border: 2px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <div style="font-size: 2rem;">⚠️</div>
                                <div style="flex: 1;">
                                    <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.25rem;">Evaluation
                                        Deadline (Event Ended)</div>
                                    <div style="font-size: 1.1rem; font-weight: 700; color: #2c3e50;">
                                        {{ $evaluationDeadlineText }}</div>
                                    <div style="font-size: 0.8rem; color: #e74c3c; margin-top: 0.25rem;">⚠️ Deadline has
                                        passed</div>
                                </div>
                            </div>

                            {{-- Progress Bar: Green (Completed) + Red (Missed) --}}
                            <div style="margin-top: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span style="font-size: 0.85rem; color: #27ae60; font-weight: 600;">✓ Completed:
                                        {{ $evaluationsCompleted }}</span>
                                    <span style="font-size: 0.85rem; color: #e74c3c; font-weight: 600;">✗ Missed:
                                        {{ $totalAssigned - $evaluationsCompleted }}</span>
                                </div>
                                <div
                                    style="width: 100%; height: 30px; background: #f8f9fa; border-radius: 15px; overflow: hidden; display: flex; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                    @if ($percentage > 0)
                                        <div
                                            style="width: {{ $percentage }}%; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem; transition: width 0.5s ease;">
                                            {{ $percentage }}%
                                        </div>
                                    @endif
                                    @if ($percentage < 100)
                                        <div
                                            style="width: {{ 100 - $percentage }}%; background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem; transition: width 0.5s ease;">
                                            {{ 100 - $percentage }}%
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align: center; margin-top: 0.5rem; font-size: 0.9rem; color: #6c757d;">
                                    <strong>{{ $evaluationsCompleted }}/{{ $totalAssigned }}</strong> evaluations completed
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Deadline Active: Show Countdown --}}
                        <div
                            style="background: #2c3e50; padding: 1rem; border-radius: 8px; flex: 1; color: white; display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem;">⏰</div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.25rem;">Evaluation Deadline
                                    (Event Ends)</div>
                                <div style="font-size: 1.1rem; font-weight: 700;">{{ $evaluationDeadlineText }}</div>
                                <div style="font-size: 0.8rem; opacity: 0.9; margin-top: 0.25rem;">⏳ Time remaining:
                                    {{ $evaluationDeadline->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif

                    <div
                        style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 180px;">
                        <div style="position: relative; width: 100px; height: 100px;">
                            <svg style="transform: rotate(-90deg);" width="100" height="100">
                                <circle cx="50" cy="50" r="45" stroke="#e0e0e0" stroke-width="8"
                                    fill="none" />
                                <circle cx="50" cy="50" r="45" stroke="{{ $progressColor }}" stroke-width="8"
                                    fill="none" stroke-dasharray="{{ 2 * 3.14159 * 45 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 45 * (1 - $percentage / 100) }}"
                                    stroke-linecap="round" style="transition: stroke-dashoffset 0.5s ease;" />
                            </svg>
                            <div
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: {{ $progressColor }};">
                                    {{ $percentage }}%</div>
                            </div>
                        </div>
                        <div
                            style="margin-top: 0.75rem; text-align: center; color: #7f8c8d; font-size: 0.9rem; font-weight: 600;">
                            Evaluation Progress
                        </div>
                    </div>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1.5rem;">
                <div
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Your Role</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">Jury Member</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #850593 0%, #f5576c 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Participants to Evaluate</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">{{ $totalAssigned }}</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #02777d 0%, #4facfe 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Evaluations Completed</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">
                        {{ $evaluationsCompleted }}/{{ $totalAssigned }}</div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem;">Assigned Participants</h3>

                @if ($evaluationsSubmitted)
                    <div
                        style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; padding: 1rem 2rem; border-radius: 8px; margin: 1rem 0; text-align: center; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);">
                        <strong>✅ Evaluations Submitted</strong> - Your evaluations have been finalized on
                        {{ \Carbon\Carbon::parse($registration->evaluations_submitted_at)->format('M d, Y h:i A') }}. They
                        are now
                        read-only.
                    </div>
                @endif

                @if (count($assignedParticipants) > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach ($assignedParticipants as $participant)
                            @php
                                $isEvaluated = $participant['mapping']->status === 'evaluated';
                                // Check if before jury deadline
                                $canEdit = true; // Default to true if no deadline
                                if ($registration->registration_deadline) {
                                    $canEdit = now()->lte($registration->registration_deadline);
                                }
                            @endphp
                            <div
                                style="background: {{ $isEvaluated ? '#f0f9ff' : '#fff' }}; border: 2px solid {{ $isEvaluated ? '#3b82f6' : '#e5e7eb' }}; padding: 1.5rem; border-radius: 8px;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50; font-size: 1.1rem;">👤
                                            {{ $participant['user']->name }}</h4>
                                        <p style="margin: 0; color: #7f8c8d; font-size: 0.9rem;">Title:
                                            {{ $participant['paper']->title }}</p>
                                        <p style="margin: 0.25rem 0 0 0; color: #7f8c8d; font-size: 0.85rem;">
                                            📧 {{ $participant['user']->email }}
                                        </p>
                                        @if (isset($participant['paper']->product_category) && $participant['paper']->product_category)
                                            <span
                                                style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #e0e7ff; color: #3730a3; border-radius: 12px; font-size: 0.8rem;">
                                                Category: {{ $participant['paper']->product_category }}
                                            </span>
                                        @endif
                                        @if (isset($participant['paper']->product_theme) && $participant['paper']->product_theme)
                                            <span
                                                style="display: inline-block; margin-top: 0.5rem; margin-left: 0.5rem; padding: 0.25rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.8rem;">
                                                Theme: {{ $participant['paper']->product_theme }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($isEvaluated)
                                        <span
                                            style="padding: 0.5rem 1rem; background: #22c55e; color: white; border-radius: 6px; font-size: 0.9rem; font-weight: 600;">
                                            ✓ Evaluated
                                        </span>
                                    @else
                                        <span
                                            style="padding: 0.5rem 1rem; background: #fbbf24; color: #78350f; border-radius: 6px; font-size: 0.9rem; font-weight: 600;">
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                @if ($participant['paper']->abstract)
                                    <p style="margin: 1rem 0; color: #4b5563; font-size: 0.9rem; line-height: 1.5;">
                                        {{ Str::limit($participant['paper']->abstract, 200) }}
                                    </p>
                                @endif

                                <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                                    {{-- @if ($participant['paper']->poster_path)
                                        <a href="{{ $participant['paper']->poster_path }}" target="_blank"
                                           style="padding: 0.5rem 1rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                            View Poster
                                        </a>
                                    @endif --}}
                                    @if ($participant['paper']->video_url)
                                        <a href="{{ $participant['paper']->video_url }}" target="_blank"
                                            style="padding: 0.5rem 1rem; background: #8b5cf6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                            Watch Video
                                        </a>
                                    @endif
                                    @if ($evaluationsSubmitted)
                                        <a href="{{ route('jury.evaluate', $participant['mapping']->id) }}"
                                            style="padding: 0.5rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: inline-block;">
                                            🔒 View Only
                                        </a>
                                    @else
                                        <a href="{{ route('jury.evaluate', $participant['mapping']->id) }}"
                                            style="padding: 0.5rem 1rem; background: {{ !$canEdit ? '#9ca3af' : ($isEvaluated ? '#3b82f6' : '#10b981') }}; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; {{ !$canEdit ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                            {{ $isEvaluated ? '✏️ Edit Evaluation' : '📝 Evaluate' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        style="background: #f8f9fa; padding: 3rem 2rem; border-radius: 8px; text-align: center; color: #7f8c8d;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50;">No Participants Assigned Yet</h4>
                        <p style="margin: 0;">The event organizer will assign participants to you for evaluation.</p>
                    </div>
                @endif

                {{-- Submit All Evaluation Button --}}
                @if (count($assignedParticipants) > 0 && !$evaluationsSubmitted)
                    {{-- <p>You must submit all evaluations</p> --}}
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                        @php
                            $allEvaluated = $evaluationsCompleted === $totalAssigned;
                            $remainingEvaluations = $totalAssigned - $evaluationsCompleted;
                        @endphp

                        @if ($allEvaluated)
                            <button onclick="openSubmitAllModal()"
                                style="width: 100%; padding: 1rem 2rem; background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(39, 174, 96, 0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(39, 174, 96, 0.3)'">
                                ✔️ Submit All Evaluations
                            </button>
                            <p
                                style="margin: 0.75rem 0 0 0; text-align: center; color: #27ae60; font-size: 0.9rem; font-weight: 600;">
                                ✅ All evaluations completed! Ready to submit.
                            </p>
                        @else
                            <button onclick="showIncompleteWarning()"
                                style="width: 100%; padding: 1rem 2rem; background: #95a5a6; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.background='#7f8c8d'" onmouseout="this.style.background='#95a5a6'">
                                🔒 Submit All Evaluations
                            </button>
                            <p
                                style="margin: 0.75rem 0 0 0; text-align: center; color: #e74c3c; font-size: 0.9rem; font-weight: 600;">
                                ⚠️ {{ $remainingEvaluations }} evaluation(s) remaining ({{ 100 - $percentage }}%
                                incomplete)
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right Column: Attendance & Feedback -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Attendance Section -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    @php
                        $eventStarted = $event->start_date && now()->gte($event->start_date);
                        $eventEnded = $event->end_date && now()->gt($event->end_date);
                        $eventActive = $eventStarted && !$eventEnded;
                    @endphp

                    <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem; text-align: center;">Attendance
                    </h3>

                    @if ($registration->checked_in_at)
                        <div style="background: #e8f5e9; padding: 1.5rem; border-radius: 8px; text-align: center;">
                            <p style="margin: 0; color: #2e7d32; font-weight: 600; font-size: 1.1rem;">✓ Checked In</p>
                            <p style="margin: 0.5rem 0 0 0; color: #558b2f; font-size: 0.85rem;">
                                {{ $registration->checked_in_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @elseif(!$eventStarted)
                        <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📅</div>
                            <p style="margin: 0; color: #856404; font-weight: 600;">Check-in will be available during the
                                event</p>
                            <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.85rem;">
                                Event starts: {{ $event->start_date->format('M d, Y h:i A') }}</p>
                        </div>
                    @elseif($eventEnded)
                        <div style="background: #ffebee; padding: 2rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔒</div>
                            <p style="margin: 0; color: #c62828; font-weight: 600;">Check-in is no longer available</p>
                            <p style="margin: 0.5rem 0 0 0; color: #d32f2f; font-size: 0.85rem;">
                                Event ended: {{ $event->end_date->format('M d, Y h:i A') }}</p>
                        </div>
                    @else
                        <div style="text-align: center;">
                            <button id="manualCheckInBtn"
                                style="width: 100%; padding: 0.75rem; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                                ✓ Check In
                            </button>
                            <div id="checkInMessage"
                                style="display: none; margin-top: 0.75rem; padding: 0.75rem; border-radius: 6px; text-align: center;">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Event Feedback Section -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    @php
                        $hasFeedback = \App\Models\Feedback::where(
                            'event_registration_id',
                            $registration->id,
                        )->exists();
                    @endphp

                    <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem; text-align: center;">Event Feedback
                    </h3>

                    @if (!$registration->checked_in_at)
                        <div style="background: #f8f9fa; padding: 2rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📝</div>
                            <p style="margin: 0; color: #6c757d; font-weight: 600;">Feedback available after check-in</p>
                        </div>
                    @elseif (!$eventEnded)
                        <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏳</div>
                            <p style="margin: 0; color: #856404; font-weight: 600;">Feedback available after event ends</p>
                        </div>
                    @else
                        @if ($hasFeedback)
                            <div
                                style="background: #d4edda; padding: 1.5rem; border-radius: 8px; text-align: center; border-left: 4px solid #28a745;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">✓</div>
                                <p style="margin: 0 0 0.75rem 0; color: #155724; font-weight: 600;">Feedback Submitted</p>
                                <a href="{{ route('feedback.show', $registration) }}"
                                    style="display: inline-block; padding: 0.75rem 1.5rem; background: #28a745; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                    onmouseover="this.style.background='#218838'"
                                    onmouseout="this.style.background='#28a745'">
                                    📋 View Your Feedback
                                </a>
                            </div>
                        @else
                            <div
                                style="background: #e8d9f7; padding: 1.5rem; border-radius: 8px; text-align: center; border-left: 4px solid #9b59b6;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💬</div>
                                <p style="margin: 0 0 0.5rem 0; color: #6c3483; font-weight: 600;">Share Your Experience
                                </p>
                                <p style="margin: 0 0 1rem 0; color: #7d3c98; font-size: 0.9rem;">Help us improve by
                                    providing feedback</p>
                                <a href="{{ route('feedback.create', $registration) }}"
                                    style="display: inline-block; padding: 0.75rem 1.5rem; background: #9b59b6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                    onmouseover="this.style.background='#8e44ad'"
                                    onmouseout="this.style.background='#9b59b6'">
                                    💬 Submit Event Feedback
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Submit All Confirmation Modal --}}
        <div id="submitAllModal"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; overflow-y: auto; padding: 2rem 0;">
            <div
                style="background: white; border-radius: 16px; max-width: 800px; width: 95%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: slideDown 0.3s ease-out; max-height: 90vh; overflow-y: auto;">
                {{-- Modal Header --}}
                <div
                    style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; padding: 1.5rem 2rem; border-radius: 16px 16px 0 0; position: sticky; top: 0; z-index: 10;">
                    <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700;">✔️ Confirm Evaluation Submission</h3>
                </div>

                {{-- Modal Body --}}
                <div style="padding: 2rem 2.5rem;">
                    <div
                        style="background: #fff3cd; border-left: 4px solid #f39c12; padding: 1rem; margin-bottom: 1.5rem; border-radius: 6px;">
                        <p style="margin: 0; color: #856404; font-size: 0.95rem; line-height: 1.6;">
                            <strong>⚠️ Important:</strong> Once you submit all evaluations, they will be finalized and sent
                            to
                            the event organizer. Please review carefully before confirming.
                        </p>
                    </div>

                    <div style="background: #f8f9fa; padding: 1.75rem; border-radius: 8px; margin-bottom: 2rem;">
                        <p style="margin: 0 0 0.75rem 0; color: #2c3e50; font-weight: 700; font-size: 1.05rem;">📊
                            Evaluation
                            Summary:</p>
                        <p style="margin: 0.5rem 0; color: #555; font-size: 1rem;">• Total Participants:
                            <strong>{{ $totalAssigned }}</strong>
                        </p>
                        <p style="margin: 0.5rem 0; color: #555; font-size: 1rem;">• Evaluations Completed:
                            <strong>{{ $evaluationsCompleted }}</strong>
                        </p>
                        <p style="margin: 0.75rem 0 0 0; color: #27ae60; font-weight: 700; font-size: 1rem;">✅ All
                            evaluations
                            are ready for submission</p>
                    </div>

                    {{-- Confirmation Checkboxes --}}
                    <div style="margin-bottom: 2rem;">
                        <label
                            style="display: flex; align-items: start; gap: 1rem; padding: 1.25rem; background: white; border: 2px solid #e5e7eb; border-radius: 8px; margin-bottom: 1.25rem; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.borderColor='#3498db'; this.style.background='#f0f9ff'"
                            onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                            <input type="checkbox" id="confirmRubric"
                                style="width: 22px; height: 22px; cursor: pointer; margin-top: 0.25rem; flex-shrink: 0;"
                                onchange="checkAllConfirmations()">
                            <span style="flex: 1; color: #2c3e50; line-height: 1.6; font-size: 1rem;">
                                I confirm that all marks given are based on the evaluation rubric and reflect fair
                                assessment of
                                each participant's work.
                            </span>
                        </label>

                        <label
                            style="display: flex; align-items: start; gap: 1rem; padding: 1.25rem; background: white; border: 2px solid #e5e7eb; border-radius: 8px; margin-bottom: 1.25rem; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.borderColor='#3498db'; this.style.background='#f0f9ff'"
                            onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                            <input type="checkbox" id="confirmReview"
                                style="width: 22px; height: 22px; cursor: pointer; margin-top: 0.25rem; flex-shrink: 0;"
                                onchange="checkAllConfirmations()">
                            <span style="flex: 1; color: #2c3e50; line-height: 1.6; font-size: 1rem;">
                                I have carefully reviewed all my evaluations and they accurately represent my professional
                                judgment.
                            </span>
                        </label>

                        <label
                            style="display: flex; align-items: start; gap: 1rem; padding: 1.25rem; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.borderColor='#3498db'; this.style.background='#f0f9ff'"
                            onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                            <input type="checkbox" id="confirmFinal"
                                style="width: 22px; height: 22px; cursor: pointer; margin-top: 0.25rem; flex-shrink: 0;"
                                onchange="checkAllConfirmations()">
                            <span style="flex: 1; color: #2c3e50; line-height: 1.6; font-size: 1rem;">
                                I understand that after submission, I will not be able to change the marks or dispute my
                                submitted evaluations.
                            </span>
                        </label>
                    </div>

                    {{-- Action Buttons --}}
                    <div style="display: flex; gap: 1.5rem; margin-top: 2rem;">
                        <button onclick="closeSubmitAllModal()"
                            style="flex: 1; padding: 1rem 2rem; background: #95a5a6; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 1.05rem; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.background='#7f8c8d'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='#95a5a6'; this.style.transform='translateY(0)'">
                            ✖ Cancel
                        </button>
                        <button id="finalSubmitBtn" onclick="submitAllEvaluations()" disabled
                            style="flex: 1; padding: 1rem 2rem; background: #95a5a6; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 1.05rem; cursor: not-allowed; transition: all 0.3s;">
                            🔒 Confirm & Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes slideDown {
                from {
                    transform: translateY(-100px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes slideInRight {
                from {
                    transform: translateX(500px);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }

                to {
                    transform: translateX(500px);
                    opacity: 0;
                }
            }

            @keyframes progress {
                from {
                    width: 100%;
                }

                to {
                    width: 0%;
                }
            }
        </style>

        <script>
            // Submit All Modal Functions
            function openSubmitAllModal() {
                document.getElementById('submitAllModal').style.display = 'flex';
                // Reset checkboxes
                document.getElementById('confirmRubric').checked = false;
                document.getElementById('confirmReview').checked = false;
                document.getElementById('confirmFinal').checked = false;
                checkAllConfirmations();
            }

            function closeSubmitAllModal() {
                document.getElementById('submitAllModal').style.display = 'none';
            }

            function checkAllConfirmations() {
                const rubricChecked = document.getElementById('confirmRubric').checked;
                const reviewChecked = document.getElementById('confirmReview').checked;
                const finalChecked = document.getElementById('confirmFinal').checked;
                const submitBtn = document.getElementById('finalSubmitBtn');

                if (rubricChecked && reviewChecked && finalChecked) {
                    submitBtn.disabled = false;
                    submitBtn.style.background = 'linear-gradient(135deg, #27ae60 0%, #229954 100%)';
                    submitBtn.style.cursor = 'pointer';
                    submitBtn.innerHTML = '✔️ Confirm & Submit';
                    submitBtn.onmouseover = function() {
                        this.style.background = 'linear-gradient(135deg, #229954 0%, #1e8449 100%)';
                        this.style.transform = 'translateY(-2px)';
                    };
                    submitBtn.onmouseout = function() {
                        this.style.background = 'linear-gradient(135deg, #27ae60 0%, #229954 100%)';
                        this.style.transform = 'translateY(0)';
                    };
                } else {
                    submitBtn.disabled = true;
                    submitBtn.style.background = '#95a5a6';
                    submitBtn.style.cursor = 'not-allowed';
                    submitBtn.innerHTML = '🔒 Confirm & Submit';
                    submitBtn.onmouseover = null;
                    submitBtn.onmouseout = null;
                }
            }

            function showIncompleteWarning() {
                const remaining = {{ $totalAssigned - $evaluationsCompleted }};
                const percentage = {{ $percentage }};

                alert(
                    `⚠️ Evaluation Incomplete\n\nYou have ${remaining} participant(s) left to evaluate.\n\nCurrent Progress: ${percentage}%\n\nPlease complete all evaluations before submitting.`
                    );
            }

            function submitAllEvaluations() {
                const submitBtn = document.getElementById('finalSubmitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Submitting...';
                submitBtn.style.background = '#95a5a6';

                fetch('{{ route('jury.submit-all', $registration->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            confirm_rubric: true,
                            confirm_review: true,
                            confirm_final: true
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                // User-friendly error message
                                const errorMsg = data.message || 'Unable to submit evaluations at this time. Please try again.';
                                throw new Error(errorMsg);
                            }).catch(jsonError => {
                                // If JSON parsing fails, show generic error
                                throw new Error('Unable to submit evaluations. Please check your connection and try again.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            closeSubmitAllModal();
                            showCustomToast('Success!', data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Submission failed');
                        }
                    })
                    .catch(error => {
                        showCustomToast('Submission Failed', error.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '✔️ Confirm & Submit';
                        submitBtn.style.background = 'linear-gradient(135deg, #27ae60 0%, #229954 100%)';
                    });
            }

            // Custom Toast Notification Function
            function showCustomToast(title, message, type = 'success') {
                const existingToast = document.getElementById('customToast');
                if (existingToast) {
                    existingToast.remove();
                }

                const colors = {
                    success: {
                        bg: 'linear-gradient(135deg, #27ae60 0%, #229954 100%)',
                        icon: '✅'
                    },
                    error: {
                        bg: 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
                        icon: '❌'
                    },
                    warning: {
                        bg: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                        icon: '⚠️'
                    },
                    info: {
                        bg: 'linear-gradient(135deg, #3498db 0%, #2980b9 100%)',
                        icon: 'ℹ️'
                    }
                };

                const color = colors[type] || colors.success;

                const toast = document.createElement('div');
                toast.id = 'customToast';
                toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                min-width: 350px;
                max-width: 500px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                z-index: 10000;
                overflow: hidden;
                animation: slideInRight 0.4s ease-out;
            `;

                toast.innerHTML = `
                <div style="background: ${color.bg}; color: white; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">${color.icon}</span>
                    <strong style="font-size: 1.1rem; flex: 1;">${title}</strong>
                    <button onclick="this.closest('#customToast').remove()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='none'">&times;</button>
                </div>
                <div style="padding: 1.25rem 1.5rem; color: #2c3e50; line-height: 1.6; font-size: 0.95rem;">
                    ${message}
                </div>
                <div style="height: 4px; background: rgba(0,0,0,0.1); position: relative; overflow: hidden;">
                    <div style="height: 100%; width: 100%; background: ${color.bg}; animation: progress 5s linear;"></div>
                </div>
            `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast && toast.parentElement) {
                        toast.style.animation = 'slideOutRight 0.4s ease-out';
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 5000);
            }

            // Close modal when clicking outside
            document.getElementById('submitAllModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeSubmitAllModal();
                }
            });

            // Manual Check-In
            document.getElementById('manualCheckInBtn')?.addEventListener('click', function() {
                const btn = this;
                const messageDiv = document.getElementById('checkInMessage');

                btn.disabled = true;
                btn.textContent = 'Processing...';
                btn.style.background = '#95a5a6';

                // Get fresh CSRF token
                fetch('/csrf-token')
                    .then(response => response.json())
                    .then(tokenData => {
                        const csrfToken = tokenData.token || '{{ csrf_token() }}';

                        return fetch('{{ route('event.check-in', [$event, $registration]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                    })
                    .catch(() => {
                        // If CSRF endpoint fails, use the existing token
                        return fetch('{{ route('event.check-in', [$event, $registration]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                    })
                    .then(response => {
                        if (response.status === 419) {
                            throw new Error('Session expired. Please refresh the page and try again.');
                        }
                        if (!response.ok) {
                            throw new Error('Server returned status ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            messageDiv.style.display = 'block';
                            messageDiv.style.background = '#d4edda';
                            messageDiv.style.color = '#155724';
                            messageDiv.textContent = '✓ ' + data.message + ' at ' + data.checked_in_at;

                            btn.style.background = '#27ae60';
                            btn.textContent = '✓ Checked In Successfully!';

                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            throw new Error(data.message || 'Check-in failed');
                        }
                    })
                    .catch(error => {
                        messageDiv.style.display = 'block';
                        messageDiv.style.background = '#f8d7da';
                        messageDiv.style.color = '#721c24';
                        messageDiv.textContent = '✗ ' + error.message;

                        btn.disabled = false;
                        btn.textContent = '✓ Mark Attendance Manually';
                        btn.style.background = '#27ae60';
                    });
            });
        </script>
    @endsection
