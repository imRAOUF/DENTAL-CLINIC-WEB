<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Get Stock Item Data for Editing
if (isset($_GET['id'])) {
    $stock_id = intval($_GET['id']);
    $query = $conn->prepare("SELECT * FROM stock WHERE stock_id = ?");
    $query->bind_param("i", $stock_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $stock_item = $result->fetch_assoc();
    } else {
        header("Location: manage_stock.php");
        exit();
    }
} else {
    header("Location: manage_stock.php");
    exit();
}

// Handle Update Stock Item Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $quantity = intval($_POST['quantity']);
    $expiration_date = htmlspecialchars(trim($_POST['expiration_date']));

    if (!empty($product_name) && $quantity > 0) {
        $update = $conn->prepare("
            UPDATE stock 
            SET product_name = ?, quantity = ?, expiration_date = ?
            WHERE stock_id = ?
        ");
        $update->bind_param("sisi", $product_name, $quantity, $expiration_date, $stock_id);

        if ($update->execute()) {
            $message = "Stock item updated successfully!";
        } else {
            $message = "Error updating stock item.";
        }
    } else {
        $message = "Product name and quantity are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock Item</title>
    <link rel="stylesheet" href="../assets/edit_stock.css">
</head>
<body>
    <div class="edit-stock-container">
        <h1>Edit Stock Item</h1>
        <form method="POST" action="">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo $stock_item['product_name']; ?>" required>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $stock_item['quantity']; ?>" min="1" required>

            <label for="expiration_date">Expiration Date</label>
            <input type="date" id="expiration_date" name="expiration_date" value="<?php echo $stock_item['expiration_date']; ?>">

            <button type="submit">Update Stock Item</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <a href="manage_stock.php" class="back-link">Back to Manage Stock</a>
    </div>
</body>
</html>
