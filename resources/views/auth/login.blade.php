<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SignUpGo</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                url('{{ asset('images/background3.png') }}') no-repeat center center/cover;
            z-index: -1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            color: white;
            box-shadow: none;
            width: 90%;
            max-width: 420px;
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
            color: white;
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
            color: white;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            padding-right: 45px;
            /* Make room for eye icon */
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
            background-color: #1261ff;
            color: #fff;
        }

        .links {
            font-size: 0.9rem;
            margin-top: 1rem;
            color: white;
        }

        .links a {
            color: rgb(13, 255, 0);
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

        .required {
            color: red;
            margin-left: 2px;
        }
    </style>
</head>

<body>
    <div class="glass-card">
        <img src="{{ asset('images/Logo.jpg') }}" alt="SignUpGo Logo" class="logo">
        <h1>Login to SignUpGo</h1>

        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address<span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password<span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" onclick="togglePassword()"
                    aria-label="Toggle password visibility">
                    <span class="eye-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </span>
                    <span class="eye-slash"></span>
                </button>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="links">
                Don't have an account? <a href="{{ route('register') }}">Register here</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const toggleButton = document.querySelector(".toggle-password");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleButton.classList.add("showing");
            } else {
                passwordField.type = "password";
                toggleButton.classList.remove("showing");
            }

            // Add ripple effect
            toggleButton.style.transform = "scale(0.95)";
            setTimeout(() => {
                toggleButton.style.transform = "scale(1)";
            }, 100);
        }
    </script>
</body>

</html>
