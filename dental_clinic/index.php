<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dental Clinic Management</title>
    <!-- Styles -->
    <link rel="stylesheet" href="../dental_clinic/assets/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
</head>
<body>
    <?php
    // Include database connection
    include 'includes/db_connect.php';
    ?>
    <!-- Header -->
    <header>
        <nav>
            <div class="logo">Dental Clinic</div>
            <ul class="nav-links">
                <li><a href="../dental_clinic/login.php" class="login-link"><i class="fas fa-sign-in-alt"></i>Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Dental Clinic Management</h1>
        <p>Efficiently manage your clinic with ease and precision.</p>
        <a href="../dental_clinic/login.php" class="cta-btn">Get Started</a>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>Our Key Features</h2>
        <div class="feature-cards">
            <div class="card">
                <i class="fas fa-boxes"></i>
                <h3>Inventory Management</h3>
                <p>Track stock levels, receive alerts, and manage supplies effortlessly.</p>
            </div>
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Smart Calendar</h3>
                <p>Schedule appointments by doctor or date with advanced filters.</p>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Role Management</h3>
                <p>Assign roles and permissions flexibly to your team.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Dental Clinic Management | All Rights Reserved</p>
    </footer>

    <!-- Scripts -->
    <script src="assets/scripts.js"></script>
</body>
</html>
