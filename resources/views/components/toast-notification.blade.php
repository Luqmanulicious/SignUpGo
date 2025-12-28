<!-- Toast Notification Component -->
@if(session('success') || session('error') || session('info') || session('warning') || $errors->any())
<div class="toast-notification 
    {{ session('success') ? 'success' : '' }}
    {{ session('error') || $errors->any() ? 'error' : '' }}
    {{ session('info') ? 'info' : '' }}
    {{ session('warning') ? 'warning' : '' }}" 
    id="toastNotification">
    <div class="toast-header">
        @if(session('success'))
            <span class="toast-icon">✓</span>
            <div class="toast-content">
                <h4 class="toast-title">Success!</h4>
                <p class="toast-message">{{ session('success') }}</p>
            </div>
        @elseif(session('error'))
            <span class="toast-icon">✗</span>
            <div class="toast-content">
                <h4 class="toast-title">Error!</h4>
                <p class="toast-message">{{ session('error') }}</p>
            </div>
        @elseif(session('info'))
            <span class="toast-icon">ℹ</span>
            <div class="toast-content">
                <h4 class="toast-title">Info</h4>
                <p class="toast-message">{{ session('info') }}</p>
            </div>
        @elseif(session('warning'))
            <span class="toast-icon">⚠</span>
            <div class="toast-content">
                <h4 class="toast-title">Warning</h4>
                <p class="toast-message">{{ session('warning') }}</p>
            </div>
        @elseif($errors->any())
            <span class="toast-icon">✗</span>
            <div class="toast-content">
                <h4 class="toast-title">Please fix the following errors:</h4>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <button class="toast-close" onclick="closeToast()">&times;</button>
    </div>
    <div class="toast-progress">
        <div class="toast-progress-bar"></div>
    </div>
</div>

<style>
    /* Toast Notification Styles */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: -400px;
        min-width: 350px;
        max-width: 450px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        overflow: hidden;
    }

    .toast-notification.show {
        right: 20px;
    }

    .toast-notification.success {
        border-left: 5px solid #28a745;
    }

    .toast-notification.error {
        border-left: 5px solid #dc3545;
    }

    .toast-notification.info {
        border-left: 5px solid #17a2b8;
    }

    .toast-notification.warning {
        border-left: 5px solid #ffc107;
    }

    .toast-header {
        display: flex;
        align-items: flex-start;
        padding: 1rem 1.25rem;
        gap: 0.75rem;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    }

    .toast-icon {
        font-size: 1.75rem;
        line-height: 1;
        flex-shrink: 0;
    }

    .toast-notification.success .toast-icon {
        color: #28a745;
    }

    .toast-notification.error .toast-icon {
        color: #dc3545;
    }

    .toast-notification.info .toast-icon {
        color: #17a2b8;
    }

    .toast-notification.warning .toast-icon {
        color: #ffc107;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 1rem;
        margin: 0 0 0.25rem 0;
        color: #2c3e50;
    }

    .toast-message {
        margin: 0;
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.4;
    }

    .toast-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6c757d;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .toast-close:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #2c3e50;
    }

    .toast-progress {
        height: 4px;
        background: rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .toast-progress-bar {
        height: 100%;
        width: 100%;
        animation: progress 5s linear;
    }

    .toast-notification.success .toast-progress-bar {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    }

    .toast-notification.error .toast-progress-bar {
        background: linear-gradient(90deg, #dc3545 0%, #e83e8c 100%);
    }

    .toast-notification.info .toast-progress-bar {
        background: linear-gradient(90deg, #17a2b8 0%, #5bc0de 100%);
    }

    .toast-notification.warning .toast-progress-bar {
        background: linear-gradient(90deg, #ffc107 0%, #ffca28 100%);
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }

    .toast-notification ul {
        margin: 0.5rem 0 0 0;
        padding: 0;
        list-style: none;
    }

    .toast-notification li {
        margin-top: 0.35rem;
        color: #6c757d;
        font-size: 0.85rem;
        position: relative;
        padding-left: 1rem;
    }

    .toast-notification li::before {
        content: '•';
        position: absolute;
        left: 0;
        color: #dc3545;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .toast-notification {
            min-width: 300px;
            max-width: calc(100vw - 40px);
            right: -100%;
        }

        .toast-notification.show {
            right: 10px;
        }
    }
</style>

<script>
    // Toast Notification Functions
    function showToast() {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            // Show toast with slide animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(() => {
                closeToast();
            }, 5000);
        }
    }

    function closeToast() {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            toast.classList.remove('show');
            
            // Remove from DOM after animation
            setTimeout(() => {
                toast.remove();
            }, 400);
        }
    }

    // Show toast on page load if exists
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showToast);
    } else {
        showToast();
    }
</script>
@endif
