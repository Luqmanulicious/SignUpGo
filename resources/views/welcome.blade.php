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
      color: white;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    p {
      margin-bottom: 2rem;
      font-size: 1.1rem;
      line-height: 1.5;
      color: white;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }

    a {
      background-color: rgba(0, 255, 255, 0.9);
      color: #000;
      text-decoration: none;
      padding: 0.8rem 2rem;
      border-radius: 25px;
      transition: all 0.3s ease;
      font-weight: 600;
      display: inline-block;
      margin: 0.5rem;
      white-space: nowrap;
      box-shadow: 0 4px 15px rgba(0, 255, 255, 0.3);
    }

    a:hover {
      background-color: rgba(0, 200, 255, 1);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 255, 255, 0.5);
    }

    .button-container {
      display: flex;
      flex-direction: row;
      gap: 1rem;
      margin-top: 2rem;
      justify-content: center;
    }

    .btn-primary {
      background-color: rgba(255, 255, 255, 0.9);
    }

    .btn-secondary {
      background-color: rgba(255, 255, 255, 0.9);
    }

    .btn-secondary:hover {
      background-color: rgb(63, 114, 0);
      color: #ffffff;
    }
  </style>
</head>
<body>
  <div class="glass-card">
    <!-- Logo Section -->
    <img src="{{ asset('images/Logo.jpg') }}" alt="SignUpGo Logo" class="logo">

    <h1>SignUpGo</h1>
    <p>Effortless registration and smart evaluation — designed for conferences and innovation events.</p>
    
    <div class="button-container">
      <a href="{{ url('/login') }}" class="btn-primary">Login Account</a>
      <a href="{{ url('/register') }}" class="btn-secondary">Create New Account</a>
    </div>
  </div>
</body>
</html>
