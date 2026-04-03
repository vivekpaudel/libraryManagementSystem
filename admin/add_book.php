<?php
// admin/add_book.php
include '../includes/auth_check.php';
include '../config/database.php';
include '../includes/functions.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $isbn = sanitize($_POST['isbn']);
    $category = sanitize($_POST['category']);
    $copies = (int)$_POST['copies'];

    if (!empty($title) && !empty($author)) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, category, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $title, $author, $isbn, $category, $copies, $copies);
        
        if ($stmt->execute()) {
            $success = "Book added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Title and Author are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <navbar>
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
        <a href="issue_book.php">Issue Book</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php" style="float:right;">Logout</a>
    </navbar>

    <div class="container">
        <h1>Add New Book</h1>
        
        <?php if($success): ?>
            <p style="color:green;"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="" style="background:white; padding:20px; max-width:500px; box-shadow:0 0 5px rgba(0,0,0,0.1);">
            <div style="margin-bottom:15px;">
                <label>Book Title</label>
                <input type="text" name="title" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Author</label>
                <input type="text" name="author" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>ISBN</label>
                <input type="text" name="isbn" style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Category</label>
                <select name="category" style="width:100%; padding:8px;">
                    <option value="Fiction">Fiction</option>
                    <option value="Science">Science</option>
                    <option value="Technology">Technology</option>
                    <option value="History">History</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label>Total Copies</label>
                <input type="number" name="copies" value="1" min="1" required style="width:100%; padding:8px;">
            </div>
            <button type="submit" class="btn btn-primary">Save Book</button>
            <a href="books.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</body>
</html>