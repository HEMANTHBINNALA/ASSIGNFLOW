<?php
session_start();
require 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $conn->real_escape_string($_POST['role']);
        $fullname = $conn->real_escape_string($_POST['fullname']);
        $email = $conn->real_escape_string($_POST['email']);
        $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);

        // Check if username already exists
        $check_username = "SELECT id FROM users WHERE username='$username'";
        $username_result = $conn->query($check_username);
        
        if ($username_result->num_rows > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            // Check if email already exists
            $check_email = "SELECT id FROM users WHERE email='$email'";
            $email_result = $conn->query($check_email);
            
            if ($email_result->num_rows > 0) {
                $error = "Email already registered. Please use a different email or login.";
            } else {
                $sql = "INSERT INTO users (username, password, role, full_name, email, date_of_birth) VALUES ('$username', '$password', '$role', '$fullname', '$email', '$date_of_birth')";

                if ($conn->query($sql) === TRUE) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    } elseif (isset($_POST['login'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT id, username, password, role, full_name FROM users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['fullname'] = $row['full_name'];

                if ($row['role'] == 'faculty') {
                    header("Location: faculty_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Invalid user.";
        }
    }
}
?>
