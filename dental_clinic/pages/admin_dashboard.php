<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

// Fetch dashboard stats
$patients_count = $conn->query("SELECT COUNT(*) AS count FROM patients")->fetch_assoc()['count'];
$appointments_count = $conn->query("SELECT COUNT(*) AS count FROM appointments")->fetch_assoc()['count'];
$stock_count = $conn->query("SELECT COUNT(*) AS count FROM stock")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admin_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_patients.php">Manage Patients</a></li>
                <li><a href="manage_appointments.php">Manage Appointments</a></li>
                <li><a href="manage_stock.php">Manage Stock</a></li>
                <li><a href="manage_prosthetics.php">Manage Prosthetics</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Welcome, Admin <?php echo $_SESSION['username']; ?>!</h1>
            
            <!-- Quick Stats Section -->
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h2><?php echo $patients_count; ?></h2>
                    <p>Total Patients</p>
                </div>
                <div class="stat-box">
                    <h2><?php echo $appointments_count; ?></h2>
                    <p>Upcoming Appointments</p>
                </div>
                <div class="stat-box">
                    <h2><?php echo $stock_count; ?></h2>
                    <p>Stock Items</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
