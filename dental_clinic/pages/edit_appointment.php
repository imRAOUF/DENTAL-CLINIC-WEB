<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Get Appointment Data for Editing
if (isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $query = $conn->prepare("
        SELECT a.*, p.name AS patient_name, u.username AS doctor_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN users u ON a.doctor_id = u.user_id
        WHERE a.appointment_id = ?
    ");
    $query->bind_param("i", $appointment_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $appointment = $result->fetch_assoc();
    } else {
        header("Location: manage_appointments.php");
        exit();
    }
} else {
    header("Location: manage_appointments.php");
    exit();
}

// Handle Update Appointment Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = intval($_POST['doctor_id']);
    $patient_id = intval($_POST['patient_id']);
    $date = htmlspecialchars(trim($_POST['date']));
    $time = htmlspecialchars(trim($_POST['time']));

    if (!empty($doctor_id) && !empty($patient_id) && !empty($date) && !empty($time)) {
        $update = $conn->prepare("
            UPDATE appointments 
            SET doctor_id = ?, patient_id = ?, date = ?, time = ?
            WHERE appointment_id = ?
        ");
        $update->bind_param("iissi", $doctor_id, $patient_id, $date, $time, $appointment_id);

        if ($update->execute()) {
            $message = "Appointment updated successfully!";
        } else {
            $message = "Error updating appointment.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="../assets/edit_appointment.css">
</head>
<body>
    <div class="edit-appointment-container">
        <h1>Edit Appointment</h1>
        <form method="POST" action="">
            <label for="doctor_id">Doctor</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">Select a Doctor</option>
                <?php
                $doctors = $conn->query("SELECT user_id, username FROM users WHERE role = 'doctor'");
                while ($doctor = $doctors->fetch_assoc()) {
                    $selected = ($doctor['user_id'] == $appointment['doctor_id']) ? 'selected' : '';
                    echo "<option value='{$doctor['user_id']}' {$selected}>{$doctor['username']}</option>";
                }
                ?>
            </select>

            <label for="patient_id">Patient</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Select a Patient</option>
                <?php
                $patients = $conn->query("SELECT patient_id, name FROM patients");
                while ($patient = $patients->fetch_assoc()) {
                    $selected = ($patient['patient_id'] == $appointment['patient_id']) ? 'selected' : '';
                    echo "<option value='{$patient['patient_id']}' {$selected}>{$patient['name']}</option>";
                }
                ?>
            </select>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo $appointment['date']; ?>" required>

            <label for="time">Time</label>
            <input type="time" id="time" name="time" value="<?php echo $appointment['time']; ?>" required>

            <button type="submit">Update Appointment</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <a href="manage_appointments.php" class="back-link">Back to Manage Appointments</a>
    </div>
</body>
</html>
