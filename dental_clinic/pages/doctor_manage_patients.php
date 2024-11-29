<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$doctor_id = $_SESSION['user_id'];
$message = "";

// Fetch Patients Assigned to the Doctor
$query = $conn->prepare("
    SELECT DISTINCT p.* 
    FROM patients p
    JOIN appointments a ON p.patient_id = a.patient_id
    WHERE a.doctor_id = ?
");
$query->bind_param("i", $doctor_id);
$query->execute();
$patients = $query->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients</title>
    <link rel="stylesheet" href="../assets/doctor_manage_patients.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Doctor Panel</h2>
            <ul>
                <li><a href="doctor_dashboard.php">Dashboard</a></li>
                <li><a href="doctor_manage_patients.php" class="active">Manage Patients</a></li>
                <li><a href="manage_appointments.php">Manage Appointments</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Patients</h1>

            <!-- Patients Table -->
            <section>
                <h2>Your Patients</h2>
                <?php if (count($patients) > 0): ?>
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
                                    <a href="view_patient.php?id=<?php echo $patient['patient_id']; ?>">View</a> |
                                    <a href="edit_patient.php?id=<?php echo $patient['patient_id']; ?>">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No patients assigned to you.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
