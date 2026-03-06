<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$student_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Get Assignment Details
$assignment_sql = "SELECT * FROM assignments WHERE id = '$assignment_id'";
$assignment_result = $conn->query($assignment_sql);
if ($assignment_result->num_rows == 0) {
    die("Assignment not found.");
}
$assignment = $assignment_result->fetch_assoc();

// Check for existing submission
$sub_sql = "SELECT * FROM submissions WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
$sub_result = $conn->query($sub_sql);
$existing_submission = ($sub_result->num_rows > 0) ? $sub_result->fetch_assoc() : null;

// Check Deadline
$current_time = new DateTime();
$deadline_time = new DateTime($assignment['deadline']);

if ($current_time > $deadline_time) {
    die("Error: The deadline for this assignment has passed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submission_id = 0;
    
    // Create or Update Submission Record
    if ($existing_submission) {
        $submission_id = $existing_submission['id'];
        $sql = "UPDATE submissions SET submission_date = NOW(), status = 'pending' WHERE id = '$submission_id'";
        $conn->query($sql);
        // Delete old files to replace with new ones (simplification)
        $conn->query("DELETE FROM submission_files WHERE submission_id = '$submission_id'");
    } else {
        $sql = "INSERT INTO submissions (assignment_id, student_id, status) VALUES ('$assignment_id', '$student_id', 'pending')";
        if ($conn->query($sql) === TRUE) {
            $submission_id = $conn->insert_id;
        } else {
            $error = "Error creating submission record: " . $conn->error;
        }
    }

    // Check if at least one file is being uploaded
    $has_document = isset($_FILES['document']) && $_FILES['document']['error'] == 0;
    $has_audio = isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0;
    $has_video = isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0;

    if (!$has_document && !$has_audio && !$has_video) {
        $error = "Please upload at least one file (Document, Audio, or Video) to submit.";
    } elseif ($submission_id > 0) {
        $upload_dir = 'uploads/';
        
        // Handle Document Upload
        if ($has_document) {
            $filename = uniqid() . '_' . basename($_FILES['document']['name']);
            $target_file = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
                $sql = "INSERT INTO submission_files (submission_id, file_type, file_path) VALUES ('$submission_id', 'document', '$target_file')";
                $conn->query($sql);
            }
        }

        // Handle Audio Upload
        if ($has_audio) {
            $filename = uniqid() . '_audio.webm';
            $target_file = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $target_file)) {
                $duration = isset($_POST['audio_duration']) ? intval($_POST['audio_duration']) : 0;
                $sql = "INSERT INTO submission_files (submission_id, file_type, file_path, duration_seconds) VALUES ('$submission_id', 'audio', '$target_file', '$duration')";
                $conn->query($sql);
            }
        }

        // Handle Video Upload
        if ($has_video) {
            $filename = uniqid() . '_video.webm';
            $target_file = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
                $duration = isset($_POST['video_duration']) ? intval($_POST['video_duration']) : 0;
                $sql = "INSERT INTO submission_files (submission_id, file_type, file_path, duration_seconds) VALUES ('$submission_id', 'video', '$target_file', '$duration')";
                $conn->query($sql);
            }
        }

        $success = "Assignment submitted successfully!";
        // Refresh to show updated status
        header("Refresh: 2; url=student_view_class.php?class_id=" . $assignment['class_id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment - <?php echo htmlspecialchars($assignment['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="student_view_class.php?class_id=<?php echo $assignment['class_id']; ?>" style="color: inherit;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
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
            <h2 style="margin: 0;">Submit: <?php echo htmlspecialchars($assignment['title']); ?></h2>
            <a href="student_view_class.php?class_id=<?php echo $assignment['class_id']; ?>" class="btn" style="background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px;">
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

        <form action="submit_assignment.php?assignment_id=<?php echo $assignment_id; ?>" method="POST" enctype="multipart/form-data">
            
            <div class="card">
                <h3>1. Upload Answer Document (PDF/DOC) (Optional)</h3>
                <input type="file" name="document" accept=".pdf,.doc,.docx">
            </div>

            <div class="card">
                <h3>2. Record Audio Answer (Optional)</h3>
                <div class="submission-controls">
                    <button type="button" id="start-audio" class="icon-btn"><i class="fas fa-microphone"></i></button>
                    <button type="button" id="stop-audio" class="icon-btn" disabled><i class="fas fa-stop"></i></button>
                </div>
                <audio id="audio-playback" controls style="margin-top: 10px; width: 100%;"></audio>
                <div id="audio-duration-display" style="margin-top: 10px; font-size: 0.9rem; color: #aaa; display: none;">
                    Duration: <span id="audio-duration-text">0s</span>
                </div>
                <!-- Hidden input for audio file -->
                <input type="file" name="audio_file" id="audio-file-input" style="display: none;">
                <input type="hidden" name="audio_duration" id="audio-duration" value="0">
            </div>

            <div class="card">
                <h3>3. Record Video Answer (Optional)</h3>
                <div class="submission-controls">
                    <button type="button" id="start-video" class="icon-btn"><i class="fas fa-video"></i></button>
                    <button type="button" id="stop-video" class="icon-btn" disabled><i class="fas fa-stop"></i></button>
                </div>
                <video id="video-preview" style="width: 100%; max-width: 400px; display: none; margin-top: 10px;" autoplay muted></video>
                <video id="video-playback" controls style="width: 100%; max-width: 400px; display: none; margin-top: 10px;"></video>
                <div id="video-duration-display" style="margin-top: 10px; font-size: 0.9rem; color: #aaa; display: none;">
                    Duration: <span id="video-duration-text">0s</span>
                </div>
                <!-- Hidden input for video file -->
                <input type="file" name="video_file" id="video-file-input" style="display: none;">
                <input type="hidden" name="video_duration" id="video-duration" value="0">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 15px; font-size: 1.2rem;">Submit Assignment</button>
        </form>
    </div>

    <script src="js/recording.js"></script>
</body>
</html>
