<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['session_token']);
echo json_encode(['isLoggedIn' => $isLoggedIn]);
?>
