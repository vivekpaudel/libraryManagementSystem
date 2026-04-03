<?php
// index.php
session_start();
include 'config/database.php';
include 'includes/functions.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in'])) {
    redirect('admin/dashboard.php');
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    // Prepare Statement to prevent SQL Injection
    $stmt = $conn->prepare("SELECT admin_id, password, full_name FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify Password (bcrypt)
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['full_name'];
            redirect('admin/dashboard.php');
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Invalid Username!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <h2 style="text-align:center;">LMS Login</h2>
        <?php if($error): ?>
            <p style="color:red; text-align:center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div style="margin-bottom:15px;">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required style="width:100%; padding:8px;">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px; font-size:12px;">
            Default: admin / admin123
        </p>
    </div>
</body>
</html>