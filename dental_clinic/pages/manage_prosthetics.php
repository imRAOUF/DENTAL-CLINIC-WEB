<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Handle Add Prosthetic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_prosthetic'])) {
    $type = htmlspecialchars(trim($_POST['type']));
    $patient_id = intval($_POST['patient_id']);
    $status = htmlspecialchars(trim($_POST['status']));
    $payment_status = htmlspecialchars(trim($_POST['payment_status']));

    if (!empty($type) && !empty($patient_id) && !empty($status) && !empty($payment_status)) {
        $query = $conn->prepare("INSERT INTO prosthetics (type, patient_id, status, payment_status) VALUES (?, ?, ?, ?)");
        $query->bind_param("siss", $type, $patient_id, $status, $payment_status);
        if ($query->execute()) {
            $message = "Prosthetic record added successfully!";
        } else {
            $message = "Error adding prosthetic record.";
        }
    } else {
        $message = "All fields are required.";
    }
}

// Handle Delete Prosthetic
if (isset($_GET['delete'])) {
    $prosthetic_id = intval($_GET['delete']);
    $query = $conn->prepare("DELETE FROM prosthetics WHERE prosthetic_id = ?");
    $query->bind_param("i", $prosthetic_id);
    if ($query->execute()) {
        $message = "Prosthetic record deleted successfully!";
    } else {
        $message = "Error deleting prosthetic record.";
    }
}

// Fetch Prosthetics
$query = $conn->query("
    SELECT p.*, pt.name AS patient_name 
    FROM prosthetics p 
    JOIN patients pt ON p.patient_id = pt.patient_id
");
$prosthetics = $query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prosthetics</title>
    <link rel="stylesheet" href="../assets/manage_prosthetics.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_patients.php">Manage Patients</a></li>
                <li><a href="manage_appointments.php">Manage Appointments</a></li>
                <li><a href="manage_stock.php">Manage Stock</a></li>
                <li><a href="manage_prosthetics.php" class="active">Manage Prosthetics</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Prosthetics</h1>
            
            <!-- Add Prosthetic Form -->
            <form method="POST" action="" class="form-container">
                <h2>Add Prosthetic</h2>
                <label for="type">Type</label>
                <input type="text" id="type" name="type" required>
                <label for="patient_id">Patient</label>
                <select id="patient_id" name="patient_id" required>
                    <option value="">Select a Patient</option>
                    <?php
                    $patients = $conn->query("SELECT patient_id, name FROM patients");
                    while ($patient = $patients->fetch_assoc()) {
                        echo "<option value='{$patient['patient_id']}'>{$patient['name']}</option>";
                    }
                    ?>
                </select>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="ordered">Ordered</option>
                    <option value="ready">Ready</option>
                    <option value="delivered">Delivered</option>
                </select>
                <label for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status" required>
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                </select>
                <button type="submit" name="add_prosthetic">Add Prosthetic</button>
            </form>
            
            <p class="message"><?php echo $message; ?></p>
            
            <!-- Prosthetics Table -->
            <h2>Existing Prosthetics</h2>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Patient</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prosthetics as $prosthetic): ?>
                    <tr>
                        <td><?php echo $prosthetic['type']; ?></td>
                        <td><?php echo $prosthetic['patient_name']; ?></td>
                        <td><?php echo ucfirst($prosthetic['status']); ?></td>
                        <td><?php echo ucfirst($prosthetic['payment_status']); ?></td>
                        <td><?php echo $prosthetic['created_at']; ?></td>
                        <td>
    <a href="edit_prosthetic.php?id=<?php echo $prosthetic['prosthetic_id']; ?>">Edit</a> |
    <a href="?delete=<?php echo $prosthetic['prosthetic_id']; ?>" onclick="return confirm('Are you sure you want to delete this prosthetic?');">Delete</a>
</td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
