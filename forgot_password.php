<?php
session_start();
require 'db_connect.php';

$success = '';
$error = '';

// Handle Password Reset Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_request'])) {
    $username = $conn->real_escape_string($_POST['username']);
    
    // Check if user exists
    $sql = "SELECT id, email, fullname FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate a temporary password (6 characters)
        $temp_password = bin2hex(random_bytes(3));
        
        // Hash and update the password in database
        $hashed_password = password_hash($temp_password, PASSWORD_BCRYPT);
        $update_sql = "UPDATE users SET password='$hashed_password' WHERE id='{$user['id']}'";
        
        if ($conn->query($update_sql) === TRUE) {
            $success = "✓ Temporary password sent! Your new password is: <strong style='color: #ffd700; font-family: monospace; font-size: 1.1rem;'>$temp_password</strong><br><br>Please use this password to login, then change it in your profile.";
        } else {
            $error = "Error processing reset. Please try again.";
        }
    } else {
        $error = "Username not found. Please check and try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Assignment Portal</title>
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
            background: rgba(5, 5, 20, 0.95);
            border-bottom: 2px solid rgba(255, 215, 0, 0.2);
            padding: 15px 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            animation: slideInLeft 0.8s ease;
        }
        
        .logo-assign { color: #ffd700; animation: glow 3s ease-in-out infinite; }
        .logo-flow { color: #ffffff; opacity: 0.95; }
        
        nav ul {
            display: flex;
            gap: 30px;
            list-style: none;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        nav a:hover {
            color: #ffd700;
        }
        
        .main-container {
            max-width: 580px;
            margin: auto;
            padding: 20px;
            position: relative;
            z-index: 5;
            animation: fadeInUp 1s ease;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
        
        .card p {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 20px;
            font-size: 0.95rem;
            animation: fadeInUp 0.6s ease 0.35s both;
        }
        
        .form-group {
            margin-bottom: 10px;
            animation: fadeInUp 0.6s ease;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.4s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }
        
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
        
        .form-group input {
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
        
        .form-group input:focus {
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
            animation: fadeInUp 0.8s ease 0.75s both;
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
            align-items: flex-start;
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
            align-items: flex-start;
            gap: 10px;
        }
        
        .form-footer {
            margin-top: 25px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            animation: fadeInUp 0.8s ease 0.9s both;
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
        <div class="container">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="Home.php" style="color: inherit; text-decoration: none;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
            </div>
            <nav>
                <ul>
                    <li><a href="Home.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <div class="card">
            <h2><i class="fas fa-key"></i> Reset Password</h2>
            <p>Enter your username to reset your password</p>
            
            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <div><?php echo $success; ?></div>
                </div>
                <div style="margin-top: 20px;">
                    <a href="login.php" class="btn btn-primary">Back to Login</a>
                </div>
            <?php else: ?>
                <form action="forgot_password.php" method="POST">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <button type="submit" name="reset_request" class="btn btn-primary">Send Reset Code</button>
                </form>
                
                <div class="form-footer">
                    Remember your password? <a href="login.php">Login here</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
