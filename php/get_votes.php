<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "event_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
if ($eventId <= 0) {
    echo json_encode([]);
    $conn->close();
    exit;
}

$sql = "SELECT eo.id AS option_id, eo.event_datetime, COUNT(v.id) AS vote_count
        FROM event_options eo
        LEFT JOIN votes v ON eo.id = v.event_option_id
        WHERE eo.event_id = $eventId
        GROUP BY eo.id";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
