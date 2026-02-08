<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$faculty_id = $_SESSION['user_id'];

// Verify assignment ownership
$check_sql = "SELECT a.*, c.class_name, c.faculty_id FROM assignments a 
              JOIN classes c ON a.class_id = c.id 
              WHERE a.id = '$assignment_id' AND c.faculty_id = '$faculty_id'";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows == 0) {
    die("Access Denied or Assignment Not Found.");
}

$assignment_data = $check_result->fetch_assoc();

// Handle Grading
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['grade_submission'])) {
    $submission_id = intval($_POST['submission_id']);
    $grade = $conn->real_escape_string($_POST['grade']);
    $feedback = $conn->real_escape_string($_POST['feedback']);
    
    $sql = "UPDATE submissions SET grade='$grade', feedback='$feedback', status='graded' WHERE id='$submission_id'";
    $conn->query($sql);
}

// Fetch Submissions
$sql = "SELECT s.*, u.full_name, u.username FROM submissions s 
        JOIN users u ON s.student_id = u.id 
        WHERE s.assignment_id = '$assignment_id' ORDER BY s.submission_date DESC";
$submissions_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions - <?php echo htmlspecialchars($assignment_data['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="manage_class.php?class_id=<?php echo $assignment_data['class_id']; ?>" style="color: inherit;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
            </div>
            <nav>
                <ul>
                    <li><a href="faculty_dashboard.php">Dashboard</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Submissions for: <?php echo htmlspecialchars($assignment_data['title']); ?></h2>
            <a href="manage_class.php?class_id=<?php echo $assignment_data['class_id']; ?>" class="btn" style="background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Class
            </a>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: var(--secondary-color);">
                    <th style="padding: 10px; text-align: left;">Student</th>
                    <th style="padding: 10px; text-align: left;">Date</th>
                    <th style="padding: 10px; text-align: left;">Files</th>

                    <th style="padding: 10px; text-align: left;">Grade</th>
                    <th style="padding: 10px; text-align: left;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($submissions_result->num_rows > 0): ?>
                    <?php while($row = $submissions_result->fetch_assoc()): ?>
                        <?php
                            // Fetch files for this submission
                            $sub_id = $row['id'];
                            $files_sql = "SELECT * FROM submission_files WHERE submission_id = '$sub_id'";
                            $files_result = $conn->query($files_sql);
                            

                        ?>
                        <tr style="border-bottom: 1px solid #444;">
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['full_name']); ?> (<?php echo htmlspecialchars($row['username']); ?>)</td>
                            <td style="padding: 10px;"><?php echo date("M d, H:i", strtotime($row['submission_date'])); ?></td>
                            <td style="padding: 10px;">
                                <?php while($file = $files_result->fetch_assoc()): ?>
                                    <div>
                                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank" style="color: var(--accent-color);">
                                            <?php echo ucfirst($file['file_type']); ?>
                                        </a>
                                        <?php if($file['duration_seconds'] > 0): ?>
                                            <?php 
                                                $seconds = $file['duration_seconds'];
                                                $hours = floor($seconds / 3600);
                                                $mins = floor(($seconds % 3600) / 60);
                                                $secs = $seconds % 60;
                                                
                                                $duration_str = '';
                                                if ($hours > 0) {
                                                    $duration_str .= $hours . 'h ';
                                                }
                                                if ($mins > 0) {
                                                    $duration_str .= $mins . 'm ';
                                                }
                                                $duration_str .= $secs . 's';
                                            ?>
                                            <span style="color: #aaa; font-size: 0.9rem;" title="Duration: <?php echo $duration_str; ?>">- <?php echo $duration_str; ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </td>

                            <td style="padding: 10px;">
                                <span style="font-weight: bold; color: <?php echo $row['grade'] ? 'var(--success-color)' : 'var(--muted-text)'; ?>;">
                                    <?php echo $row['grade'] ? $row['grade'] : 'Pending'; ?>
                                </span>
                            </td>
                            <td style="padding: 10px;">
                                <form action="view_submissions.php?assignment_id=<?php echo $assignment_id; ?>" method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="submission_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="grade" placeholder="Grade (A-F)" value="<?php echo $row['grade']; ?>" style="width: 80px; padding: 5px;" required>
                                    <input type="text" name="feedback" placeholder="Feedback" value="<?php echo $row['feedback']; ?>" style="width: 150px; padding: 5px;">
                                    <button type="submit" name="grade_submission" class="btn" style="padding: 5px 10px;">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="padding: 20px; text-align: center;">No submissions yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
