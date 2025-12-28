@extends('layouts.app')

@section('title', 'My Account | SignUpGo')

@section('styles')
<style>
    .container {
        max-width: 900px;
        width: 100%;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        margin: 0;
        color: #2c3e50;
    }

    .btn-edit {
        display: inline-block;
        padding: 0.7rem 1.5rem;
        background: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: none;
    }

    .alert-success {
        background: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .alert-danger ul {
        margin-bottom: 0;
    }

    .alert-danger li {
        margin-top: 0.25rem;
    }

    .profile-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .profile-avatar {
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
        margin-right: 2rem;
        overflow: hidden;
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .profile-info h2 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
    }

    .profile-info p {
        margin: 0;
        color: #7f8c8d;
        font-size: 1rem;
    }

    .info-section {
        margin-bottom: 2rem;
    }

    .info-section h3 {
        margin: 0 0 1rem 0;
        color: #34495e;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
    }

    .info-section h3::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #3498db;
        margin-right: 0.75rem;
        border-radius: 2px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-weight: 600;
        color: #7f8c8d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        color: #2c3e50;
        font-size: 1rem;
        word-break: break-word;
    }

    .info-value.empty {
        color: #bdc3c7;
        font-style: italic;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 0.5rem;
    }

    .file-info {
        display: flex;
        align-items: center;
    }

    .file-icon {
        font-size: 1.5rem;
        margin-right: 0.75rem;
    }

    .file-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-download, .btn-delete {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-download {
        background: #27ae60;
        color: white;
    }

    .btn-download:hover {
        background: #229954;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    .completion-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 1rem;
    }

    .completion-badge.complete {
        background: #d4edda;
        color: #155724;
    }

    .completion-badge.high {
        background: #c3f5d4;
        color: #0e7a2e;
    }

    .completion-badge.medium {
        background: #fff3cd;
        color: #856404;
    }

    .completion-badge.low {
        background: #f8d7da;
        color: #721c24;
    }

    /* Custom Confirmation Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        animation: fadeIn 0.2s ease;
    }

    .modal-overlay.active {
        display: flex;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(20px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }

    .confirmation-modal {
        background: #396693;
        border-radius: 16px;
        padding: 2rem;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        animation: slideUp 0.3s ease;
    }

    .modal-header {
        color: #ecf0f1;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .modal-message {
        color: #d5dbdb;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .modal-buttons {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .modal-btn {
        padding: 0.7rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-confirm {
        background: #a8e6a1;
        color: #155724;
        border: 2px solid transparent;
    }

    .btn-confirm:hover {
        background: #8fd687;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(168, 230, 161, 0.4);
    }

    .btn-cancel {
        background: transparent;
        color: #ecf0f1;
        border: 2px solid #7f8c8d;
    }

    .btn-cancel:hover {
        background: #455a64;
        border-color: #95a5a6;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <h1>My Account</h1>
        <a href="{{ route('account.edit') }}" class="btn-edit">Edit Profile</a>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay">
        <div class="confirmation-modal">
            <div class="modal-header">Deletion Confirmation</div>
            <div class="modal-message">Are you sure you want to delete this certificate?</div>
            <div class="modal-buttons">
                <button type="button" class="modal-btn btn-confirm" onclick="confirmDelete()">Yes, Confirm</button>
                <button type="button" class="modal-btn btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                @if($user->profile_picture)
                    <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}">
                @else
                    <span class="profile-avatar-text">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
                @php
                    $completedFields = 0;
                    $totalFields = 9; // profile_picture, phone, job_title, organization, address, postcode, website, certificate, resume
                    if($user->profile_picture) $completedFields++;
                    if($user->phone) $completedFields++;
                    if($user->job_title) $completedFields++;
                    if($user->organization) $completedFields++;
                    if($user->address) $completedFields++;
                    if($user->postcode) $completedFields++;
                    if($user->website) $completedFields++;
                    if($user->certificate_path) $completedFields++;
                    if($user->resume_path) $completedFields++;
                    $percentage = round(($completedFields / $totalFields) * 100);
                    
                    // Determine badge color based on percentage
                    if ($percentage == 100) {
                        $badgeClass = 'complete';
                    } elseif ($percentage >= 70) {
                        $badgeClass = 'high';
                    } elseif ($percentage >= 26) {
                        $badgeClass = 'medium';
                    } else {
                        $badgeClass = 'low';
                    }
                @endphp
                <span class="completion-badge {{ $badgeClass }}">
                    Profile {{ $percentage }}% Complete
                </span>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="info-section">
            <h3>Contact Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value {{ $user->phone ? '' : 'empty' }}">
                        {{ $user->phone ?: 'Not provided' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="info-section">
            <h3>Professional Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Job Title</span>
                    <span class="info-value {{ $user->job_title ? '' : 'empty' }}">
                        {{ $user->job_title ?: 'Not provided' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Organization/Institute</span>
                    <span class="info-value {{ $user->organization ? '' : 'empty' }}">
                        {{ $user->organization ?: 'Not provided' }}
                    </span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Website</span>
                    <span class="info-value {{ $user->website ? '' : 'empty' }}">
                        @if($user->website)
                            <a href="{{ $user->website }}" target="_blank" style="color: #3498db; text-decoration: underline;">
                                {{ $user->website }}
                            </a>
                        @else
                            Not provided
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="info-section">
            <h3>Address Information</h3>
            <div class="info-grid">
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Address</span>
                    <span class="info-value {{ $user->address ? '' : 'empty' }}">
                        {{ $user->address ?: 'Not provided' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Postcode</span>
                    <span class="info-value {{ $user->postcode ? '' : 'empty' }}">
                        {{ $user->postcode ?: 'Not provided' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="info-section">
            <h3>Documents</h3>
            
            <div class="info-item">
                <span class="info-label">Certificate (For Jury/Reviewer Applications)</span>
                @if($user->certificate_path)
                    <div class="file-item">
                        <div class="file-info">
                            <span class="file-icon">📄</span>
                            <span>Certificate Uploaded</span>
                        </div>
                        <div class="file-actions">
                            <a href="{{ route('account.download-certificate') }}" target="_blank" class="btn-download">
                                View Certificate
                            </a>
                            <form id="deleteCertificateForm" action="{{ route('account.delete-file') }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="type" value="certificate">
                                <button type="button" class="btn-delete" onclick="showDeleteModal('certificate', 'deleteCertificateForm')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <span class="info-value empty">Not uploaded</span>
                @endif
            </div>

            <div class="info-item" style="margin-top: 1.5rem;">
                <span class="info-label">Resume/CV</span>
                @if($user->resume_path)
                    <div class="file-item">
                        <div class="file-info">
                            <span class="file-icon">📋</span>
                            <span>Resume/CV Uploaded</span>
                        </div>
                        <div class="file-actions">
                            <a href="{{ route('account.download-resume') }}" target="_blank" class="btn-download">
                                View Resume
                            </a>
                            <form id="deleteResumeForm" action="{{ route('account.delete-file') }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="type" value="resume">
                                <button type="button" class="btn-delete" onclick="showDeleteModal('resume', 'deleteResumeForm')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <span class="info-value empty">Not uploaded</span>
                @endif
            </div>
        </div>

        @if($percentage < 100)
            <div style="margin-top: 2rem; padding: 1.5rem; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                <strong style="color: #856404;">⚠️ Complete Your Profile</strong>
                <p style="margin: 0.5rem 0 0 0; color: #856404;">
                    Some profile information is missing. Complete your profile to apply for Jury or Reviewer roles in events.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
    let currentFormId = null;
    let currentFileType = null;

    function showDeleteModal(fileType, formId) {
        currentFormId = formId;
        currentFileType = fileType;
        
        const modal = document.getElementById('confirmModal');
        const message = modal.querySelector('.modal-message');
        
        const fileTypeName = fileType === 'certificate' ? 'certificate' : 'resume';
        message.textContent = `Are you sure you want to delete this ${fileTypeName}?`;
        
        modal.classList.add('active');
    }

    function closeModal() {
        const modal = document.getElementById('confirmModal');
        modal.classList.remove('active');
        currentFormId = null;
        currentFileType = null;
    }

    function confirmDelete() {
        if (currentFormId) {
            console.log('Deleting file from form:', currentFormId);
            
            // Get the form element
            const form = document.getElementById(currentFormId);
            
            if (!form) {
                console.error('Form not found:', currentFormId);
                alert('Error: Form not found. Please refresh the page and try again.');
                return;
            }
            
            // Close modal
            closeModal();
            
            // Submit the form (toast notification will be shown by component after redirect)
            console.log('Submitting form...');
            form.submit();
        } else {
            console.error('No form ID set');
        }
    }

    // Close modal when clicking outside
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endsection
