<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../includes/db_connect.php';

$message = "";

// Handle Add Stock Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stock'])) {
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $quantity = intval($_POST['quantity']);
    $expiration_date = htmlspecialchars(trim($_POST['expiration_date']));

    if (!empty($product_name) && $quantity > 0) {
        $query = $conn->prepare("INSERT INTO stock (product_name, quantity, expiration_date) VALUES (?, ?, ?)");
        $query->bind_param("sis", $product_name, $quantity, $expiration_date);
        if ($query->execute()) {
            $message = "Stock item added successfully!";
        } else {
            $message = "Error adding stock item.";
        }
    } else {
        $message = "Product name and quantity are required.";
    }
}

// Handle Delete Stock Item
if (isset($_GET['delete'])) {
    $stock_id = intval($_GET['delete']);
    $query = $conn->prepare("DELETE FROM stock WHERE stock_id = ?");
    $query->bind_param("i", $stock_id);
    if ($query->execute()) {
        $message = "Stock item deleted successfully!";
    } else {
        $message = "Error deleting stock item.";
    }
}

// Fetch Stock Items
$query = $conn->query("SELECT * FROM stock");
$stock_items = $query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock</title>
    <link rel="stylesheet" href="../assets/manage_stock.css">
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
                <li><a href="manage_stock.php" class="active">Manage Stock</a></li>
                <li><a href="manage_prosthetics.php">Manage Prosthetics</a></li>
                <li><a href="../pages/logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Manage Stock</h1>
            
            <!-- Add Stock Form -->
            <form method="POST" action="" class="form-container">
                <h2>Add Stock Item</h2>
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" required>
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
                <label for="expiration_date">Expiration Date</label>
                <input type="date" id="expiration_date" name="expiration_date">
                <button type="submit" name="add_stock">Add Stock</button>
            </form>
            
            <p class="message"><?php echo $message; ?></p>
            
            <!-- Stock Table -->
            <h2>Stock Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Expiration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stock_items as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $item['expiration_date']; ?></td>
                        <td>
                            <a href="edit_stock.php?id=<?php echo $item['stock_id']; ?>">Edit</a> |
                            <a href="?delete=<?php echo $item['stock_id']; ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
