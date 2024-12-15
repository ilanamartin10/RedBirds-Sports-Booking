<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

try {
    // Fetch bookings from both tables
    $query = "
        SELECT 
            id AS booking_id, 
            court_name AS title, 
            booking_start AS start, 
            DATE_ADD(booking_start, INTERVAL duration MINUTE) AS end,
            status
        FROM court_bookings
        WHERE user_id = ?
        UNION ALL
        SELECT 
            booking_id, 
            item_name AS title, 
            booking_time AS start, 
            DATE_ADD(booking_time, INTERVAL 10 DAY) AS end,
            status
        FROM bookings
        WHERE user_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['booking_id'],
            'title' => $row['title'],
            'start' => $row['start'],
            'end' => $row['end'],
        ];
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($events);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to fetch bookings']);
    exit;
}
?>
