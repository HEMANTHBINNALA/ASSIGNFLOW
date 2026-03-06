<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

// Fetch All Classes (Students can see all folders/classes)
$sql = "SELECT c.*, u.full_name as faculty_name FROM classes c JOIN users u ON c.faculty_id = u.id ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Assignment Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <span class="logo-assign">Assign</span><span class="logo-flow">Flow</span>
                <span style="font-size: 0.8rem; color: #ccc; margin-left:8px;">Student</span>
            </div>
            <nav>
                <ul>
                    <li><a href="student_profile.php" style="color: var(--accent-color);"><i class="fas fa-user"></i> My Profile</a></li>
                    <li>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php" style="color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Available Classes / Folders</h2>
            <input type="text" id="search-input" placeholder="Search by class name or faculty..." style="padding: 10px 15px; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: white; width: 280px;" />
        </div>
        <div id="no-results" style="display: none; padding: 20px; text-align: center; color: var(--muted-text);">No classes match your search.</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;" id="classes-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card class-card" style="border-left: 5px solid var(--accent-color);" data-search="<?php echo strtolower(htmlspecialchars($row['class_name'] . ' ' . $row['section'] . ' ' . $row['faculty_name'])); ?>">
                        <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($row['class_name']); ?></h3>
                        <p style="color: var(--muted-text); font-size: 0.9rem;">Section: <?php echo htmlspecialchars($row['section']); ?></p>
                        <p style="color: var(--muted-text); font-size: 0.9rem;">Faculty: <?php echo htmlspecialchars($row['faculty_name']); ?></p>
                        <a href="student_view_class.php?class_id=<?php echo $row['id']; ?>" class="btn" style="margin-top: 15px; width: 80%; text-align: center;">View Assignments</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No classes available yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const searchInput = document.getElementById('search-input');
        const classCards = document.querySelectorAll('.class-card');
        const noResults = document.getElementById('no-results');
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();
            let visibleCount = 0;
            
            classCards.forEach(card => {
                const searchData = card.getAttribute('data-search');
                if (searchData.includes(searchTerm)) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.3s ease';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        });
        
        const style = document.createElement('style');
        style.textContent = '@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }';
        document.head.appendChild(style);
    </script>
</body>
</html>
