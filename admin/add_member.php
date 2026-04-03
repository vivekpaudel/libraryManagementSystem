<?php
// admin/add_member.php
include '../includes/auth_check.php';
include '../config/database.php';
include '../includes/functions.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $status = $_POST['status'];

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO members (name, email, phone, address, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $address, $status);
        
        if ($stmt->execute()) {
            $success = "Member registered successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Name and Email are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Member - LMS</title>
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
        <h1>Register New Member</h1>
        
        <?php if($success): ?>
            <p style="color:green;"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="" style="background:white; padding:20px; max-width:500px; box-shadow:0 0 5px rgba(0,0,0,0.1);">
            <div style="margin-bottom:15px;">
                <label>Full Name</label>
                <input type="text" name="name" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Email Address</label>
                <input type="email" name="email" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Phone Number</label>
                <input type="text" name="phone" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Address</label>
                <textarea name="address" style="width:100%; padding:8px;"></textarea>
            </div>
            <div style="margin-bottom:15px;">
                <label>Status</label>
                <select name="status" style="width:100%; padding:8px;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Register Member</button>
            <a href="members.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</body>
</html>