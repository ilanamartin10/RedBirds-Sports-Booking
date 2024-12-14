<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$username = "root";
$password = "";
$dbname = "event_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
if ($eventId <= 0) {
    echo json_encode(["error" => "Invalid event ID"]);
    $conn->close();
    exit;
}

$eventSql = "SELECT * FROM events WHERE id = $eventId";
$eventResult = $conn->query($eventSql);
if ($eventResult->num_rows === 0) {
    echo json_encode(["error" => "No event found"]);
    $conn->close();
    exit;
}
$eventData = $eventResult->fetch_assoc();

$optionsSql = "SELECT * FROM event_options WHERE event_id = $eventId";
$optionsResult = $conn->query($optionsSql);
$options = [];
while ($row = $optionsResult->fetch_assoc()) {
    $options[] = $row;
}

echo json_encode([
    "event" => $eventData,
    "options" => $options
]);

$conn->close();
