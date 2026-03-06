<?php
require 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Assignment Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #070416 0%, #0f0620 50%, #1a0a2e 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* Animated background elements */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            left: 5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 25s ease-in-out infinite reverse;
            z-index: 0;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }
        
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes glow {
            0%, 100% { text-shadow: 0 0 10px rgba(255, 215, 0, 0.3); }
            50% { text-shadow: 0 0 20px rgba(255, 215, 0, 0.6); }
        }
        
        header {
            position: relative;
            z-index: 10;
            animation: fadeInDown 0.8s ease;
            padding: 15px 0;
            flex-shrink: 0;
        }
        
        .container > div:not(.card-container) {
            position: relative;
            z-index: 5;
        }
        
        .logo { display: flex; align-items: center; gap: 8px; font-weight: 700; animation: slideInLeft 0.8s ease; }
        .logo-assign { color: #ffd700; animation: glow 3s ease-in-out infinite; }
        .logo-flow { color: #ffffff; opacity: 0.95; }
        
        .btn-nav { border-radius: 12px; transition: all 0.3s ease; }
        .btn-nav:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3); }
        
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            padding: 20px;
            position: relative;
            z-index: 5;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 215, 0, 0.2);
            border-radius: 20px;
            padding: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37), inset 0 0 20px rgba(255, 255, 255, 0.0);
            animation: fadeInUp 0.8s ease 0.2s both;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 30s ease-in-out infinite;
        }
        
        .card:hover {
            border-color: rgba(255, 215, 0, 0.4);
            box-shadow: 0 8px 32px rgba(255, 215, 0, 0.2), inset 0 0 20px rgba(255, 215, 0, 0.05);
            transform: translateY(-5px);
        }
        
        .card h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 2rem;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInDown 0.8s ease 0.3s both;
        }
        
        .form-group {
            margin-bottom: 10px;
            animation: fadeInUp 0.6s ease;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.4s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }
        .form-group:nth-child(3) { animation-delay: 0.6s; }
        .form-group:nth-child(4) { animation-delay: 0.7s; }
        .form-group:nth-child(5) { animation-delay: 0.8s; }
        .form-group:nth-child(6) { animation-delay: 0.9s; }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 9px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 215, 0, 0.6);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3), inset 0 0 10px rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }
        
        .btn {
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ffd700, #ff9f00);
            color: #111;
            font-size: 1rem;
            box-shadow: 0 8px 30px rgba(255, 215, 0, 0.3);
            animation: fadeInUp 0.8s ease 1s both;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(255, 215, 0, 0.5);
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .error-message {
            background: rgba(220, 53, 69, 0.2);
            border: 2px solid rgba(220, 53, 69, 0.5);
            color: #ff6b6b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            animation: slideInLeft 0.5s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success-message {
            background: rgba(40, 167, 69, 0.2);
            border: 2px solid rgba(40, 167, 69, 0.5);
            color: #51cf66;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            animation: slideInLeft 0.5s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-footer {
            margin-top: 25px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            animation: fadeInUp 0.8s ease 1s both;
        }
        
        .form-footer a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .form-footer a:hover {
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        }
    </style>
</head>
<body>
    <header>
        <div class="container" style="display:flex; justify-content:space-between; align-items:center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <span class="logo-assign">Assign</span><span class="logo-flow">Flow</span>
            </div>
            <nav>
                <ul>
                    <li><a href="Home.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="max-width: 580px; margin-top: 50px;">
        <div class="card">
            <h2>Create Account</h2>
            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?> <a href="login.php" style="color: #51cf66; text-decoration: underline; font-weight: 600;">Login now</a>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" max="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Select your role</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                    </select>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </form>
            <div class="form-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
