<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    echo "Unauthorized access. Please log in.";
    exit;
}

// Validate session token
$stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo "Session validation failed. Please log in again.";
    exit;
}
$stmt->close();

// Retrieve and sanitize POST data
$court = filter_input(INPUT_POST, 'court', FILTER_SANITIZE_STRING);
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
$startTime = filter_input(INPUT_POST, 'startTime', FILTER_SANITIZE_STRING);
$duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);

if (!$court || !$date || !$startTime || !$duration) {
    echo "All fields are required.";
    exit;
}

$allowed_courts = ["Tennis", "Squash"];
if (!in_array($court, $allowed_courts)) {
    echo "Invalid court type.";
    exit;
}

// Convert date and time into a valid datetime format
$startTimestamp = strtotime("$date $startTime");
if (!$startTimestamp) {
    echo "Invalid date or time.";
    exit;
}
$bookingStart = date("Y-m-d H:i:s", $startTimestamp);

// Insert booking into the database
$sql = "INSERT INTO court_bookings (user_id, court_name, booking_start, duration, booking_time, status) 
        VALUES (?, ?, ?, ?, NOW(), 'pending')";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Database error: " . $conn->error;
    exit;
}
$stmt->bind_param("issi", $_SESSION['user_id'], $court, $bookingStart, $duration);
if ($stmt->execute()) {
    echo "Court booking successful!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
