<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - AssignFlow</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            font-family: 'Segoe UI', sans-serif;
        }
        
        .logout-container {
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logout-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: slideDown 0.6s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logout-text {
            color: white;
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .logout-subtext {
            color: #bdbdbd;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 215, 0, 0.2);
            border-top: 4px solid #ffd700;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-top: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-out {
            animation: fadeOut 0.6s ease-out forwards;
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container" id="logout-content">
        <div class="logout-icon">👋</div>
        <div class="logout-text">See you soon!</div>
        <div class="logout-subtext">You have been logged out successfully</div>
        <div class="spinner"></div>
    </div>
    
    <script>
        // Show fade-out animation after 1.5 seconds, then redirect
        setTimeout(function() {
            const element = document.getElementById('logout-content');
            element.classList.add('fade-out');
            
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 600);
        }, 1500);
    </script>
</body>
</html>
