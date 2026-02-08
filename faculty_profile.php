<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['user_id'];
$update_success = '';
$update_error = '';

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    
    $update_sql = "UPDATE users SET full_name='$full_name', email='$email', date_of_birth='$date_of_birth' WHERE id='$faculty_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $update_success = "Profile updated successfully!";
    } else {
        $update_error = "Error updating profile: " . $conn->error;
    }
}

// Get faculty details
$faculty_sql = "SELECT * FROM users WHERE id = '$faculty_id'";
$faculty_result = $conn->query($faculty_sql);
$faculty = $faculty_result->fetch_assoc();

// Get classes managed
$classes_sql = "SELECT COUNT(*) as total_classes FROM classes WHERE faculty_id = '$faculty_id'";
$classes_result = $conn->query($classes_sql);
$classes_data = $classes_result->fetch_assoc();

// Get assignments created
$assignments_sql = "SELECT COUNT(*) as total_assignments FROM assignments a 
                    JOIN classes c ON a.class_id = c.id 
                    WHERE c.faculty_id = '$faculty_id'";
$assignments_result = $conn->query($assignments_sql);
$assignments_data = $assignments_result->fetch_assoc();

// Get submissions received
$submissions_sql = "SELECT COUNT(*) as total_submissions FROM submissions s 
                    JOIN assignments a ON s.assignment_id = a.id 
                    JOIN classes c ON a.class_id = c.id 
                    WHERE c.faculty_id = '$faculty_id'";
$submissions_result = $conn->query($submissions_sql);
$submissions_data = $submissions_result->fetch_assoc();

// Get submissions graded
$graded_sql = "SELECT COUNT(*) as graded_submissions FROM submissions s 
               JOIN assignments a ON s.assignment_id = a.id 
               JOIN classes c ON a.class_id = c.id 
               WHERE c.faculty_id = '$faculty_id' AND s.grade IS NOT NULL";
$graded_result = $conn->query($graded_sql);
$graded_data = $graded_result->fetch_assoc();

// Get grade distribution
$grades_sql = "SELECT 
    SUM(CASE WHEN s.grade = 'A' THEN 1 ELSE 0 END) as grade_a,
    SUM(CASE WHEN s.grade = 'B' THEN 1 ELSE 0 END) as grade_b,
    SUM(CASE WHEN s.grade = 'C' THEN 1 ELSE 0 END) as grade_c,
    SUM(CASE WHEN s.grade = 'D' THEN 1 ELSE 0 END) as grade_d,
    SUM(CASE WHEN s.grade = 'E' THEN 1 ELSE 0 END) as grade_e,
    SUM(CASE WHEN s.grade = 'F' THEN 1 ELSE 0 END) as grade_f
    FROM submissions s 
    JOIN assignments a ON s.assignment_id = a.id 
    JOIN classes c ON a.class_id = c.id 
    WHERE c.faculty_id = '$faculty_id'";
$grades_result = $conn->query($grades_sql);
$grades = $grades_result->fetch_assoc();

// Calculate completion rate
$pending_sql = "SELECT COUNT(*) as pending_submissions FROM submissions s 
                JOIN assignments a ON s.assignment_id = a.id 
                JOIN classes c ON a.class_id = c.id 
                WHERE c.faculty_id = '$faculty_id' AND s.grade IS NULL";
$pending_result = $conn->query($pending_sql);
$pending_data = $pending_result->fetch_assoc();

$total_subs = $submissions_data['total_submissions'] ?: 1;
$completion_rate = round(($graded_data['graded_submissions'] / $total_subs) * 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo htmlspecialchars($faculty['full_name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 2px solid rgba(255, 215, 0, 0.3);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease;
        }

        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            animation: fadeInUp 0.6s ease;
        }

        .info-label {
            color: #aaa;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            color: white;
            font-size: 1.1rem;
            margin-top: 5px;
            font-weight: 500;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 215, 0, 0.3);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            animation: cardIn 0.8s ease;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .stat-label {
            color: #aaa;
            font-size: 0.9rem;
        }

        .grades-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .grade-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 215, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            animation: scaleIn 0.6s ease;
            transition: all 0.3s ease;
        }

        .grade-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
        }

        .grade-letter {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .grade-count {
            font-size: 1.5rem;
            color: white;
            margin-bottom: 5px;
        }

        .grade-label {
            font-size: 0.8rem;
            color: #aaa;
        }

        .completion-section {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 2px solid rgba(255, 215, 0, 0.4);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease;
        }

        .completion-text {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .completion-rate {
            font-size: 3rem;
            font-weight: bold;
            color: var(--accent-color);
        }

        .completion-label {
            color: #ccc;
            font-size: 1rem;
            margin-top: 10px;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .profile-info {
                grid-template-columns: 1fr;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .grades-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
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
        <a href="faculty_dashboard.php" class="back-button btn" style="background: rgba(255, 255, 255, 0.1); display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <?php if($update_success): ?>
            <div style="background-color: var(--success-color); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; animation: fadeInDown 0.6s ease;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i><?php echo $update_success; ?>
            </div>
        <?php endif; ?>
        <?php if($update_error): ?>
            <div style="background-color: #dc3545; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; animation: fadeInDown 0.6s ease;">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i><?php echo $update_error; ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1 style="margin: 0;"><i class="fas fa-user-circle" style="color: var(--accent-color); margin-right: 10px;"></i><?php echo htmlspecialchars($faculty['full_name']); ?>'s Profile</h1>
                <button type="button" onclick="toggleEditForm()" class="btn" style="background: rgba(255, 215, 0, 0.1); border: 2px solid var(--accent-color); padding: 8px 15px;">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>
            
            <!-- View Mode -->
            <div id="view-mode" class="profile-info">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-envelope" style="margin-right: 8px;"></i>Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($faculty['email'] ?: '(Not provided)'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-birthday-cake" style="margin-right: 8px;"></i>Date of Birth</div>
                    <div class="info-value"><?php echo $faculty['date_of_birth'] ? date("M d, Y", strtotime($faculty['date_of_birth'])) : '(Not provided)'; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-user-tag" style="margin-right: 8px;"></i>Username</div>
                    <div class="info-value"><?php echo htmlspecialchars($faculty['username']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-briefcase" style="margin-right: 8px;"></i>Role</div>
                    <div class="info-value" style="text-transform: capitalize;"><?php echo htmlspecialchars($faculty['role']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>Joined</div>
                    <div class="info-value"><?php echo date("M d, Y", strtotime($faculty['created_at'])); ?></div>
                </div>
            </div>

            <!-- Edit Mode -->
            <div id="edit-mode" style="display: none;">
                <form action="faculty_profile.php" method="POST" style="margin-top: 20px;">
                    <div class="profile-info">
                        <div class="info-item">
                            <label class="info-label"><i class="fas fa-user" style="margin-right: 8px;"></i>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($faculty['full_name']); ?>" style="width: 100%; padding: 8px; margin-top: 5px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,215,0,0.3); border-radius: 5px; color: white;" required>
                        </div>
                        <div class="info-item">
                            <label class="info-label"><i class="fas fa-envelope" style="margin-right: 8px;"></i>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($faculty['email'] ?: ''); ?>" style="width: 100%; padding: 8px; margin-top: 5px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,215,0,0.3); border-radius: 5px; color: white;" required>
                        </div>
                        <div class="info-item">
                            <label class="info-label"><i class="fas fa-birthday-cake" style="margin-right: 8px;"></i>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($faculty['date_of_birth'] ?: ''); ?>" style="width: 100%; padding: 8px; margin-top: 5px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,215,0,0.3); border-radius: 5px; color: white;" required>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" name="update_profile" class="btn btn-primary" style="background: linear-gradient(90deg, var(--accent-color), #ff9f00);">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" onclick="toggleEditForm()" class="btn" style="background: rgba(255, 255, 255, 0.1);">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <h2 style="margin-bottom: 20px; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px;">
            <i class="fas fa-chart-line" style="color: var(--accent-color); margin-right: 10px;"></i>Teaching Activity
        </h2>

        <div class="completion-section">
            <div class="completion-text">GRADING COMPLETION RATE</div>
            <div class="completion-rate"><?php echo $completion_rate; ?>%</div>
            <div class="completion-label"><?php echo $graded_data['graded_submissions']; ?> of <?php echo $total_subs; ?> submissions graded</div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $classes_data['total_classes']; ?></div>
                <div class="stat-label">Classes Created</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $assignments_data['total_assignments']; ?></div>
                <div class="stat-label">Assignments Posted</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $submissions_data['total_submissions']; ?></div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $graded_data['graded_submissions']; ?></div>
                <div class="stat-label">Submissions Graded</div>
            </div>
        </div>

        <h2 style="margin-top: 40px; margin-bottom: 20px; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px;">
            <i class="fas fa-star" style="color: var(--accent-color); margin-right: 10px;"></i>Grades Given Distribution
        </h2>

        <div class="grades-container">
            <div class="grade-card">
                <div class="grade-letter">A</div>
                <div class="grade-count"><?php echo $grades['grade_a'] ?: 0; ?></div>
                <div class="grade-label">Excellent</div>
            </div>
            <div class="grade-card">
                <div class="grade-letter">B</div>
                <div class="grade-count"><?php echo $grades['grade_b'] ?: 0; ?></div>
                <div class="grade-label">Good</div>
            </div>
            <div class="grade-card">
                <div class="grade-letter">C</div>
                <div class="grade-count"><?php echo $grades['grade_c'] ?: 0; ?></div>
                <div class="grade-label">Satisfactory</div>
            </div>
            <div class="grade-card">
                <div class="grade-letter">D</div>
                <div class="grade-count"><?php echo $grades['grade_d'] ?: 0; ?></div>
                <div class="grade-label">Pass</div>
            </div>
            <div class="grade-card">
                <div class="grade-letter">E</div>
                <div class="grade-count"><?php echo $grades['grade_e'] ?: 0; ?></div>
                <div class="grade-label">Weak</div>
            </div>
            <div class="grade-card">
                <div class="grade-letter">F</div>
                <div class="grade-count"><?php echo $grades['grade_f'] ?: 0; ?></div>
                <div class="grade-label">Failed</div>
            </div>
        </div>
    </div>

    <script>
        function toggleEditForm() {
            const viewMode = document.getElementById('view-mode');
            const editMode = document.getElementById('edit-mode');
            
            if (viewMode.style.display === 'none') {
                viewMode.style.display = '';
                editMode.style.display = 'none';
            } else {
                viewMode.style.display = 'none';
                editMode.style.display = '';
            }
        }
    </script>
</body>
</html>
