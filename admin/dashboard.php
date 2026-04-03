<?php
// admin/dashboard.php
include '../includes/auth_check.php';
include '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Library Management System</title>
    <!-- Ensure this path is correct (goes one level up to find assets) -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Updated Navbar -->
    <navbar>
        <a href="dashboard.php" class="logo">📚 LMS</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="books.php">Books</a>
            <a href="members.php">Members</a>
            <a href="issue_book.php">Issue</a>
            <a href="return_book.php">Return</a>
            <a href="reports.php">Reports</a>
        </div>
        <a href="../logout.php" class="logout">Logout</a>
    </navbar>

    <div class="container">
        <!-- Welcome Section -->
        <div class="page-header">
            <h1>Welcome, <?php echo $_SESSION['admin_name']; ?></h1>
            <span style="color:#666; font-size:14px;">Last Login: <?php echo date('Y-m-d H:i'); ?></span>
        </div>

        <!-- Updated Dashboard Cards -->
        <div class="dashboard-cards">
            <!-- Total Books -->
            <div class="card card-blue">
                <h3>Total Books</h3>
                <p>
                    <?php 
                    $result = $conn->query("SELECT COUNT(*) as count FROM books");
                    echo $result->fetch_assoc()['count']; 
                    ?>
                </p>
            </div>

            <!-- Total Members -->
            <div class="card card-green">
                <h3>Total Members</h3>
                <p>
                    <?php 
                    $result = $conn->query("SELECT COUNT(*) as count FROM members");
                    echo $result->fetch_assoc()['count']; 
                    ?>
                </p>
            </div>

            <!-- Active Issues -->
            <div class="card card-orange">
                <h3>Active Issues</h3>
                <p>
                    <?php 
                    $result = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE status='issued'");
                    echo $result->fetch_assoc()['count']; 
                    ?>
                </p>
            </div>

            <!-- Overdue Books -->
            <div class="card card-red">
                <h3>Overdue Books</h3>
                <p>
                    <?php 
                    $today = date('Y-m-d');
                    $result = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE due_date < '$today' AND status='issued'");
                    echo $result->fetch_assoc()['count']; 
                    ?>
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2>Quick Actions</h2>
        <div style="margin-top:20px;">
            <a href="issue_book.php" class="btn btn-primary">Issue New Book</a>
            <a href="return_book.php" class="btn btn-success">Process Return</a>
            <a href="books.php" class="btn btn-info">Manage Books</a>
            <a href="reports.php" class="btn btn-secondary">View Reports</a>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Library Management System. All Rights Reserved.
    </footer>
</body>
</html>