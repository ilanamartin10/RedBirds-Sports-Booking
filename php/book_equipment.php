<?php
session_start();
// Check if user is logged in (this is just an example)
// if (!isset($_SESSION['user_id'])) {
//     die("Please log in first.");
// }
$_SESSION['user_id'] = 1;

include 'db_connect.php';

// The item posted from the form
$item = $_POST['item'] ?? null;

// Validate input
if (!$item) {
    die("No item specified.");
}

// Example logic: Insert a booking record
// You need a table (e.g., 'bookings') with fields like user_id, item, booking_time, etc.
// We'll assume a 'bookings' table: (booking_id, user_id, item_name, booking_time)
$user_id = $_SESSION['user_id'];

// Insert a booking (example: booking_time = NOW())
$sql = "INSERT INTO bookings (user_id, item_name, booking_time, status) VALUES (?, ?, NOW(), 'pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $user_id, $item);
if ($stmt->execute()) {
    echo "Booking successful!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
