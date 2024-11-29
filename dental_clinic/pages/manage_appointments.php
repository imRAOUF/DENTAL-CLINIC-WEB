<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Handle Add Appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_appointment'])) {
    $doctor_id = intval($_POST['doctor_id']);
    $patient_id = intval($_POST['patient_id']);
    $date = htmlspecialchars(trim($_POST['date']));
    $time = htmlspecialchars(trim($_POST['time']));

    if (!empty($doctor_id) && !empty($patient_id) && !empty($date) && !empty($time)) {
        $query = $conn->prepare("INSERT INTO appointments (doctor_id, patient_id, date, time) VALUES (?, ?, ?, ?)");
        $query->bind_param("iiss", $doctor_id, $patient_id, $date, $time);
        if ($query->execute()) {
            $message = "Appointment added successfully!";
        } else {
            $message = "Error adding appointment.";
        }
    } else {
        $message = "All fields are required.";
    }
}

// Handle Delete Appointment
if (isset($_GET['delete'])) {
    $appointment_id = intval($_GET['delete']);
    $query = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
    $query->bind_param("i", $appointment_id);
    if ($query->execute()) {
        $message = "Appointment deleted successfully!";
    } else {
        $message = "Error deleting appointment.";
    }
}

// Fetch Appointments
$query = $conn->query("
    SELECT a.*, p.name AS patient_name, u.username AS doctor_name 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    JOIN users u ON a.doctor_id = u.user_id
");
$appointments = $query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="../assets/manage_appointments.css">
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
                <li><a href="manage_appointments.php" class="active">Manage Appointments</a></li>
                <li><a href="manage_stock.php">Manage Stock</a></li>
                <li><a href="manage_prosthetics.php">Manage Prosthetics</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Appointments</h1>
            
            <!-- Add Appointment Form -->
            <form method="POST" action="" class="form-container">
                <h2>Add Appointment</h2>
                <label for="doctor_id">Doctor</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">Select a Doctor</option>
                    <?php
                    $doctors = $conn->query("SELECT user_id, username FROM users WHERE role = 'doctor'");
                    while ($doctor = $doctors->fetch_assoc()) {
                        echo "<option value='{$doctor['user_id']}'>{$doctor['username']}</option>";
                    }
                    ?>
                </select>
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
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
                <label for="time">Time</label>
                <input type="time" id="time" name="time" required>
                <button type="submit" name="add_appointment">Add Appointment</button>
            </form>
            
            <p class="message"><?php echo $message; ?></p>
            
            <!-- Appointments Table -->
            <h2>Existing Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo $appointment['patient_name']; ?></td>
                        <td><?php echo $appointment['doctor_name']; ?></td>
                        <td><?php echo $appointment['date']; ?></td>
                        <td><?php echo $appointment['time']; ?></td>
                        <td>
    <a href="edit_appointment.php?id=<?php echo $appointment['appointment_id']; ?>">Edit</a> |
    <a href="?delete=<?php echo $appointment['appointment_id']; ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
</td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
