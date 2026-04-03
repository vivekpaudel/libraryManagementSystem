<?php
// admin/books.php
include '../includes/auth_check.php';
include '../config/database.php';
include '../includes/functions.php';

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        setMessage('success', 'Book deleted successfully!');
    } else {
        setMessage('danger', 'Error deleting book.');
    }
    redirect('books.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books - LMS</title>
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
            <h1>Manage Books</h1>
            <a href="add_book.php" class="btn btn-success">+ Add New Book</a>
        </div>

        <!-- Message Display -->
        <?php if(isset($_SESSION['message'])): ?>
            <p style="color: <?php echo $_SESSION['msg_type'] == 'success' ? 'green' : 'red'; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </p>
        <?php endif; ?>

        <!-- Books Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Category</th>
                    <th>Available</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM books ORDER BY book_id DESC");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['book_id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['author'] . "</td>";
                    echo "<td>" . $row['isbn'] . "</td>";
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>" . $row['available_copies'] . "/" . $row['total_copies'] . "</td>";
                    echo "<td><a href='books.php?delete=" . $row['book_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>