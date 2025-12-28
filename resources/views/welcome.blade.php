<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SignUpGo | Smart Registration System</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: url('https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
      font-family: 'Poppins', sans-serif;
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
      max-width: 420px;
    }

    .logo {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 1rem;
      box-shadow: 0 0 15px rgba(255,255,255,0.4);
    }

    h1 {
      margin-bottom: 1rem;
      font-size: 2rem;
      color: black;
    }

    p {
      margin-bottom: 2rem;
      font-size: 1.1rem;
      line-height: 1.5;
      color: black;
    }

    a {
      background-color: #ffffff;
      color: #000;
      text-decoration: none;
      padding: 0.8rem 2rem;
      border-radius: 25px;
      transition: all 0.3s ease;
      font-weight: 600;
      display: inline-block;
      margin: 0.5rem;
      white-space: nowrap;
    }

    a:hover {
      background-color: #000;
      color: #fff;
    }

    .button-container {
      display: flex;
      flex-direction: row;
      gap: 1rem;
      margin-top: 2rem;
      justify-content: center;
    }

    .btn-primary {
      background-color: #ffffff;
    }

    .btn-secondary {
      background-color: #ffffff;
    }

    .btn-secondary:hover {
      background-color: #000000;
      color: #ffffff;
    }
  </style>
</head>
<body>
  <div class="glass-card">
    <!-- Logo Section -->
    <img src="{{ asset('images/Logo.jpg') }}" alt="SignUpGo Logo" class="logo">

    <h1>SignUpGo</h1>
    <p>Effortless event registration and management — designed for conferences that matter.</p>
    
    <div class="button-container">
      <a href="{{ url('/login') }}" class="btn-primary">Login Account</a>
      <a href="{{ url('/register') }}" class="btn-secondary">Create New Account</a>
    </div>
  </div>
</body>
</html>
