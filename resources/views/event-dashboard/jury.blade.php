@extends('layouts.app')

@section('title', 'Jury Dashboard - ' . $event->title)

@section('content')
    @php
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
                
                $user = \DB::table('users')
                    ->where('id', $participantReg->user_id)
                    ->first();
                
                if ($paper && $user) {
                    $assignedParticipants[] = [
                        'mapping' => $mapping,
                        'registration' => $participantReg,
                        'paper' => $paper,
                        'user' => $user
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
            style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="margin: 0 0 0.5rem 0; color: #2c3e50;"> Jury Dashboard</h1>
                    <h2 style="margin: 0; color: #7f8c8d; font-size: 1.3rem; font-weight: 500;">{{ $event->title }}</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1.5rem;">
                <div
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Your Role</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">Jury Member</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Participants to Evaluate</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">{{ $totalAssigned }}</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Evaluations Completed</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">{{ $evaluationsCompleted }}/{{ $totalAssigned }}</div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem;">Assigned Participants</h3>

                @if(count($assignedParticipants) > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($assignedParticipants as $participant)
                            @php
                                $isEvaluated = $participant['mapping']->status === 'evaluated';
                                // Check if before jury deadline
                                $canEdit = true; // Default to true if no deadline
                                if ($registration->registration_deadline) {
                                    $canEdit = now()->lte($registration->registration_deadline);
                                }
                            @endphp
                            <div style="background: {{ $isEvaluated ? '#f0f9ff' : '#fff' }}; border: 2px solid {{ $isEvaluated ? '#3b82f6' : '#e5e7eb' }}; padding: 1.5rem; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.5rem 0; color: #2c3e50; font-size: 1.1rem;">👤 {{ $participant['user']->name }}</h4>
                                        <p style="margin: 0; color: #7f8c8d; font-size: 0.9rem;">Title: {{ $participant['paper']->title }}</p>
                                        <p style="margin: 0.25rem 0 0 0; color: #7f8c8d; font-size: 0.85rem;">
                                            📧 {{ $participant['user']->email }}
                                        </p>
                                        @if(isset($participant['paper']->product_category) && $participant['paper']->product_category)
                                            <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #e0e7ff; color: #3730a3; border-radius: 12px; font-size: 0.8rem;">
                                                Category: {{ $participant['paper']->product_category }}
                                            </span>
                                        @endif
                                        @if(isset($participant['paper']->product_theme) && $participant['paper']->product_theme)
                                            <span style="display: inline-block; margin-top: 0.5rem; margin-left: 0.5rem; padding: 0.25rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.8rem;">
                                                Theme: {{ $participant['paper']->product_theme }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($isEvaluated)
                                        <span style="padding: 0.5rem 1rem; background: #22c55e; color: white; border-radius: 6px; font-size: 0.9rem; font-weight: 600;">
                                            ✓ Evaluated
                                        </span>
                                    @else
                                        <span style="padding: 0.5rem 1rem; background: #fbbf24; color: #78350f; border-radius: 6px; font-size: 0.9rem; font-weight: 600;">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                                
                                @if($participant['paper']->abstract)
                                    <p style="margin: 1rem 0; color: #4b5563; font-size: 0.9rem; line-height: 1.5;">
                                        {{ Str::limit($participant['paper']->abstract, 200) }}
                                    </p>
                                @endif
                                
                                <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                                    {{-- @if($participant['paper']->poster_path)
                                        <a href="{{ $participant['paper']->poster_path }}" target="_blank"
                                           style="padding: 0.5rem 1rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                            View Poster
                                        </a>
                                    @endif --}}
                                    @if($participant['paper']->video_url)
                                        <a href="{{ $participant['paper']->video_url }}" target="_blank"
                                           style="padding: 0.5rem 1rem; background: #8b5cf6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                            Watch Video
                                        </a>
                                    @endif
                                    <a href="{{ route('jury.evaluate', $participant['mapping']->id) }}"
                                       style="padding: 0.5rem 1rem; background: {{ !$canEdit ? '#9ca3af' : ($isEvaluated ? '#3b82f6' : '#10b981') }}; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; {{ !$canEdit ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                        {{ !$canEdit ? '🔒 Deadline Passed' : ($isEvaluated ? '📝 Edit Evaluation' : '✍️ Evaluate Now') }}
                                    </a>
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
            </div>

            <!-- Attendance Section -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                @php
                    $eventStarted = $event->start_date && now()->gte($event->start_date);
                    $eventEnded = $event->end_date && now()->gt($event->end_date);
                    $eventActive = $eventStarted && !$eventEnded;
                    // QR code functionality temporarily disabled
                    $presentationQR = null;
                @endphp
                
                <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem; text-align: center;">Attendance</h3>

                @if ($registration->checked_in_at)
                    <div style="background: #e8f5e9; padding: 1.5rem; border-radius: 8px; text-align: center;">
                        <p style="margin: 0; color: #2e7d32; font-weight: 600; font-size: 1.1rem;">✓ Checked In</p>
                        <p style="margin: 0.5rem 0 0 0; color: #558b2f; font-size: 0.85rem;">
                            {{ $registration->checked_in_at->format('M d, Y h:i A') }}</p>
                        
                        @if($eventEnded)
                            <a href="{{ route('feedback.create', $registration) }}"
                               style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                               onmouseover="this.style.background='#229954'"
                               onmouseout="this.style.background='#27ae60'">
                                💬 Submit Event Feedback
                            </a>
                        @endif
                    </div>
                @elseif(!$eventStarted)
                    <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📅</div>
                        <p style="margin: 0; color: #856404; font-weight: 600;">Attendance check-in will be available during the event</p>
                        <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.85rem;">
                            Event starts: {{ $event->start_date->format('M d, Y h:i A') }}</p>
                    </div>
                @elseif($eventEnded)
                    <div style="background: #ffebee; padding: 2rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔒</div>
                        <p style="margin: 0; color: #c62828; font-weight: 600;">Attendance check-in is no longer available</p>
                        <p style="margin: 0.5rem 0 0 0; color: #d32f2f; font-size: 0.85rem;">
                            Event ended: {{ $event->end_date->format('M d, Y h:i A') }}</p>
                    </div>
                @else
                    @if($presentationQR)
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Option 1: Scan QR Code</p>
                            <img src="{{ $presentationQR->qr_image_url }}" alt="Attendance QR Code"
                                style="width: 100%; max-width: 250px; border: 4px solid #667eea; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin-bottom: 1rem;">
                            <p style="margin: 0 0 0 0; color: #7f8c8d; font-size: 0.9rem;">Scan this QR code at the event venue</p>
                        </div>

                        <div style="border-top: 2px solid #e0e0e0; padding-top: 1.5rem; margin-top: 1.5rem; text-align: center;">
                            <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Option 2: Manual Check-In</p>
                    @else
                        <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 1.5rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏳</div>
                            <p style="margin: 0; color: #856404;">QR code is being generated...</p>
                        </div>

                        <div style="border-top: 2px solid #e0e0e0; padding-top: 1.5rem; text-align: center;">
                            <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Manual Check-In</p>
                    @endif
                    
                            <button id="manualCheckInBtn"
                                style="width: 100%; padding: 0.75rem; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                                ✓ Mark Attendance Manually
                            </button>
                            <div id="checkInMessage"
                                style="display: none; margin-top: 0.75rem; padding: 0.75rem; border-radius: 6px; text-align: center;">
                            </div>
                        </div>
                @endif
            </div>
        </div>
    </div>

    <script>
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
