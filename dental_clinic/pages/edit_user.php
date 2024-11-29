<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Fetch User Data
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $query = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        header("Location: manage_users.php");
        exit();
    }
} else {
    header("Location: manage_users.php");
    exit();
}

// Handle Update User Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $role = htmlspecialchars(trim($_POST['role']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!empty($username) && !empty($role)) {
        if (!empty($password)) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $update = $conn->prepare("UPDATE users SET username = ?, role = ?, password = ? WHERE user_id = ?");
            $update->bind_param("sssi", $username, $role, $hashed_password, $user_id);
        } else {
            // Update without changing password
            $update = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE user_id = ?");
            $update->bind_param("ssi", $username, $role, $user_id);
        }

        if ($update->execute()) {
            $message = "User updated successfully!";
        } else {
            $message = "Error updating user.";
        }
    } else {
        $message = "Username and role are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/edit_user.css">
</head>
<body>
    <div class="edit-user-container">
        <h1>Edit User</h1>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="doctor" <?php if ($user['role'] === 'doctor') echo 'selected'; ?>>Doctor</option>
                <option value="staff" <?php if ($user['role'] === 'staff') echo 'selected'; ?>>Staff</option>
            </select>

            <label for="password">New Password (Optional)</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">

            <button type="submit">Update User</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <a href="manage_users.php" class="back-link">Back to Manage Users</a>
    </div>
</body>
</html>
