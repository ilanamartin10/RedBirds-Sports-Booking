<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "";
$dbname = "event_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventOptionId = isset($_POST['event_option_id']) ? (int)$_POST['event_option_id'] : 0;
$user = "anonymous"; // Placeholder for user identification

if ($eventOptionId > 0) {
    $stmt = $conn->prepare("INSERT INTO votes (event_option_id, user_identifier) VALUES (?, ?)");
    $stmt->bind_param("is", $eventOptionId, $user);
    if ($stmt->execute()) {
        echo "Vote recorded successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Invalid event option.";
}

$conn->close();
