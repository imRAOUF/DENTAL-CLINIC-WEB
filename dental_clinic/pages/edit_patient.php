<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Get Patient Data for Editing
if (isset($_GET['id'])) {
    $patient_id = intval($_GET['id']);
    $query = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $query->bind_param("i", $patient_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $patient = $result->fetch_assoc();
    } else {
        header("Location: manage_patients.php");
        exit();
    }
} else {
    header("Location: manage_patients.php");
    exit();
}

// Handle Update Patient Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));

    if (!empty($name)) {
        $update = $conn->prepare("UPDATE patients SET name = ?, phone = ?, email = ? WHERE patient_id = ?");
        $update->bind_param("sssi", $name, $phone, $email, $patient_id);

        if ($update->execute()) {
            $message = "Patient updated successfully!";
        } else {
            $message = "Error updating patient.";
        }
    } else {
        $message = "Name is required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
    <link rel="stylesheet" href="../assets/edit_patient.css">
</head>
<body>
    <div class="edit-patient-container">
        <h1>Edit Patient</h1>
        <form method="POST" action="">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo $patient['name']; ?>" required>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo $patient['phone']; ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $patient['email']; ?>">

            <button type="submit">Update Patient</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <a href="manage_patients.php" class="back-link">Back to Manage Patients</a>
    </div>
</body>
</html>
