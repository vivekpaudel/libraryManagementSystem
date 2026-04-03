<?php
// admin/members.php
include '../includes/auth_check.php';
include '../config/database.php';
include '../includes/functions.php';

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        setMessage('success', 'Member removed successfully!');
    } else {
        setMessage('danger', 'Error removing member.');
    }
    redirect('members.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <navbar>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="issue_book.php">Issue Book</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php" style="float:right;">Logout</a>
    </navbar>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Manage Members</h1>
            <a href="add_member.php" class="btn btn-success">+ Register Member</a>
        </div>

        <!-- Message Display -->
        <?php if(isset($_SESSION['message'])): ?>
            <p style="color: <?php echo $_SESSION['msg_type'] == 'success' ? 'green' : 'red'; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </p>
        <?php endif; ?>

        <!-- Members Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM members ORDER BY member_id DESC");
                while ($row = $result->fetch_assoc()) {
                    $statusColor = $row['status'] == 'active' ? 'green' : 'red';
                    echo "<tr>";
                    echo "<td>" . $row['member_id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['phone'] . "</td>";
                    echo "<td>" . $row['join_date'] . "</td>";
                    echo "<td style='color:$statusColor; font-weight:bold;'>" . $row['status'] . "</td>";
                    echo "<td><a href='members.php?delete=" . $row['member_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Remove</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>