<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$submission_id = isset($_GET['submission_id']) ? intval($_GET['submission_id']) : 0;
$student_id = $_SESSION['user_id'];

// Verify submission belongs to this student
$sql = "SELECT s.*, a.title, a.class_id, c.class_name FROM submissions s
        JOIN assignments a ON s.assignment_id = a.id
        JOIN classes c ON a.class_id = c.id
        WHERE s.id = '$submission_id' AND s.student_id = '$student_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Submission not found or access denied.");
}

$submission = $result->fetch_assoc();
$class_id = $submission['class_id'];
$assignment_id = $submission['assignment_id'];

// Get all files for this submission
$files_sql = "SELECT * FROM submission_files WHERE submission_id = '$submission_id'";
$files_result = $conn->query($files_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submission - <?php echo htmlspecialchars($submission['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .file-section {
            margin: 20px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
        }
        
        .file-item {
            padding: 12px;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.03);
            border-left: 4px solid #ffd700;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        
        .file-type {
            background: linear-gradient(135deg, #ffd700, #ff9f00);
            color: #111;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            min-width: 80px;
            text-align: center;
        }
        
        .file-duration {
            color: #aaa;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        .download-btn {
            background: rgba(255, 215, 0, 0.2);
            border: 1px solid rgba(255, 215, 0, 0.5);
            color: #ffd700;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .download-btn:hover {
            background: rgba(255, 215, 0, 0.4);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        }
        
        .preview-container {
            margin-top: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 5px;
        }
        
        .preview-container audio,
        .preview-container video {
            width: 100%;
            max-width: 500px;
            margin-top: 10px;
        }
        
        .no-files {
            text-align: center;
            color: #aaa;
            padding: 30px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="student_view_class.php?class_id=<?php echo $class_id; ?>" style="color: inherit;"><span class="logo-assign">Assign</span><span class="logo-flow">Flow</span></a>
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

    <div class="container" style="margin-top: 30px; max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Submission: <?php echo htmlspecialchars($submission['title']); ?></h2>
            <a href="student_view_class.php?class_id=<?php echo $class_id; ?>" class="btn" style="background: rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Class
            </a>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-info-circle"></i> Submission Details</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div>
                    <p><strong>Status:</strong></p>
                    <p style="color: <?php echo ($submission['status'] == 'graded') ? '#51cf66' : '#ffa500'; ?>;">
                        <?php 
                            if ($submission['status'] == 'graded') {
                                echo 'Assignment Graded';
                            } elseif ($submission['status'] == 'pending') {
                                echo 'Assignment Submitted - Evaluation Pending';
                            } else {
                                echo ucfirst($submission['status']);
                            }
                        ?>
                    </p>
                </div>
                <div>
                    <p><strong>Submitted:</strong></p>
                    <p><?php echo date("M d, Y H:i", strtotime($submission['submission_date'])); ?></p>
                </div>
                <?php if($submission['grade']): ?>
                <div>
                    <p><strong>Grade:</strong></p>
                    <p style="color: #51cf66; font-weight: bold; font-size: 1.2rem;"><?php echo htmlspecialchars($submission['grade']); ?></p>
                </div>
                <?php endif; ?>
                <?php if(!empty($submission['feedback'])): ?>
                <div>
                    <p><strong>Feedback:</strong></p>
                    <p style="color: #ffd700;"><?php echo htmlspecialchars($submission['feedback']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-file-upload"></i> Your Uploaded Files</h3>
            
            <?php if ($files_result->num_rows > 0): ?>
                <div class="file-section">
                    <?php while($file = $files_result->fetch_assoc()): ?>
                        <div class="file-item">
                            <div class="file-info">
                                <span class="file-type">
                                    <i class="fas fa-<?php 
                                        if ($file['file_type'] == 'document') echo 'file-pdf';
                                        elseif ($file['file_type'] == 'audio') echo 'volume-up';
                                        elseif ($file['file_type'] == 'video') echo 'video';
                                    ?>"></i>
                                    <?php echo ucfirst($file['file_type']); ?>
                                </span>
                                <div>
                                    <div style="color: white;">File: <?php echo basename($file['file_path']); ?></div>
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
                                        <div class="file-duration"><i class="fas fa-clock"></i> Duration: <?php echo $duration_str; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?php echo htmlspecialchars($file['file_path']); ?>" download class="download-btn">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>

                        <?php if($file['file_type'] == 'document'): ?>
                            <div class="preview-container">
                                <strong style="color: #ffd700;">Preview:</strong>
                                <?php 
                                    $file_ext = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION));
                                    if($file_ext == 'pdf'): 
                                ?>
                                    <iframe src="<?php echo htmlspecialchars($file['file_path']); ?>" style="width: 100%; height: 400px; border: none; border-radius: 5px;"></iframe>
                                <?php endif; ?>
                            </div>
                        <?php elseif($file['file_type'] == 'audio'): ?>
                            <div class="preview-container">
                                <strong style="color: #ffd700;">Play:</strong>
                                <audio controls style="width: 100%; margin-top: 10px;">
                                    <source src="<?php echo htmlspecialchars($file['file_path']); ?>" type="audio/webm">
                                    <source src="<?php echo htmlspecialchars($file['file_path']); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        <?php elseif($file['file_type'] == 'video'): ?>
                            <div class="preview-container">
                                <strong style="color: #ffd700;">Play:</strong>
                                <video controls style="width: 100%; margin-top: 10px;">
                                    <source src="<?php echo htmlspecialchars($file['file_path']); ?>" type="video/webm">
                                    <source src="<?php echo htmlspecialchars($file['file_path']); ?>" type="video/mp4">
                                    Your browser does not support the video element.
                                </video>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-files">
                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p>No files uploaded yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3><i class="fas fa-edit"></i> Faculty Feedback</h3>
            <?php if(!empty($submission['feedback'])): ?>
                <div class="file-section">
                    <p style="color: #ffd700; background: rgba(255, 215, 0, 0.1); padding: 15px; border-radius: 5px; border-left: 4px solid #ffd700;">
                        <strong>Feedback:</strong> <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="no-files">
                    <p style="color: #aaa;">No feedback provided yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <a href="submit_assignment.php?assignment_id=<?php echo $assignment_id; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit/Resubmit
            </a>
        </div>
    </div>
</body>
</html>
