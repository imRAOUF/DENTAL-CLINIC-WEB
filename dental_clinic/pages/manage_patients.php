<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Handle Add Patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));

    if (!empty($name)) {
        $query = $conn->prepare("INSERT INTO patients (name, phone, email) VALUES (?, ?, ?)");
        $query->bind_param("sss", $name, $phone, $email);
        if ($query->execute()) {
            $message = "Patient added successfully!";
        } else {
            $message = "Error adding patient.";
        }
    } else {
        $message = "Name is required.";
    }
}

// Handle Delete Patient
if (isset($_GET['delete'])) {
    $patient_id = intval($_GET['delete']);
    $query = $conn->prepare("DELETE FROM patients WHERE patient_id = ?");
    $query->bind_param("i", $patient_id);
    if ($query->execute()) {
        $message = "Patient deleted successfully!";
    } else {
        $message = "Error deleting patient.";
    }
}

// Fetch Patients
$query = $conn->query("SELECT * FROM patients");
$patients = $query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients</title>
    <link rel="stylesheet" href="../assets/manage_patients.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_patients.php" class="active">Manage Patients</a></li>
                <li><a href="manage_appointments.php">Manage Appointments</a></li>
                <li><a href="manage_stock.php">Manage Stock</a></li>
                <li><a href="manage_prosthetics.php">Manage Prosthetics</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Patients</h1>
            
            <!-- Add Patient Form -->
            <form method="POST" action="" class="form-container">
                <h2>Add Patient</h2>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                <button type="submit" name="add_patient">Add Patient</button>
            </form>
            
            <p class="message"><?php echo $message; ?></p>
            
            <!-- Patients Table -->
            <h2>Existing Patients</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?php echo $patient['name']; ?></td>
                        <td><?php echo $patient['phone']; ?></td>
                        <td><?php echo $patient['email']; ?></td>
                        <td>
    <a href="edit_patient.php?id=<?php echo $patient['patient_id']; ?>">Edit</a> |
    <a href="?delete=<?php echo $patient['patient_id']; ?>" onclick="return confirm('Are you sure you want to delete this patient?');">Delete</a>
</td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
