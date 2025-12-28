@extends('layouts.app')

@section('title', 'Edit Profile | SignUpGo')

@section('styles')
<style>
    .container {
        max-width: 700px;
        width: 100%;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
    }

    .page-header p {
        margin: 0;
        color: #7f8c8d;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .form-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #ecf0f1;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section h3 {
        margin: 0 0 1.5rem 0;
        color: #34495e;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .form-section h3::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #3498db;
        margin-right: 0.75rem;
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .form-group label .optional {
        color: #95a5a6;
        font-weight: normal;
        font-size: 0.85rem;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: -9999px;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        background: #f8f9fa;
        color: #495057;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-input-label:hover {
        background: #e9ecef;
        border-color: #3498db;
    }

    .current-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: #d4edda;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .current-file-info {
        display: flex;
        align-items: center;
        color: #155724;
    }

    .current-file-icon {
        font-size: 1.2rem;
        margin-right: 0.5rem;
    }

    .file-name {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .file-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    .profile-picture-preview {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .preview-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        overflow: hidden;
        position: relative;
    }

    .preview-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-info {
        flex: 1;
    }

    .upload-info h4 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-size: 1rem;
    }

    .upload-info p {
        margin: 0;
        color: #7f8c8d;
        font-size: 0.85rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: #3498db;
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Edit Profile</h1>
        <p>Update your personal and professional information</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="form-section">
                <h3>Basic Information</h3>
                
                <div class="form-group">
                    <label>Profile Picture <span class="optional">(Optional)</span></label>
                    <div class="profile-picture-preview">
                        <div class="preview-avatar" id="previewAvatar">
                            @if($user->profile_picture)
                                <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" id="avatarImage">
                            @else
                                <span id="avatarText">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="upload-info">
                            <h4>Upload your photo</h4>
                            <p>JPG, PNG or GIF. Max size 5MB</p>
                            <div class="file-input-wrapper" style="margin-top: 0.75rem;">
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)">
                                <label for="profile_picture" class="file-input-label">
                                    📷 Choose Photo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="optional">(Optional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+60123456789">
                </div>
            </div>

            <!-- Professional Information -->
            <div class="form-section">
                <h3>Professional Information</h3>
                
                <div class="form-group">
                    <label for="job_title">Job Title <span class="optional">(Optional)</span></label>
                    <input type="text" id="job_title" name="job_title" value="{{ old('job_title', $user->job_title) }}" placeholder="e.g., Software Engineer">
                </div>

                <div class="form-group">
                    <label for="organization">Organization/Institute <span class="optional">(Optional)</span></label>
                    <input type="text" id="organization" name="organization" value="{{ old('organization', $user->organization) }}" placeholder="e.g., University of Malaya">
                </div>

                <div class="form-group">
                    <label for="website">Website <span class="optional">(Optional)</span></label>
                    <input type="url" id="website" name="website" value="{{ old('website', $user->website) }}" placeholder="https://yourwebsite.com">
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section">
                <h3>Address Information</h3>
                
                <div class="form-group">
                    <label for="address">Address <span class="optional">(Optional)</span></label>
                    <textarea id="address" name="address" placeholder="Your full address">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="postcode">Postcode <span class="optional">(Optional)</span></label>
                    <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $user->postcode) }}" placeholder="50000">
                </div>
            </div>

            <!-- Documents -->
            <div class="form-section">
                <h3>Documents</h3>
                
                <div class="form-group">
                    <label for="certificate">Certificate <span class="optional">(For Jury/Reviewer Applications)</span></label>
                    @if($user->certificate_path)
                        <div class="current-file">
                            <div class="current-file-info">
                                <span class="current-file-icon">📄</span>
                                <span>Current: {{ basename($user->certificate_path) }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $user->certificate_path) }}" target="_blank" style="color: #155724; font-size: 0.9rem;">View</a>
                        </div>
                    @endif
                    <div class="file-input-wrapper">
                        <input type="file" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName('certificate')">
                        <label for="certificate" class="file-input-label">
                            📄 {{ $user->certificate_path ? 'Replace Certificate' : 'Upload Certificate' }}
                        </label>
                    </div>
                    <span class="file-name" id="certificate-name"></span>
                    <p class="file-hint">Accepted formats: PDF, JPG, JPEG, PNG (Max: 5MB)</p>
                </div>

                <div class="form-group">
                    <label for="resume">Resume/CV <span class="optional">(Optional)</span></label>
                    @if($user->resume_path)
                        <div class="current-file">
                            <div class="current-file-info">
                                <span class="current-file-icon">📋</span>
                                <span>Current: {{ basename($user->resume_path) }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $user->resume_path) }}" target="_blank" style="color: #155724; font-size: 0.9rem;">View</a>
                        </div>
                    @endif
                    <div class="file-input-wrapper">
                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" onchange="updateFileName('resume')">
                        <label for="resume" class="file-input-label">
                            📋 {{ $user->resume_path ? 'Replace Resume' : 'Upload Resume' }}
                        </label>
                    </div>
                    <span class="file-name" id="resume-name"></span>
                    <p class="file-hint">Accepted formats: PDF, DOC, DOCX (Max: 5MB)</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('account.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFileName(inputId) {
        const input = document.getElementById(inputId);
        const fileNameSpan = document.getElementById(inputId + '-name');
        
        if (input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            fileNameSpan.textContent = '✓ Selected: ' + fileName;
            fileNameSpan.style.color = '#28a745';
        } else {
            fileNameSpan.textContent = '';
        }
    }

    function previewImage(event) {
        const file = event.target.files[0];
        const previewAvatar = document.getElementById('previewAvatar');
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Remove existing content
                previewAvatar.innerHTML = '';
                
                // Create and add image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'avatarImage';
                previewAvatar.appendChild(img);
            };
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
