@extends('layouts.app')

@section('title', 'My Certificates')

@section('content')
    <div class="container" style="max-width: 1400px; padding: 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="margin: 0 0 0.5rem 0; color: #2c3e50;">🎓 My Certificates</h1>
                    <p style="margin: 0; color: #7f8c8d; font-size: 1rem;">View and download your event participation certificates</p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2rem; color: #667eea;">{{ $certificates->count() }}</div>
                    <div style="font-size: 0.9rem; color: #7f8c8d; font-weight: 600;">Total Certificates</div>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #28a745;">
                    <strong>✓ Success!</strong> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #dc3545;">
                    <strong>✗ Error!</strong> {{ session('error') }}
                </div>
            @endif

            @if($certificates->isEmpty())
                <div style="background: #f8f9fa; padding: 4rem 2rem; border-radius: 12px; text-align: center;">
                    <div style="font-size: 5rem; margin-bottom: 1rem; opacity: 0.5;">🎓</div>
                    <h3 style="margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.5rem;">No Certificates Yet</h3>
                    <p style="margin: 0 0 2rem 0; color: #7f8c8d; font-size: 1rem;">Your certificates will appear here once you complete events.</p>
                    <a href="{{ route('events.index') }}" style="display: inline-block; padding: 0.75rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        📝 Browse Events
                    </a>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                    @foreach($certificates as $certificate)
                        @php
                            // Determine color scheme based on event category
                            $isConference = strtolower($certificate->event_category ?? '') === 'conference';
                            
                            if ($isConference) {
                                // Golden gradient for Conference
                                $gradientStart = '#f39c12';
                                $gradientEnd = '#e67e22';
                                $shadowColor = 'rgba(243, 156, 18, 0.3)';
                                $shadowColorHover = 'rgba(243, 156, 18, 0.4)';
                                $buttonColor = '#f39c12';
                            } else {
                                // Purple/Blue gradient for Innovation
                                $gradientStart = '#667eea';
                                $gradientEnd = '#764ba2';
                                $shadowColor = 'rgba(102, 126, 234, 0.3)';
                                $shadowColorHover = 'rgba(102, 126, 234, 0.4)';
                                $buttonColor = '#667eea';
                            }
                        @endphp
                        
                        <div style="background: linear-gradient(135deg, {{ $gradientStart }} 0%, {{ $gradientEnd }} 100%); border-radius: 12px; padding: 1.5rem; color: white; box-shadow: 0 4px 12px {{ $shadowColor }}; position: relative; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px {{ $shadowColorHover }}'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px {{ $shadowColor }}'">
                            {{-- Decorative Elements --}}
                            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                            <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                            
                            <div style="position: relative; z-index: 1;">
                                {{-- Certificate Icon & Type --}}
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div style="font-size: 2.5rem;">🎓</div>
                                    <div style="display: flex; flex-direction: column; gap: 0.35rem; align-items: flex-end;">
                                        @if($certificate->event_category)
                                            <span style="background: rgba(255,255,255,0.3); padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                                {{ $certificate->event_category }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Event Name --}}
                                <h3 style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 700; line-height: 1.3;">
                                    {{ $certificate->event_name }}
                                </h3>

                                {{-- Participant Info --}}
                                <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.2);">
                                    <p style="margin: 0 0 0.35rem 0; font-size: 0.9rem; opacity: 0.9;">
                                        <strong>Name:</strong> {{ $certificate->participant_name }}
                                    </p>
                                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">
                                        <strong>Role:</strong> {{ ucfirst($certificate->registration_role ?? $certificate->participant_role) }}
                                    </p>
                                </div>

                                {{-- Event Date & Generated Date --}}
                                <div style="margin-bottom: 1.25rem;">
                                    <p style="margin: 0 0 0.35rem 0; font-size: 0.85rem; opacity: 0.8;">
                                        📅 Event Date: {{ $certificate->event_date }}
                                    </p>
                                    <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">
                                        ⏰ Generated: {{ \Carbon\Carbon::parse($certificate->generated_at)->format('M d, Y h:i A') }}
                                    </p>
                                </div>

                                {{-- Certificate Link Button --}}
                                <a href="{{ route('certificates.download', $certificate->id) }}" target="_blank" style="display: block; width: 100%; padding: 0.75rem; background: white; color: {{ $buttonColor }}; text-decoration: none; border-radius: 8px; font-weight: 700; text-align: center; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.2);" onmouseover="this.style.background='#f8f9fa'; this.style.transform='scale(1.02)'" onmouseout="this.style.background='white'; this.style.transform='scale(1)'">
                                    🔗 Open Certificate
                                </a>

                                {{-- Downloaded Indicator --}}
                                @if($certificate->downloaded_at)
                                    <p style="margin: 0.75rem 0 0 0; font-size: 0.75rem; opacity: 0.7; text-align: center;">
                                        ✓ Opened on {{ \Carbon\Carbon::parse($certificate->downloaded_at)->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
