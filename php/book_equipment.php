<?php
session_start();

// Check if session variables exist
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    error_log("Session variables not set. Debug info: " . print_r($_SESSION, true));
    header("Location: ../html/login.html");
    exit;
}

include 'db_connect.php';

// Validate session token in the database
$stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    error_log("Session validation failed for user ID: " . $_SESSION['user_id']);
    header("Location: ../html/login.html");
    exit;
}

$stmt->close();

// The item posted from the form
$item = $_POST['item'] ?? null;

// Validate input
if (!$item) {
    die("No item specified.");
}

// Validate the item against a predefined list
$allowed_items = ["Basketballs", "Foosball balls", "Tennis balls", "Volleyballs"];
if (!in_array($item, $allowed_items)) {
    die("Invalid item specified.");
}

// Insert a booking
$user_id = $_SESSION['user_id'];
$sql = "INSERT INTO bookings (user_id, item_name, booking_time, status) VALUES (?, ?, NOW(), 'pending')";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param('is', $user_id, $item);
if ($stmt->execute()) {
    echo "Booking successful!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
