<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$doctor_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch today's appointments
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

// Fetch this week's appointments
$week_query = $conn->prepare("
    SELECT a.*, p.name AS patient_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.patient_id 
    WHERE a.doctor_id = ? AND YEARWEEK(a.date, 1) = YEARWEEK(CURDATE(), 1)
");
$week_query->bind_param("i", $doctor_id);
$week_query->execute();
$week_appointments = $week_query->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../assets/doctor_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Doctor Panel</h2>
            <ul>
                <li><a href="doctor_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="../pages/doctor_manage_patients.php">Manage Patients</a></li>
                <li><a href="../pages/doctor_manage_appointments.php">Manage Appointments</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Welcome, Dr. <?php echo $username; ?>!</h1>

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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($today_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['time']; ?></td>
                                <td><?php echo $appointment['patient_name']; ?></td>
                                <td>Upcoming</td>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($week_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['date']; ?></td>
                                <td><?php echo $appointment['time']; ?></td>
                                <td><?php echo $appointment['patient_name']; ?></td>
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
