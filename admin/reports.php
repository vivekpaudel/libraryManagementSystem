<?php
// admin/reports.php
include '../includes/auth_check.php';
include '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Library Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
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
        <h1>Library Reports</h1>

        <!-- Statistics Section -->
        <div class="dashboard-cards">
            <div class="card card-blue">
                <h3>Total Books</h3>
                <p><?php echo $conn->query("SELECT COUNT(*) as c FROM books")->fetch_assoc()['c']; ?></p>
            </div>
            <div class="card card-green">
                <h3>Total Members</h3>
                <p><?php echo $conn->query("SELECT COUNT(*) as c FROM members")->fetch_assoc()['c']; ?></p>
            </div>
            <div class="card card-red">
                <h3>Overdue Books</h3>
                <p><?php 
                $today = date('Y-m-d');
                echo $conn->query("SELECT COUNT(*) as c FROM transactions WHERE due_date < '$today' AND status='issued'")->fetch_assoc()['c']; 
                ?></p>
            </div>
        </div>

        <!-- Overdue Books Table -->
        <h2>Overdue Books List</h2>
        <table>
            <thead>
                <tr>
                    <th>Trans ID</th>
                    <th>Member</th>
                    <th>Book Title</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                    <th>Est. Fine (NPR)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $today = date('Y-m-d');
                $sql = "SELECT t.trans_id, m.name, b.title, t.due_date 
                        FROM transactions t 
                        JOIN members m ON t.member_id = m.member_id 
                        JOIN books b ON t.book_id = b.book_id 
                        WHERE t.due_date < '$today' AND t.status = 'issued'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $diff = strtotime($today) - strtotime($row['due_date']);
                        $days = floor($diff / (60 * 60 * 24));
                        $fine = $days * 10; // NPR 10 per day
                        echo "<tr>";
                        echo "<td>" . $row['trans_id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td style='color:#dc3545; font-weight:bold;'>" . $row['due_date'] . "</td>";
                        echo "<td><span class='badge badge-danger'>" . $days . " Days</span></td>";
                        echo "<td style='color:#dc3545; font-weight:bold;'>NPR " . $fine . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:30px;'><div class='alert alert-success'>No overdue books found. Great job!</div></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Library Management System. All Rights Reserved.
    </footer>
</body>
</html>