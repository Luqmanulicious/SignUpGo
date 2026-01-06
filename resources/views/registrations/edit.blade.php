@extends('layouts.app')

@section('title', 'Edit {{ $registration->role === "jury" ? "Expertise" : "Paper" }} - ' . $event->title)

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
<style>
    .container { 
        max-width: 800px;
        width: 100%;
    }
    
    .card { 
        background: white; 
        padding: 2rem; 
        border-radius: 8px; 
        box-shadow: 0 2px 6px rgba(0,0,0,0.06); 
    }
    
    .event-header {
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
    
    .event-header h2 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-size: 1.5rem;
    }
    
    .event-header .meta {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .edit-notice {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        color: #856404;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-weight: 500;
    }
    
    .form-group label.required::after {
        content: ' *';
        color: #e74c3c;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }
    
    .help-text {
        margin-top: 0.25rem;
        font-size: 0.85rem;
        color: #7f8c8d;
    }
    
    .error-text {
        color: #e74c3c;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
    
    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #ecf0f1;
    }
    
    .btn {
        display: inline-block;
        padding: 0.75rem 2rem;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .btn-primary {
        background: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }
    
    .current-file {
        background: #d4edda;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        border-left: 4px solid #28a745;
        color: #155724;
    }
    
    .expertise-option {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .expertise-option:hover {
        border-color: #3498db;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.1);
    }

    .expertise-option input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        flex-shrink: 0;
    }

    .expertise-option.selected {
        border-color: #3498db;
        background: #e3f2fd;
    }

    .expertise-label {
        font-size: 1rem;
        color: #2c3e50;
        font-weight: 500;
        flex: 1;
    }

    .expertise-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('registrations.index') }}" style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; background: #6c7778; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
            ← Back to My Registrations
        </a>
    </div>

    <div class="card">
        <div class="event-header">
            <h2>{{ $event->title }}</h2>
            <p class="meta">
                {{ $event->event_type }} Event • 
                @php
                    try {
                        $start = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'TBA';
                    } catch (\Exception $e) {
                        $start = 'TBA';
                    }
                @endphp
                {{ $start }}
            </p>
        </div>

        @if($registration->role === 'jury')
            {{-- Jury Registration Edit Section --}}
            <div class="edit-notice">
                <strong>📝 Update Your Registration</strong><br>
                Update your expertise areas and application notes. This helps us assign you to review the most relevant submissions.
            </div>

            <h3 style="margin-top: 0; color: #2c3e50;">Edit Your Registration</h3>
        @else
            {{-- Participant Paper Edit Section --}}
            <div class="edit-notice">
                <strong>📝 Editing Paper Submission</strong><br>
                Update your paper details below. All changes will be saved automatically and can be edited until the submission deadline.
            </div>

            <h3 style="margin-top: 0; color: #2c3e50;">Edit Your Paper Submission</h3>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('registrations.update', $registration) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($registration->role === 'jury')
                {{-- Jury Expertise Form --}}
                @php
                    $isInnovation = stripos($event->event_type, 'innovation') !== false;
                    $isConference = stripos($event->event_type, 'conference') !== false;
                    
                    $categories = [];
                    $themes = [];
                    
                    // Innovation events: show both categories and themes
                    if ($isInnovation) {
                        // Handle innovation_categories
                        $innovationCategories = $event->innovation_categories;
                        if (is_string($innovationCategories)) {
                            $innovationCategories = json_decode($innovationCategories, true) ?? [];
                        }
                        if (is_array($innovationCategories) && count($innovationCategories) > 0) {
                            $categories = $innovationCategories;
                        }
                        
                        // Handle innovation_theme
                        $innovationTheme = $event->innovation_theme;
                        if (is_string($innovationTheme)) {
                            $innovationTheme = json_decode($innovationTheme, true) ?? [];
                        }
                        if (is_array($innovationTheme) && count($innovationTheme) > 0) {
                            $themes = $innovationTheme;
                        }
                    }
                    
                    // Conference events: show themes only (no categories)
                    if ($isConference) {
                        $conferenceTheme = $event->conference_theme;
                        if (is_string($conferenceTheme)) {
                            $conferenceTheme = json_decode($conferenceTheme, true) ?? [];
                        }
                        if (is_array($conferenceTheme) && count($conferenceTheme) > 0) {
                            $themes = $conferenceTheme;
                        }
                    }
                    
                    // Get current selections
                    $selectedCategories = old('jury_categories', $registration->jury_categories ?? []);
                    $selectedThemes = old('jury_themes', $registration->jury_themes ?? []);
                @endphp
                
                {{-- Innovation Events: Show Categories --}}
                @if($isInnovation && count($categories) > 0)
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="required">Product Categories</label>
                    <p class="help-text" style="margin-bottom: 1rem;">Select the categories you have expertise in. You can select multiple.</p>
                    <div class="expertise-grid">
                        @foreach($categories as $index => $category)
                        <label class="expertise-option {{ in_array($category, $selectedCategories) ? 'selected' : '' }}" onclick="toggleExpertise(event, 'category_{{ $index }}')" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="jury_categories[]" 
                                value="{{ $category }}" 
                                id="category_{{ $index }}"
                                {{ in_array($category, $selectedCategories) ? 'checked' : '' }}
                            >
                            <span class="expertise-label">{{ $category }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('jury_categories')
                        <div class="error-text" style="margin-top: 0.75rem; padding: 0.75rem; background: #fee; border-left: 3px solid #e74c3c; border-radius: 4px;">
                            <strong>⚠️ Required:</strong> {{ $message }}
                        </div>
                    @enderror
                </div>
                @endif
                
                {{-- Show Themes (for both Innovation and Conference) --}}
                @if(count($themes) > 0)
                <div class="form-group">
                    <label class="required">{{ $isInnovation ? 'Product' : 'Paper' }} Themes</label>
                    <p class="help-text" style="margin-bottom: 1rem;">Select the themes you have expertise in. You can select multiple.</p>
                    <div class="expertise-grid">
                        @foreach($themes as $index => $theme)
                        <label class="expertise-option {{ in_array($theme, $selectedThemes) ? 'selected' : '' }}" onclick="toggleExpertise(event, 'theme_{{ $index }}')" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="jury_themes[]" 
                                value="{{ $theme }}" 
                                id="theme_{{ $index }}"
                                {{ in_array($theme, $selectedThemes) ? 'checked' : '' }}
                            >
                            <span class="expertise-label">{{ $theme }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('jury_themes')
                        <div class="error-text" style="margin-top: 0.75rem; padding: 0.75rem; background: #fee; border-left: 3px solid #e74c3c; border-radius: 4px;">
                            <strong>⚠️ Required:</strong> {{ $message }}
                        </div>
                    @enderror
                </div>
                @endif

                {{-- Application Notes field for jury --}}
                <div class="form-group">
                    <label for="application_notes">Application Notes (Optional)</label>
                    <textarea 
                        name="application_notes" 
                        id="application_notes"
                        class="form-control" 
                        rows="4"
                        maxlength="1000"
                        placeholder="Add any additional notes or information about your application (max 1000 characters)"
                    >{{ old('application_notes', $registration->application_notes) }}</textarea>
                    <p class="help-text">You can add any relevant information about your experience or availability.</p>
                    @error('application_notes')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div style="background: #e3f2fd; padding: 1rem; border-radius: 6px; border-left: 4px solid #2196f3; margin-bottom: 1.5rem;">
                    <p style="margin: 0; color: #1565c0;">
                        <strong>ℹ️ Note:</strong> Your updated information will be reviewed by the organizer to ensure proper assignment of submissions.
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Update Registration
                    </button>
                    <a href="{{ route('registrations.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>

            @else
                {{-- Participant Paper Form --}}

                {{-- Participant Paper Form --}}
                @php
                    $isInnovation = stripos($event->event_type, 'innovation') !== false;
                    $isConference = stripos($event->event_type, 'conference') !== false;
                    
                    $categories = [];
                    $themes = [];
                    
                    // Innovation events: show both categories and themes
                    if ($isInnovation) {
                        // Handle innovation_categories
                        $innovationCategories = $event->innovation_categories;
                        if (is_array($innovationCategories) && count($innovationCategories) > 0) {
                            $categories = $innovationCategories;
                        }
                        
                        // Handle innovation_theme
                        $innovationTheme = $event->innovation_theme;
                        if (is_array($innovationTheme) && count($innovationTheme) > 0) {
                            $themes = $innovationTheme;
                        }
                    }
                    
                    // Conference events: show themes only (no categories)
                    if ($isConference) {
                        $conferenceCategories = $event->conference_categories;
                        if (is_array($conferenceCategories) && count($conferenceCategories) > 0) {
                            $themes = $conferenceCategories;
                        }
                    }
                @endphp
                
                {{-- Innovation Events Only: Show Categories --}}
                @if($isInnovation && count($categories) > 0)
                <div class="form-group">
                    <label class="required" for="paper_category">Product Category</label>
                    <select name="paper_category" id="paper_category" class="form-control" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('paper_category', $paper->product_category) == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <p class="help-text">Choose the category that best fits your product.</p>
                    @error('paper_category')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                @endif
                
                {{-- Show Themes (for both Innovation and Conference) --}}
                @if(count($themes) > 0)
                <div class="form-group">
                    <label class="required" for="paper_theme">{{ $isInnovation ? 'Product' : 'Paper' }} Theme</label>
                    <select name="paper_theme" id="paper_theme" class="form-control" required>
                        <option value="">Select a theme</option>
                        @foreach($themes as $theme)
                            <option value="{{ $theme }}" {{ old('paper_theme', $paper->product_theme) == $theme ? 'selected' : '' }}>
                                {{ $theme }}
                            </option>
                        @endforeach
                    </select>
                    <p class="help-text">Choose the theme that best fits your {{ $isInnovation ? 'product' : 'paper' }}.</p>
                    @error('paper_theme')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                @endif

            <div class="form-group">
                <label class="required" for="paper_title">Paper Title</label>
                <input 
                    type="text" 
                    name="paper_title" 
                    id="paper_title"
                    class="form-control"
                    placeholder="Enter your paper title"
                    value="{{ old('paper_title', $paper->title) }}"
                    maxlength="255"
                    required
                >
                @error('paper_title')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="required" for="paper_abstract">Abstract</label>
                <textarea 
                    name="paper_abstract" 
                    id="paper_abstract"
                    class="form-control" 
                    rows="6"
                    maxlength="2000"
                    placeholder="Provide a brief abstract of your paper (max 2000 characters)"
                    required
                >{{ old('paper_abstract', $paper->abstract) }}</textarea>
                <p class="help-text">Remaining characters: <span id="char-count">{{ 2000 - strlen($paper->abstract) }}</span></p>
                @error('paper_abstract')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="paper_poster">Poster / Presentation File</label>
                
                @if($paper->poster_path)
                    <div class="current-file">
                        <p style="margin: 0;">
                            <strong>✓ Current file:</strong> {{ basename(parse_url($paper->poster_path, PHP_URL_PATH)) }}
                        </p>
                    </div>
                    <p class="help-text">Upload a new file to replace the current one (optional)</p>
                @endif
                
                <input 
                    type="file" 
                    name="paper_poster" 
                    id="paper_poster"
                    class="form-control"
                    accept="{{ $isInnovation ? 'image/*' : '.doc,.docx,.pdf' }}"
                >
                <p class="help-text">{{ $isInnovation ? 'Upload your presentation poster (JPG, PNG, or PDF - Max 10MB)' : 'Upload your paper document (DOC, DOCX, or PDF - Max 10MB)' }}</p>
                @error('paper_poster')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="paper_video_url">Video URL (Optional)</label>
                <input 
                    type="url" 
                    name="paper_video_url" 
                    id="paper_video_url"
                    class="form-control"
                    placeholder="https://youtube.com/watch?v=..."
                    value="{{ old('paper_video_url', $paper->video_url) }}"
                >
                <p class="help-text">Provide a link to your video presentation (YouTube, Google Drive, Vimeo, etc.)</p>
                @error('paper_video_url')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div style="background: #e3f2fd; padding: 1rem; border-radius: 6px; border-left: 4px solid #2196f3; margin-bottom: 1.5rem;">
                <p style="margin: 0; color: #1565c0;">
                    <strong>ℹ️ Note:</strong> You can edit your paper anytime before the submission deadline. Your updates will be saved automatically and reviewed by the organizer.
                </p>
            </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Update Paper
                    </button>
                    <a href="{{ route('registrations.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
@if($registration->role === 'jury')
    // Jury expertise toggle function
    function toggleExpertise(event, checkboxId) {
        event.preventDefault(); // Prevent default label behavior
        
        const checkbox = document.getElementById(checkboxId);
        const label = event.currentTarget;
        
        // Don't toggle if user clicked directly on the checkbox
        if (event.target === checkbox) {
            // Checkbox will toggle itself, just update visual
            setTimeout(() => {
                if (checkbox.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            }, 0);
            return;
        }
        
        // Toggle checkbox when clicking on label
        checkbox.checked = !checkbox.checked;
        
        // Trigger change event for form validation
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Toggle visual selection
        if (checkbox.checked) {
            label.classList.add('selected');
        } else {
            label.classList.remove('selected');
        }
    }
    
    // Initialize checkbox visual states on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.expertise-option input[type="checkbox"]').forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.closest('.expertise-option').classList.add('selected');
            }
        });
    });
@else
    // Character counter for paper abstract
    document.addEventListener('DOMContentLoaded', function() {
        const abstractField = document.getElementById('paper_abstract');
        if (abstractField) {
            abstractField.addEventListener('input', function() {
                const remaining = 2000 - this.value.length;
                const charCount = document.getElementById('char-count');
                if (charCount) {
                    charCount.textContent = remaining;
                }
            });
        }
    });
@endif
</script>
@endsection
