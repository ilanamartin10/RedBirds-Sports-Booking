<?php
session_start();

// Connect to MySQL
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate session token
if (!isset($_SESSION['session_token'])) {
    header("Location: ../html/login.html");
    exit;
}

$session_token = $_SESSION['session_token'];
$stmt = $conn->prepare("SELECT user_id FROM user_sessions WHERE session_token = ?");
$stmt->bind_param("s", $session_token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    header("Location: ../html/login.html");
    exit;
}

$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Basic validation
    if (empty($title) || empty($description)) {
        echo "Both title and description are required!";
        exit;
    }

    // Insert new post into the database
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, description, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $title, $description);

    if ($stmt->execute()) {
        header("Location: ../html/find_a_partner.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
