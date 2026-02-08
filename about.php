<?php
// Simple About page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - AssignFlow</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .about-hero{ display:grid; grid-template-columns:1fr 420px; gap:30px; align-items:center; padding:60px 20px; }
        .about-img{ width:100%; height:320px; background:rgba(255,255,255,0.03); border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--muted-text); }
        @media(max-width:900px){ .about-hero{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="container" style="display:flex; justify-content:space-between; align-items:center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:36px; vertical-align:middle;">
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

    <div class="container">
        <section class="about-hero">
            <div>
                <h1 style="font-size:2.4rem; margin-bottom:10px;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span> — Smart Assignment Portal</h1>
                <p style="color:var(--muted-text); font-size:1.05rem;">
                    AssignFlow is a modern assignment submission and review platform that supports documents, audio, and video submissions. It provides faculty tools for grading and feedback while giving students a simple, secure way to submit work.
                </p>

                <h3 style="margin-top:20px;">About the Developers</h3>
                <p style="color:var(--muted-text);">This project was developed by a small team of full-stack developers focused on improving the education workflow. The developer section below is left open for adding developer images and bios.</p>
            </div>

            <div>
                <div class="about-img">
                    <!-- Image slot: replace with r-radius:12px;"> -->
                     <img src="images/team.webp" alt="Developer" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                    
                </div>
            </div>
        </section>

        <h2 style="margin-top:30px;">Features</h2>
        <p style="color:var(--muted-text);">Multi-format submissions, secure storage, and AI-assisted review tools make AssignFlow a comprehensive solution for modern classrooms.</p>
    </div>

    <footer>
        <div class="container" style="display:flex; flex-direction:column; align-items:center; gap:12px;">
         
            <h3><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></h3>
            <div style="color:var(--muted-text); display:flex; gap:20px; align-items:center; flex-wrap:wrap; justify-content:center;">
                <a id="open-feedback" style="color:var(--muted-text); cursor:pointer;">Feedback</a>
                <span>Phone: +91-9876-543-210</span>
                <span>Email: support@assignflow.com</span>
            </div>
            <p style="color:var(--muted-text); margin-top:6px;">© 2026 AssignFlow. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>
