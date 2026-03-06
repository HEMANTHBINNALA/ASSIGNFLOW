<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$faculty_id = $_SESSION['user_id'];

// Verify class ownership
$check_sql = "SELECT * FROM classes WHERE id = '$class_id' AND faculty_id = '$faculty_id'";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows == 0) {
    die("Access Denied or Class Not Found.");
}

$class_data = $check_result->fetch_assoc();
$success = '';
$error = '';

// Handle Create Assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_assignment'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $deadline = $conn->real_escape_string($_POST['deadline']);
    
    // Constraints (Video/Audio duration, etc.) - stored as JSON
    $constraints = [
        'min_audio_duration' => 120, // 2 mins in seconds
        'min_video_duration' => 120,
        'file_types' => ['pdf', 'doc', 'docx']
    ];
    $constraints_json = json_encode($constraints);

    $sql = "INSERT INTO assignments (class_id, title, description, deadline, constraints_json) 
            VALUES ('$class_id', '$title', '$description', '$deadline', '$constraints_json')";
    
    if ($conn->query($sql) === TRUE) {
        $success = "Assignment posted successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Delete Assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_assignment'])) {
    $assignment_id = intval($_POST['assignment_id']);
    
    // Security check: Ensure assignment belongs to this class and faculty owner
    $check_assign = "SELECT id FROM assignments WHERE id='$assignment_id' AND class_id='$class_id'";
    if ($conn->query($check_assign)->num_rows > 0) {
        $conn->query("DELETE FROM assignments WHERE id='$assignment_id'");
        $success = "Assignment deleted successfully.";
    } else {
        $error = "Failed to delete assignment.";
    }
}

// Fetch Assignments
$sql = "SELECT * FROM assignments WHERE class_id = '$class_id' ORDER BY deadline ASC";
$assignments_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class - <?php echo htmlspecialchars($class_data['class_name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="faculty_dashboard.php" style="color: inherit;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
            </div>
            <nav>
                <ul>
                    <li><a href="faculty_dashboard.php">Dashboard</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php" style="color: #ff6b6b;">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; border-bottom: none; padding-bottom: 0;">
                Class: <?php echo htmlspecialchars($class_data['class_name']) . " (" . htmlspecialchars($class_data['section']) . ")"; ?>
            </h2>
            <a href="faculty_dashboard.php" class="btn" style="background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div style="border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; margin-bottom: 20px;"></div>
        
        <?php if($success): ?>
            <div style="background-color: var(--success-color); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 20px;">
            <!-- Create Assignment Form -->
            <div class="card">
                <h3>Post New Assignment</h3>
                <form action="manage_class.php?class_id=<?php echo $class_id; ?>" method="POST">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="datetime-local" name="deadline" required>
                    </div>
                    <button type="submit" name="create_assignment" class="btn btn-primary" style="width: 100%;">Post Assignment</button>
                    <p style="font-size: 0.8rem; color: #aaa; margin-top: 10px;">
                        * Constraints (2 min Audio/Video check) are applied automatically.
                    </p>
                </form>
            </div>

            <!-- Assignments List -->
            <div>
                <h3>Assignments</h3>
                <?php if ($assignments_result->num_rows > 0): ?>
                    <?php while($row = $assignments_result->fetch_assoc()): ?>
                        <div class="card" style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between;">
                                <h4 style="margin: 0;"><?php echo htmlspecialchars($row['title']); ?></h4>
                                <span style="font-size: 0.9rem; color: var(--accent-color);">
                                    Due: <?php echo date("M d, Y H:i", strtotime($row['deadline'])); ?>
                                </span>
                            </div>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                            
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="view_submissions.php?assignment_id=<?php echo $row['id']; ?>" class="btn">
                                    <i class="fas fa-eye"></i> View Submissions
                                </a>
                                <a href="edit_assignment.php?assignment_id=<?php echo $row['id']; ?>" class="btn btn-primary" style="background: rgba(255, 215, 0, 0.2);">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="manage_class.php?class_id=<?php echo $class_id; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment?');" style="display: inline;">
                                    <input type="hidden" name="assignment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_assignment" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No assignments posted yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
