<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$favorite_sports = $_POST['favorite_sports'];
$major = $_POST['major'];
$minor = $_POST['minor'];
$about = $_POST['about'];

// Check if the profile exists
$stmt = $conn->prepare("SELECT id FROM profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update existing profile
    $stmt->close();
    $stmt = $conn->prepare("UPDATE profiles SET favorite_sports = ?, major = ?, minor = ?, about = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $favorite_sports, $major, $minor, $about, $user_id);
} else {
    // Create a new profile
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO profiles (user_id, favorite_sports, major, minor, about) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $favorite_sports, $major, $minor, $about);
}

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
