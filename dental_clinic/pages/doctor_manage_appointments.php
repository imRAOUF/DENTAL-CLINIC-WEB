<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$doctor_id = $_SESSION['user_id'];
$message = "";

// Fetch Today's Appointments
$today_date = date("Y-m-d");
$today_query = $conn->prepare("
    SELECT a.*, p.name AS patient_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.patient_id 
    WHERE a.doctor_id = ? AND a.date = ?
");
$today_query->bind_param("is", $doctor_id, $today_date);
$today_query->execute();
$today_appointments = $today_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch This Week's Appointments
$week_query = $conn->prepare("
    SELECT a.*, p.name AS patient_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.patient_id 
    WHERE a.doctor_id = ? AND YEARWEEK(a.date, 1) = YEARWEEK(CURDATE(), 1)
");
$week_query->bind_param("i", $doctor_id);
$week_query->execute();
$week_appointments = $week_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle Appointment Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $status = htmlspecialchars(trim($_POST['status']));
    
    $update_query = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ? AND doctor_id = ?");
    $update_query->bind_param("sii", $status, $appointment_id, $doctor_id);
    
    if ($update_query->execute()) {
        $message = "Appointment status updated successfully.";
    } else {
        $message = "Error updating appointment status.";
    }
}

// Handle Appointment Cancellation
if (isset($_GET['cancel'])) {
    $appointment_id = intval($_GET['cancel']);
    
    $cancel_query = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ? AND doctor_id = ?");
    $cancel_query->bind_param("ii", $appointment_id, $doctor_id);
    
    if ($cancel_query->execute()) {
        $message = "Appointment canceled successfully.";
    } else {
        $message = "Error canceling appointment.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="../assets/doctor_manage_appointments.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Doctor Panel</h2>
            <ul>
                <li><a href="doctor_dashboard.php">Dashboard</a></li>
                <li><a href="doctor_manage_patients.php">Manage Patients</a></li>
                <li><a href="manage_appointments.php" class="active">Manage Appointments</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Appointments</h1>
            <p class="message"><?php echo $message; ?></p>

            <!-- Today's Appointments -->
            <section>
                <h2>Today's Appointments</h2>
                <?php if (count($today_appointments) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($today_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['time']; ?></td>
                                <td><?php echo $appointment['patient_name']; ?></td>
                                <td><?php echo ucfirst($appointment['status']); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                        <select name="status">
                                            <option value="scheduled" <?php echo ($appointment['status'] === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                            <option value="completed" <?php echo ($appointment['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            <option value="canceled" <?php echo ($appointment['status'] === 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                                        </select>
                                        <button type="submit" name="update_status">Update Status</button>
                                    </form>
                                    <a href="?cancel=<?php echo $appointment['appointment_id']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">Cancel</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No appointments for today.</p>
                <?php endif; ?>
            </section>

            <!-- This Week's Appointments -->
            <section>
                <h2>This Week's Appointments</h2>
                <?php if (count($week_appointments) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($week_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['date']; ?></td>
                                <td><?php echo $appointment['time']; ?></td>
                                <td><?php echo $appointment['patient_name']; ?></td>
                                <td><?php echo ucfirst($appointment['status']); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                        <select name="status">
                                            <option value="scheduled" <?php echo ($appointment['status'] === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                            <option value="completed" <?php echo ($appointment['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            <option value="canceled" <?php echo ($appointment['status'] === 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                                        </select>
                                        <button type="submit" name="update_status">Update Status</button>
                                    </form>
                                    <a href="?cancel=<?php echo $appointment['appointment_id']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">Cancel</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No appointments for this week.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
