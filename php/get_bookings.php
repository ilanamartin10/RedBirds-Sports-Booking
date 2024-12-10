<?php
$host = "localhost";
$user = "root";
$password = ""; // Default password for XAMPP
$dbname = "your_database_name"; // Replace with your DB name

// Connect to the database
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all bookings
$sql = "SELECT event_name, event_date, event_time FROM bookings";
$result = $conn->query($sql);

$bookings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            "title" => $row['event_name'],
            "start" => $row['event_date'] . "T" . $row['event_time']
        ];
    }
}

echo json_encode($bookings);
$conn->close();
?>
