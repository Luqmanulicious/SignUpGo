<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SignUpGo</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            padding: 2rem 0;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
            filter: blur(8px);
            z-index: -1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            color: white;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
        }

        h1 {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: black;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: black;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            padding-right: 45px; 
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ffffff;
        }

        /* Password toggle button with eye icon */
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-10%);
            cursor: pointer;
            background: none;
            border: none;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .toggle-password:hover {
            transform: translateY(-50%) scale(1.1);
        }

        .eye-icon {
            width: 20px;
            height: 20px;
            position: relative;
            display: inline-block;
        }

        /* Eye shape - outer contour */
        .eye-icon svg {
            width: 100%;
            height: 100%;
            stroke: #333;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.3s ease;
        }

        .toggle-password:hover .eye-icon svg,
        .toggle-password.showing .eye-icon svg {
            stroke: #0066cc;
        }

        /* Slash line */
        .eye-slash {
            position: absolute;
            width: 26px;
            height: 2px;
            background: #333;
            transform: rotate(-45deg);
            top: 50%;
            left: 50%;
            margin-left: -13px;
            margin-top: -1px;
            transition: all 0.3s ease;
            opacity: 1;
            transform-origin: center;
        }

        .toggle-password:hover .eye-slash {
            background: #0066cc;
        }

        .toggle-password.showing .eye-slash {
            opacity: 0;
            transform: rotate(-45deg) scale(0);
        }

        /* Checkbox style for password toggle */
        .show-password-container {
            display: flex;
            align-items: center;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .show-password-checkbox {
            position: relative;
            cursor: pointer;
            font-size: 0.9rem;
            color: black;
            display: flex;
            align-items: center;
            user-select: none;
        }

        .show-password-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: relative;
            height: 18px;
            width: 18px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 4px;
            margin-right: 8px;
            transition: all 0.2s ease;
        }

        .show-password-checkbox:hover input~.checkmark {
            background-color: rgba(255, 255, 255, 1);
        }

        .show-password-checkbox input:checked~.checkmark {
            background-color: #4CAF50;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 4px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .show-password-checkbox input:checked~.checkmark:after {
            display: block;
        }

        .btn {
            background-color: #ffffff;
            color: #000;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn:hover {
            background-color: #000;
            color: #fff;
        }

        .links {
            font-size: 0.9rem;
            margin-top: 1rem;
            color: black;
        }

        .links a {
            color: black;
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .optional-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .optional-header {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: black;
        }

        .optional-subtitle {
            font-size: 0.85rem;
            color: black;
            margin-bottom: 1.5rem;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            box-sizing: border-box;
            resize: vertical;
            min-height: 80px;
        }

        .form-group textarea:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ffffff;
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
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background: rgba(255, 255, 255, 1);
        }

        .file-name {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: black;
            text-align: center;
        }

        .required {
            color: red;
            margin-left: 2px;
        }
    </style>
</head>

<body>
    <div class="glass-card">
        <img src="{{ asset('images/Logo.jpg') }}" alt="SignUpGo Logo" class="logo">
        <h1>Create Account</h1>

        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">Full Name<span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email Address<span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password<span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" id="togglePassword" onclick="togglePasswordFields()" aria-label="Toggle password visibility">
                    <span class="eye-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </span>
                    <span class="eye-slash"></span>
                </button>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password<span class="required">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <button type="button" class="toggle-password" id="togglePasswordConfirm" onclick="togglePasswordFields()" aria-label="Toggle password visibility">
                    <span class="eye-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </span>
                    <span class="eye-slash"></span>
                </button>
            </div>

            <div class="show-password-container">
                <label class="show-password-checkbox">
                    <input type="checkbox" id="showPassword" onchange="togglePasswordFields()">
                    <span class="checkmark"></span>
                    Show Password
                </label>
            </div>

            <!-- Optional Profile Information -->
            <div class="optional-section">
                <div class="optional-header">Additional Information (Optional)</div>
                <div class="optional-subtitle">You can skip this and complete your profile later</div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+60123456789">
                </div>

                <div class="form-group">
                    <label for="job_title">Job Title</label>
                    <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}" placeholder="e.g., Software Engineer">
                </div>

                <div class="form-group">
                    <label for="organization">Organization/Institute</label>
                    <input type="text" id="organization" name="organization" value="{{ old('organization') }}" placeholder="e.g., University of Malaya">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" placeholder="Your full address">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" value="{{ old('postcode') }}" placeholder="50000">
                </div>

                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" value="{{ old('website') }}" placeholder="https://yourwebsite.com">
                </div>

                <div class="form-group">
                    <label for="certificate">Certificate (For Jury/Reviewer)</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName('certificate')">
                        <label for="certificate" class="file-input-label">
                            📄 Choose Certificate File
                        </label>
                    </div>
                    <span class="file-name" id="certificate-name"></span>
                </div>

                <div class="form-group">
                    <label for="resume">Resume/CV</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" onchange="updateFileName('resume')">
                        <label for="resume" class="file-input-label">
                            📋 Choose Resume File
                        </label>
                    </div>
                    <span class="file-name" id="resume-name"></span>
                </div>
            </div>

            <button type="submit" class="btn">Register</button>

            <div class="links">
                Already have an account? <a href="{{ route('login') }}">Login here</a>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordFields() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            const showPasswordCheckbox = document.getElementById('showPassword');
            const toggleButtons = document.querySelectorAll('.toggle-password');

            // Get current state
            const isShowing = passwordField.type === 'text';

            // Toggle password fields
            passwordField.type = isShowing ? 'password' : 'text';
            confirmPasswordField.type = isShowing ? 'password' : 'text';

            // Sync checkbox
            showPasswordCheckbox.checked = !isShowing;

            // Toggle button states
            toggleButtons.forEach(button => {
                if (isShowing) {
                    button.classList.remove('showing');
                } else {
                    button.classList.add('showing');
                }
            });

            // Add animation to checkbox
            const checkmark = showPasswordCheckbox.nextElementSibling;
            checkmark.style.transform = 'scale(0.9)';
            setTimeout(() => {
                checkmark.style.transform = 'scale(1)';
            }, 100);
        }

        function updateFileName(inputId) {
            const input = document.getElementById(inputId);
            const fileNameSpan = document.getElementById(inputId + '-name');
            
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                fileNameSpan.textContent = '✓ ' + fileName;
                fileNameSpan.style.color = '#48ff00';
            } else {
                fileNameSpan.textContent = '';
            }
        }
    </script>
</body>

</html>
