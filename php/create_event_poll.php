<?php
session_start();

// Check if session variables exist
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    error_log("Session variables not set. Debug info: " . print_r($_SESSION, true));
    header("Location: ../html/login.html");
    exit;
}

include 'db_connect.php'; // This file should create $conn connected to redbird_bookings database

// Validate session token in the database
$stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    error_log("Session validation failed for user ID: " . $_SESSION['user_id']);
    header("Location: ../html/login.html");
    exit;
}

$stmt->close();

// Variables for event display logic
$showPoll = false;
$eventId = null;
$eventData = null;
$optionsData = [];
$votesData = [];

// Handle voting (GET request)
if (isset($_GET['vote_option_id']) && isset($_GET['event_id'])) {
    $voteOptionId = intval($_GET['vote_option_id']);
    $eventId = intval($_GET['event_id']);
    // Increment vote count for the selected option
    $conn->query("UPDATE event_votes SET vote_count = vote_count + 1 WHERE option_id = $voteOptionId AND event_id = $eventId");
    $showPoll = true;
    // After voting, we do not redirect. We'll show the
}
