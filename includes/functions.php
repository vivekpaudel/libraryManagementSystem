<?php
// includes/functions.php

function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($location) {
    header("Location: $location");
    exit();
}

function setMessage($type, $message) {
    $_SESSION['message'] = $message;
    $_SESSION['msg_type'] = $type;
}
?>