<?php
// admin/issue_book.php
include '../includes/auth_check.php';
include '../config/database.php';
include '../includes/functions.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = (int)$_POST['member_id'];
    $book_id = (int)$_POST['book_id'];
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days')); // 14 days loan period

    // Check Book Availability
    $stmt = $conn->prepare("SELECT available_copies FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();

    if ($book['available_copies'] > 0) {
        // Insert Transaction
        $stmt = $conn->prepare("INSERT INTO transactions (member_id, book_id, issue_date, due_date, status) VALUES (?, ?, ?, ?, 'issued')");
        $stmt->bind_param("iiss", $member_id, $book_id, $issue_date, $due_date);
        
        if ($stmt->execute()) {
            // Update Book Availability
            $conn->query("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = $book_id");
            $success = "Book issued successfully! Due Date: $due_date";
        } else {
            $error = "Error issuing book: " . $conn->error;
        }
    } else {
        $error = "Book not available (0 copies left).";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book - LMS</title>
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
        <h1>Issue Book</h1>
        
        <?php if($success): ?>
            <p style="color:green; background:#dff0d8; padding:10px;"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if($error): ?>
            <p style="color:red; background:#f2dede; padding:10px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="" style="background:white; padding:20px; max-width:500px; box-shadow:0 0 5px rgba(0,0,0,0.1);">
            <div style="margin-bottom:15px;">
                <label>Select Member</label>
                <select name="member_id" required style="width:100%; padding:8px;">
                    <option value="">-- Choose Member --</option>
                    <?php
                    $members = $conn->query("SELECT member_id, name FROM members WHERE status='active'");
                    while($m = $members->fetch_assoc()) {
                        echo "<option value='".$m['member_id']."'>".$m['name']." (ID: ".$m['member_id'].")</option>";
                    }
                    ?>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label>Select Book</label>
                <select name="book_id" required style="width:100%; padding:8px;">
                    <option value="">-- Choose Book --</option>
                    <?php
                    $books = $conn->query("SELECT book_id, title, available_copies FROM books WHERE available_copies > 0");
                    while($b = $books->fetch_assoc()) {
                        echo "<option value='".$b['book_id']."'>".$b['title']." (Avail: ".$b['available_copies'].")</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Issue Book</button>
            <a href="dashboard.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</body>
</html>