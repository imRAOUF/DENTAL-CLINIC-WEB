<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Fetch Prosthetic Data for Editing
if (isset($_GET['id'])) {
    $prosthetic_id = intval($_GET['id']);
    $query = $conn->prepare("
        SELECT p.*, pt.name AS patient_name 
        FROM prosthetics p 
        JOIN patients pt ON p.patient_id = pt.patient_id 
        WHERE prosthetic_id = ?
    ");
    $query->bind_param("i", $prosthetic_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $prosthetic = $result->fetch_assoc();
    } else {
        header("Location: manage_prosthetics.php");
        exit();
    }
} else {
    header("Location: manage_prosthetics.php");
    exit();
}

// Handle Update Prosthetic Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = htmlspecialchars(trim($_POST['type']));
    $patient_id = intval($_POST['patient_id']);
    $status = htmlspecialchars(trim($_POST['status']));
    $payment_status = htmlspecialchars(trim($_POST['payment_status']));

    if (!empty($type) && !empty($patient_id) && !empty($status) && !empty($payment_status)) {
        $update = $conn->prepare("
            UPDATE prosthetics 
            SET type = ?, patient_id = ?, status = ?, payment_status = ? 
            WHERE prosthetic_id = ?
        ");
        $update->bind_param("sissi", $type, $patient_id, $status, $payment_status, $prosthetic_id);

        if ($update->execute()) {
            $message = "Prosthetic updated successfully!";
        } else {
            $message = "Error updating prosthetic.";
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
    <title>Edit Prosthetic</title>
    <link rel="stylesheet" href="../assets/edit_prosthetic.css">
</head>
<body>
    <div class="edit-prosthetic-container">
        <h1>Edit Prosthetic</h1>
        <form method="POST" action="">
            <label for="type">Type</label>
            <input type="text" id="type" name="type" value="<?php echo $prosthetic['type']; ?>" required>

            <label for="patient_id">Patient</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Select a Patient</option>
                <?php
                $patients = $conn->query("SELECT patient_id, name FROM patients");
                while ($patient = $patients->fetch_assoc()) {
                    $selected = ($patient['patient_id'] == $prosthetic['patient_id']) ? 'selected' : '';
                    echo "<option value='{$patient['patient_id']}' {$selected}>{$patient['name']}</option>";
                }
                ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="ordered" <?php echo ($prosthetic['status'] === 'ordered') ? 'selected' : ''; ?>>Ordered</option>
                <option value="ready" <?php echo ($prosthetic['status'] === 'ready') ? 'selected' : ''; ?>>Ready</option>
                <option value="delivered" <?php echo ($prosthetic['status'] === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
            </select>

            <label for="payment_status">Payment Status</label>
            <select id="payment_status" name="payment_status" required>
                <option value="unpaid" <?php echo ($prosthetic['payment_status'] === 'unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                <option value="paid" <?php echo ($prosthetic['payment_status'] === 'paid') ? 'selected' : ''; ?>>Paid</option>
            </select>

            <button type="submit">Update Prosthetic</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <a href="manage_prosthetics.php" class="back-link">Back to Manage Prosthetics</a>
    </div>
</body>
</html>
