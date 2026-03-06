<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle Create Class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_class'])) {
    $class_name = $conn->real_escape_string($_POST['class_name']);
    $section = $conn->real_escape_string($_POST['section']);
    
    $sql = "INSERT INTO classes (class_name, section, faculty_id) VALUES ('$class_name', '$section', '$faculty_id')";
    
    if ($conn->query($sql) === TRUE) {
        $success = "Class created successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Delete Class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_class'])) {
    $class_id = intval($_POST['class_id']);
    
    // Verify class ownership
    $check_sql = "SELECT id FROM classes WHERE id='$class_id' AND faculty_id='$faculty_id'";
    if ($conn->query($check_sql)->num_rows > 0) {
        // Delete all assignments in this class first
        $conn->query("DELETE FROM assignments WHERE class_id='$class_id'");
        // Then delete the class
        $conn->query("DELETE FROM classes WHERE id='$class_id'");
        $success = "Class deleted successfully.";
    } else {
        $error = "Failed to delete class.";
    }
}

// Fetch Classes
$sql = "SELECT * FROM classes WHERE faculty_id = '$faculty_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Assignment Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <span class="logo-assign">Assign</span><span class="logo-flow">Flow</span>
                <span style="font-size: 0.8rem; color: #ccc; margin-left:8px;">Faculty</span>
            </div>
            <nav>
                <ul>
                    <li><a href="faculty_profile.php" style="color: var(--accent-color);"><i class="fas fa-user"></i> My Profile</a></li>
                    <li>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php" style="color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <?php if($success): ?>
            <div style="background-color: var(--success-color); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 30px;">
            <!-- Sidebar / Create Class -->
            <div>
                <div class="card">
                    <h3>Create New Class</h3>
                    <form action="faculty_dashboard.php" method="POST">
                        <div class="form-group">
                            <label>Class Name (e.g., CSD)</label>
                            <input type="text" name="class_name" required>
                        </div>
                        <div class="form-group">
                            <label>Section (e.g., A)</label>
                            <input type="text" name="section" required>
                        </div>
                        <button type="submit" name="create_class" class="btn btn-primary" style="width: 100%;">Create Class</button>
                    </form>
                </div>
            </div>

            <!-- Main Content / Class List -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Your Classes</h2>
                    <input type="text" id="search-input" placeholder="Search by class name or section..." style="padding: 10px 15px; border-radius: 8px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: white; width: 280px;" />
                </div>
                <div id="no-results" style="display: none; padding: 20px; text-align: center; color: var(--muted-text);">No classes match your search.</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;" id="classes-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="card class-card" style="border-left: 5px solid var(--accent-color);" data-search="<?php echo strtolower(htmlspecialchars($row['class_name'] . ' ' . $row['section'])); ?>">
                                <h3><?php echo htmlspecialchars($row['class_name']); ?></h3>
                                <p style="color: var(--muted-text);">Section: <?php echo htmlspecialchars($row['section']); ?></p>
                                <a href="manage_class.php?class_id=<?php echo $row['id']; ?>" class="btn" style="margin-top: 10px; width: 100%; text-align: center;">Manage Assignments</a>
                                
                                <div style="display: flex; gap: 10px; margin-top: 10px;">
                                    <a href="edit_class.php?class_id=<?php echo $row['id']; ?>" class="btn" style="flex: 1; background: rgba(255, 215, 0, 0.2); text-align: center;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="faculty_dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this class and all its assignments?');" style="flex: 1;">
                                        <input type="hidden" name="class_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_class" class="btn btn-danger" style="width: 100%;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No classes found. Create one to get started.</p>
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
        </div>
    </div>
</body>
</html>
