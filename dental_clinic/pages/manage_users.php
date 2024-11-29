<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $role = htmlspecialchars(trim($_POST['role']));

    if (!empty($username) && !empty($password) && !empty($role)) {
        $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 0) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $username, $hashed_password, $role);

            if ($insert->execute()) {
                $message = "User added successfully!";
            } else {
                $message = "Error adding user.";
            }
        } else {
            $message = "Username already exists.";
        }
    } else {
        $message = "All fields are required.";
    }
}

// Handle Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = htmlspecialchars(trim($_POST['username']));
    $role = htmlspecialchars(trim($_POST['role']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!empty($username) && !empty($role)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $update = $conn->prepare("UPDATE users SET username = ?, role = ?, password = ? WHERE user_id = ?");
            $update->bind_param("sssi", $username, $role, $hashed_password, $user_id);
        } else {
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

// Handle Delete User
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $delete = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $delete->bind_param("i", $user_id);

    if ($delete->execute()) {
        $message = "User deleted successfully!";
    } else {
        $message = "Error deleting user.";
    }
}

// Fetch All Users
$users_query = $conn->query("SELECT user_id, username, role FROM users WHERE role != 'admin'");
$users = $users_query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/manage_users.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="manage_patients.php">Manage Patients</a></li>
                <li><a href="manage_appointments.php">Manage Appointments</a></li>
                <li><a href="manage_stock.php">Manage Stock</a></li>
                <li><a href="manage_prosthetics.php">Manage Prosthetics</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Users</h1>

            <!-- Add User Form -->
            <div class="add-user-form">
                <h2>Add New User</h2>
                <form method="POST" action="">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="doctor">Doctor</option>
                        <option value="staff">Staff</option>
                    </select>

                    <button type="submit" name="add_user">Add User</button>
                </form>
            </div>

            <!-- Users Table -->
            <h2>Existing Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['username']; ?></td>
                         <td><?php echo ucfirst($user['role']); ?></td>
                          <td>
                             <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Edit</a> |
                              <a href="?delete=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                           </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Display Message -->
            <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
