<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AssignFlow - Future of Education</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root{
            --accent-color:#ffd700;
            --glass-bg:rgba(255,255,255,0.06);
            --glass-border:rgba(255,255,255,0.15);
            --muted-text:#bdbdbd;
        }

        body{
            margin:0;
            font-family: 'Segoe UI', sans-serif;
            background:#0b0614;
            color:white;
        }

        header{
            position:sticky;
            top:0;
            background:rgba(0,0,0,0.6);
            backdrop-filter:blur(12px);
            border-bottom:1px solid var(--glass-border);
            z-index:999;
        }

        .container{
            max-width:1200px;
            margin:auto;
            padding:20px;
        }

        .logo{
            font-size:1.8rem;
            font-weight:bold;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .logo-assign{ color:var(--accent-color); }
        .logo-flow{ color:#ffffff; opacity:0.95; }

        nav ul{
            list-style:none;
            display:flex;
            gap:20px;
            margin:0;
            padding:0;
        }

        nav a{
            color:white;
            text-decoration:none;
        }

        .btn{
            padding:10px 20px;
            border-radius:25px;
            text-decoration:none;
            color:white;
            border:1px solid var(--glass-border);
        }

        .btn-primary{
            background:linear-gradient(135deg,#ffd700,#ff9f00);
            color:black;
            font-weight:600;
        }

        /* HERO */
        .hero-section{
            display:grid;
            grid-template-columns:1fr 1fr;
            align-items:center;
            gap:40px;
            padding:120px 20px;
        }

        .hero-title{
            font-size:3.5rem;
            background:linear-gradient(to right,#fff,var(--accent-color));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
        }

        .hero-img img{
            width:100%;
            max-height:420px;
            object-fit:cover;
            border-radius:20px;
            animation: float 4s ease-in-out infinite;
            box-shadow:0 20px 50px rgba(0,0,0,0.6);
        }

        @keyframes float{
            0%,100%{ transform:translateY(0); }
            50%{ transform:translateY(-20px); }
        }

        /* FEATURES */
        .features-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:30px;
            margin:80px 0;
        }

        .feature-card{
            background:var(--glass-bg);
            backdrop-filter:blur(10px);
            border:1px solid var(--glass-border);
            padding:30px;
            border-radius:20px;
            text-align:center;
            transition:0.4s;
        }

        .feature-card:hover{
            transform:translateY(-10px);
            border-color:var(--accent-color);
            box-shadow:0 20px 40px rgba(255,215,0,0.3);
        }

        .feature-card img{
            width:100%;
            height:200px;
            object-fit:cover;
            border-radius:12px;
            margin-bottom:18px;
        }

        footer{
            background:rgba(0,0,0,0.7);
            border-top:1px solid var(--glass-border);
            padding:50px 20px;
            text-align:center;
        }

        footer img{
            width:60px;
            margin-bottom:15px;
        }

        @media(max-width:900px){
            .hero-section{
                grid-template-columns:1fr;
                text-align:center;
            }
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

<div class="container">

    <!-- HERO -->
    <section class="hero-section">
        <div>
            <h1 class="hero-title">Smart Assignment Submission & Review</h1>
            <p style="color:var(--muted-text); font-size:1.2rem;">
                Submit assignments in PDF, audio, or video format and receive intelligent AI-powered reviews instantly.
            </p>
            <br>
            <a href="register.php" class="btn btn-primary">Get Started</a>
            <a href="login.php" class="btn" style="margin-left:15px;">Faculty Portal</a>
        </div>

        <div class="hero-img">
            <img src="images/hero.avif" alt="Online Assignment System">
        </div>
    </section>

    <!-- FEATURES -->
    <h2 style="text-align:center; font-size:2.6rem;">Why Choose AssignFlow?</h2>

    <div class="features-grid">

        <div class="feature-card">
            <img src="images/upload2.webp" alt="Multi Format">
            <h3>Multi-Format Submissions</h3>
            <p>Upload documents, audio, or video assignments in one secure place.</p>
        </div>

        <div class="feature-card">
            <img src="images/security.jpg" alt="Secure">
            <h3>Secure & Private</h3>
            <p>Enterprise-grade security ensures your data remains protected.</p>
        </div>

        <div class="feature-card">
            <img src="images/ai.jpg" alt="AI Review">
            <h3>AI-Powered Review</h3>
            <p>Automatic grading and intelligent feedback using AI.</p>
        </div>

    </div>
</div>

<footer>
    <div class="container" style="display:flex; flex-direction:column; align-items:center; gap:12px;">
        <img src="images/logo.jpeg" alt="AssignFlow Logo">
        <h3><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></h3>
        <div style="color:var(--muted-text); display:flex; gap:20px; align-items:center; flex-wrap:wrap; justify-content:center;">
            <a id="open-feedback" style="color:var(--muted-text); cursor:pointer;">Feedback</a>
            <span>Phone: 1800 2100 4255</span>
            <span>Email: support@assignflow.com</span>
        </div>
        <p style="color:var(--muted-text); margin-top:6px;">
            © 2026 AssignFlow. All Rights Reserved.
        </p>
    </div>
</footer>

<!-- Feedback Modal -->
<div id="feedback-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:9999;">
    <div style="background:linear-gradient(180deg, rgba(12,6,20,0.98), rgba(25,18,40,0.98)); width:92%; max-width:760px; margin:auto; border-radius:12px; padding:18px; position:relative; border:1px solid rgba(255,255,255,0.06); color:#fff;">
        <button id="close-feedback" style="position:absolute; right:12px; top:12px; background:transparent; border:none; color:#fff; font-size:1.4rem; cursor:pointer;">&times;</button>
        <h2 style="color:var(--accent-color); margin-top:6px;">Feedback & Contact</h2>
        <p style="color:var(--muted-text);">Use the form below to submit a complaint, suggestion, or general feedback. Submitted items are listed below.</p>

        <div style="display:grid; grid-template-columns:1fr 320px; gap:18px; margin-top:10px;">
            <form id="feedback-form" style="display:flex; flex-direction:column; gap:10px;">
                <input type="text" name="name" placeholder="Your Name" required style="padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.02); color:#fff;" />
                <input type="email" name="email" placeholder="Your Email" required style="padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.02); color:#fff;" />
                <select name="type" required style="padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.02); color:#fff;">
                    <option value="" disabled selected>Select Type</option>
                    <option value="complaint">Complaint</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="feedback">General Feedback</option>
                </select>
                <textarea name="message" rows="5" placeholder="Your message" required style="padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.02); color:#fff;"></textarea>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" id="cancel-feedback" class="btn" style="background:transparent; border:1px solid rgba(255,255,255,0.06); color:#fff; padding:8px 14px; border-radius:6px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding:8px 14px; border-radius:6px;">Submit</button>
                </div>
                <div id="feedback-msg" style="color:var(--accent-color); display:none; margin-top:6px;"></div>
            </form>

            <div style="background:rgba(255,255,255,0.02); border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.04);">
                <h4 style="margin-top:0; color:#fff;">Contact Info</h4>
                <p style="color:var(--muted-text); margin:6px 0;">Phone: +91-9876-543-210</p>
                <p style="color:var(--muted-text); margin:6px 0;">Email: support@assignflow.com</p>
                <hr style="border:none; border-top:1px solid rgba(255,255,255,0.04); margin:10px 0;">
                <h4 style="margin:6px 0 8px 0; color:#fff;">Submitted Items</h4>
                <div id="submitted-feedbacks" style="max-height:320px; overflow:auto; color:var(--muted-text);"></div>
            </div>
        </div>
    </div>
</div>

        <script>
        // Feedback modal and storage (client-side)
        const openBtn = document.getElementById('open-feedback');
        const modal = document.getElementById('feedback-modal');
        const closeBtn = document.getElementById('close-feedback');
        const cancelBtn = document.getElementById('cancel-feedback');
        const form = document.getElementById('feedback-form');
        const msg = document.getElementById('feedback-msg');
        const list = document.getElementById('submitted-feedbacks');

        function getFeedbacks(){
            try{ return JSON.parse(localStorage.getItem('assignflow_feedbacks') || '[]'); }catch(e){ return []; }
        }

        function saveFeedback(obj){
            const arr = getFeedbacks();
            arr.unshift(obj);
            localStorage.setItem('assignflow_feedbacks', JSON.stringify(arr));
        }

        function renderFeedbacks(){
            const arr = getFeedbacks();
            if(!list) return;
            if(arr.length===0){ list.innerHTML = '<p style="color:var(--muted-text);">No items yet.</p>'; return; }
            list.innerHTML = arr.map(f=>{
                const time = new Date(f.ts).toLocaleString();
                return `<div style="padding:8px; border-bottom:1px solid rgba(255,255,255,0.03); margin-bottom:6px;">
                    <div style="font-weight:600; color:#fff;">${escapeHtml(f.type)} — ${escapeHtml(f.name || 'Anonymous')}</div>
                    <div style="color:var(--muted-text); font-size:0.9rem; margin:6px 0;">${escapeHtml(f.message)}</div>
                    <div style="color:rgba(255,255,255,0.45); font-size:0.8rem;">${time} • ${escapeHtml(f.email || '')}</div>
                </div>`;
            }).join('');
        }

        function escapeHtml(s){ return String(s).replace(/[&<>"']/g,function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c]; }); }

        function openModal(){ if(modal){ modal.style.display='flex'; document.body.style.overflow='hidden'; renderFeedbacks(); } }
        function closeModal(){ if(modal){ modal.style.display='none'; document.body.style.overflow=''; msg.style.display='none'; msg.textContent=''; form.reset(); } }

        if(openBtn) openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
        if(closeBtn) closeBtn.addEventListener('click', closeModal);
        if(cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if(modal) modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });

        form.addEventListener('submit', function(e){
            e.preventDefault();
            const data = new FormData(form);
            const obj = { name: data.get('name'), email: data.get('email'), type: data.get('type'), message: data.get('message'), ts: Date.now() };
            saveFeedback(obj);
            msg.style.display='block'; msg.textContent='Thanks — your message has been recorded.';
            renderFeedbacks();
            form.reset();
        });

        // Ensure submitted list renders if modal exists on page load (if open later it will re-render)
        document.addEventListener('DOMContentLoaded', function(){ if(list) renderFeedbacks(); });
        </script>

        </body>
        </html>
