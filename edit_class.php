<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$faculty_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Verify class ownership
$sql = "SELECT * FROM classes WHERE id = '$class_id' AND faculty_id = '$faculty_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Class not found or access denied.");
}

$class = $result->fetch_assoc();

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_class'])) {
    $class_name = $conn->real_escape_string($_POST['class_name']);
    $section = $conn->real_escape_string($_POST['section']);
    
    $update_sql = "UPDATE classes SET class_name='$class_name', section='$section' WHERE id='$class_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success = "Class updated successfully!";
        // Refresh data
        $class['class_name'] = $class_name;
        $class['section'] = $section;
    } else {
        $error = "Error updating class: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - <?php echo htmlspecialchars($class['class_name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --accent: #ffd700; --accent-color: #ffd700; --success-color: #28a745; --danger-color: #dc3545; --secondary-color: #ff9f00; --muted-text: #aaa; --glass-border: rgba(255, 255, 255, 0.1); }
        body { background: linear-gradient(135deg, #070416 0%, #0f0620 50%, #1a0a2e 100%); color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: rgba(5, 5, 20, 0.95); border-bottom: 2px solid rgba(255, 215, 0, 0.2); padding: 15px 0; margin-bottom: 30px; }
        header nav ul { display: flex; gap: 30px; list-style: none; }
        header a { color: white; text-decoration: none; }
        header a:hover { color: var(--accent); }
        .logo-assign { color: var(--accent); font-weight: bold; }
        .card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.04); border-radius: 14px; padding: 28px; box-shadow: 0 12px 40px rgba(2, 6, 23, 0.7); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: white; font-size: 1rem; transition: 0.3s ease; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 15px rgba(255, 215, 0, 0.2); }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-primary { background: linear-gradient(90deg, var(--accent), #ff9f00); color: #111; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(255, 215, 0, 0.3); }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); color: white; }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.15); }
        h2 { border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; margin-bottom: 20px; }
        h3 { margin-bottom: 15px; }
        .success-message { background-color: var(--success-color); color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error-message { background-color: var(--danger-color); color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <header>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo" style="display: flex; align-items: center; gap: 8px;">
                <img src="images/logo.jpeg" alt="AssignFlow Logo" style="height:40px; vertical-align:middle;">
                <a href="faculty_dashboard.php" style="color: inherit;"><span class="logo-assign">Assign</span><span style="color: white;">Flow</span></a>
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

    <div class="container" style="margin-top: 30px; max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; border-bottom: none; padding-bottom: 0;">Edit Class</h2>
            <a href="faculty_dashboard.php" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="edit_class.php?class_id=<?php echo $class_id; ?>" method="POST">
                <div class="form-group">
                    <label>Class Name</label>
                    <input type="text" name="class_name" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input type="text" name="section" value="<?php echo htmlspecialchars($class['section']); ?>" required>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="update_class" class="btn btn-primary" style="flex: 1;">Update Class</button>
                    <a href="faculty_dashboard.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
