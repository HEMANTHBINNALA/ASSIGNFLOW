<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$faculty_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Verify assignment ownership
$sql = "SELECT a.*, c.class_name FROM assignments a 
        JOIN classes c ON a.class_id = c.id 
        WHERE a.id = '$assignment_id' AND c.faculty_id = '$faculty_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Assignment not found or access denied.");
}

$assignment = $result->fetch_assoc();

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_assignment'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $deadline = $conn->real_escape_string($_POST['deadline']);
    
    $update_sql = "UPDATE assignments SET title='$title', description='$description', deadline='$deadline' WHERE id='$assignment_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success = "Assignment updated successfully!";
        // Refresh data
        $assignment['title'] = $title;
        $assignment['description'] = $description;
        $assignment['deadline'] = $deadline;
    } else {
        $error = "Error updating assignment: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment - <?php echo htmlspecialchars($assignment['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="manage_class.php?class_id=<?php echo $assignment['class_id']; ?>" style="color: inherit;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
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

    <div class="container" style="margin-top: 30px; max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Edit Assignment</h2>
            <a href="manage_class.php?class_id=<?php echo $assignment['class_id']; ?>" class="btn" style="background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Class
            </a>
        </div>
        
        <?php if($success): ?>
            <div style="background-color: var(--success-color); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div style="background-color: var(--danger-color); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="edit_assignment.php?assignment_id=<?php echo $assignment_id; ?>" method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="6" required><?php echo htmlspecialchars($assignment['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Deadline</label>
                    <input type="datetime-local" name="deadline" value="<?php echo date('Y-m-d\TH:i', strtotime($assignment['deadline'])); ?>" required>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="update_assignment" class="btn btn-primary" style="flex: 1;">Update Assignment</button>
                    <a href="manage_class.php?class_id=<?php echo $assignment['class_id']; ?>" class="btn" style="background: rgba(255, 255, 255, 0.1);">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
