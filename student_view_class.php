<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$student_id = $_SESSION['user_id'];

// Get class info
$class_sql = "SELECT * FROM classes WHERE id = '$class_id'";
$class_result = $conn->query($class_sql);
if ($class_result->num_rows == 0) {
    die("Class not found.");
}
$class_data = $class_result->fetch_assoc();

// Get Assignments and check submission status for each
$sql = "SELECT a.*, 
        (SELECT id FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = '$student_id') as submission_id,
        (SELECT status FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = '$student_id') as status,
        (SELECT grade FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = '$student_id') as grade,
        (SELECT feedback FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = '$student_id') as feedback
        FROM assignments a WHERE class_id = '$class_id' ORDER BY deadline ASC";
$assignments_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($class_data['class_name']); ?> - Assignments</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="student_dashboard.php" style="color: inherit;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
            </div>
            <nav>
                <ul>
                    <li><a href="student_dashboard.php">Dashboard</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Class: <?php echo htmlspecialchars($class_data['class_name']); ?></h2>
            <a href="student_dashboard.php" class="btn" style="background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div style="margin-top: 20px;">
            <?php if ($assignments_result->num_rows > 0): ?>
                <?php while($row = $assignments_result->fetch_assoc()): ?>
                    <div class="card" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between;">
                            <h3 style="margin: 0;"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <span style="color: var(--accent-color);">Due: <?php echo date("M d, H:i", strtotime($row['deadline'])); ?></span>
                        </div>
                        <p style="margin: 10px 0;"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                            <div>
                                <strong>Status: </strong>
                                <span style="color: <?php echo ($row['status'] == 'graded') ? 'var(--success-color)' : (($row['status'] == 'pending') ? '#ffa500' : 'var(--muted-text)'); ?>">
                                    <?php 
                                        if ($row['status'] == 'graded') {
                                            echo 'Assignment Graded';
                                        } elseif ($row['status'] == 'pending') {
                                            echo 'Assignment Submitted - Evaluation Pending';
                                        } else {
                                            echo 'Not Submitted';
                                        }
                                    ?>
                                </span>
                                <?php if($row['grade']): ?>
                                    <span style="margin-left: 10px; font-weight: bold; color: var(--success-color);">Grade: <?php echo $row['grade']; ?></span>
                                <?php endif; ?>
                                
                                <?php if(!empty($row['feedback'])): ?>
                                    <div style="margin-top: 5px; font-size: 0.9rem; color: #fff; background: rgba(255, 255, 255, 0.1); padding: 5px 10px; border-radius: 5px;">
                                        <strong>Feedback:</strong> <?php echo htmlspecialchars($row['feedback']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <?php if($row['status']): ?>
                                    <a href="view_submission_files.php?submission_id=<?php echo $row['submission_id']; ?>" class="btn" style="background: rgba(255, 215, 0, 0.2);">
                                        <i class="fas fa-eye"></i> View Files
                                    </a>
                                <?php endif; ?>
                                <a href="submit_assignment.php?assignment_id=<?php echo $row['id']; ?>" class="btn">
                                    <?php echo $row['status'] ? 'Edit Submission' : 'Submit Assignment'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No assignments posted yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
