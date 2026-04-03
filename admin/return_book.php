<?php
// admin/return_book.php
include '../includes/auth_check.php';
include '../config/database.php';
include '../includes/functions.php';

$success = "";
$error = "";

// Handle Return Action
if (isset($_GET['return_id'])) {
    $trans_id = (int)$_GET['return_id'];
    $return_date = date('Y-m-d');
    $fine_per_day = 10; // NPR 10 per day

    // Get Transaction Details
    $stmt = $conn->prepare("SELECT book_id, due_date FROM transactions WHERE trans_id = ? AND status='issued'");
    $stmt->bind_param("i", $trans_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $book_id = $row['book_id'];
        $due_date = $row['due_date'];

        // Calculate Fine
        $diff = strtotime($return_date) - strtotime($due_date);
        $days = floor($diff / (60 * 60 * 24));
        $fine = ($days > 0) ? $days * $fine_per_day : 0;

        // Update Transaction
        $stmt = $conn->prepare("UPDATE transactions SET return_date = ?, fine_amount = ?, status = 'returned' WHERE trans_id = ?");
        $stmt->bind_param("sdi", $return_date, $fine, $trans_id);
        
        if ($stmt->execute()) {
            // Update Book Availability
            $conn->query("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = $book_id");
            $success = "Book returned successfully. Fine: NPR $fine";
        } else {
            $error = "Error processing return.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <navbar>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="issue_book.php">Issue Book</a>
        <a href="return_book.php">Return Book</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php" style="float:right;">Logout</a>
    </navbar>

    <div class="container">
        <h1>Return Book</h1>
        
        <?php if($success): ?>
            <p style="color:green; background:#dff0d8; padding:10px;"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if($error): ?>
            <p style="color:red; background:#f2dede; padding:10px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Trans ID</th>
                    <th>Member</th>
                    <th>Book Title</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT t.trans_id, m.name, b.title, t.issue_date, t.due_date 
                        FROM transactions t 
                        JOIN members m ON t.member_id = m.member_id 
                        JOIN books b ON t.book_id = b.book_id 
                        WHERE t.status = 'issued'";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['trans_id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['issue_date'] . "</td>";
                        echo "<td>" . $row['due_date'] . "</td>";
                        echo "<td><a href='return_book.php?return_id=" . $row['trans_id'] . "' class='btn btn-success' onclick='return confirm(\"Process Return?\")'>Return</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No active issues found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>