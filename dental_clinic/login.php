<?php
session_start();
require 'includes/db_connect.php';

$message = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!empty($username) && !empty($password)) {
        $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect to admin dashboard or other role-specific pages
                if ($user['role'] === 'admin') {
                    header("Location: pages/admin_dashboard.php");
                } elseif ($user['role'] === 'doctor') {
                    header("Location: pages/doctor_dashboard.php");
                } else {
                    header("Location: pages/staff_dashboard.php");
                }
                exit();
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "User not found.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../dental_clinic/assets/index.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Welcome Back</h1>
            <p>Please log in to continue</p>
            <form method="POST" action="">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit">Login</button>
            </form>
            <p class="message"><?php echo $message; ?></p>
        </div>
    </div>
</body>
</html>
