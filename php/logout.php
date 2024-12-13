<?php
session_start();

// Connect to MySQL
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete the session token from the database
if (isset($_SESSION['session_token'])) {
    $stmt = $conn->prepare("DELETE FROM user_sessions WHERE session_token = ?");
    $stmt->bind_param("s", $_SESSION['session_token']);
    $stmt->execute();
    $stmt->close();
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to the login page
header("Location: ../html/login.html");
exit;
?>
