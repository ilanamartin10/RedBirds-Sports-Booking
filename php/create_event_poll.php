<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login_submit.php");
    exit;
}

// Fetch logged-in user's email
$userId = intval($_SESSION['user_id']);
$userQuery = $conn->prepare("SELECT email FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    die("User not found.");
}
$userEmail = $userResult->fetch_assoc()['email'];
$userQuery->close();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form inputs
    $title = $conn->real_escape_string($_POST['event-title']);
    $description = $conn->real_escape_string($_POST['event-description']);
    $location = $conn->real_escape_string($_POST['event-location']);
    $datetimeOptions = $_POST['datetime-options'] ?? [];

    if (empty($datetimeOptions)) {
        die("Please add at least one date and time option.");
    }

    // Generate a unique token for the event
    $eventToken = bin2hex(random_bytes(16));

    // Insert the event into the database
    $stmt = $conn->prepare("INSERT INTO events (title, description, location, visibility, created_at, event_token) 
                            VALUES (?, ?, ?, 'private', NOW(), ?)");
    $stmt->bind_param("ssss", $title, $description, $location, $eventToken);
    $stmt->execute();

    $eventId = $stmt->insert_id;
    $stmt->close();

    // Insert date/time options
    $optionStmt = $conn->prepare("INSERT INTO event_options (event_id, option_datetime, vote_count) VALUES (?, ?, 0)");
    foreach ($datetimeOptions as $option) {
        $optionStmt->bind_param("is", $eventId, $option);
        $optionStmt->execute();
    }
    $optionStmt->close();

    // Generate the voting link
    // Generate the voting link for hosted server
    $baseUrl = "http://marc.infinityfreeapp.com"; // Use your hosted base URL
    $eventUrl = $baseUrl . "/php/vote_event.php?token=" . urlencode($eventToken);

    // Display the success message page
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Event Poll Created</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Anton" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <style>
            body {
                font-family: 'Open Sans', Arial, sans-serif;
                background-color: #f4f4f4;
            }
            .navbar {
                font-family: Anton;
                background-color: #000;
                color: #fff;
                padding: 0.5rem 1rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .navbar nav a {
                color: #fff;
                text-decoration: none;
                font-family: Anton;
                padding: 0.5rem 1rem;
            }
            .main-container {
                background-color: #ec1b2e;
                color: #fff;
                text-align: center;
                padding: 2rem;
                font-family: Anton;
            }
            .success-container {
                background-color: #fff;
                padding: 2rem;
                border-radius: 8px;
                max-width: 600px;
                margin: 2rem auto;
                text-align: center;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }
            .copy-btn {
                background-color: #ec1b2e;
                color: #fff;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 5px;
                font-family: Anton;
                cursor: pointer;
            }
            .copy-btn:hover {
                background-color: #cc0000;
            }
            a {
                color: #ec1b2e;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <div class="navbar">
        <a href="../index.html" class="button">
               <img src="../assets/images/logo.png" alt="Redbird Bookings Logo" class="logo" width="180" height="140">
            </a>
            <nav>
                <a href="../php/logout.php">LOG OUT</a>
            </nav>
        </div>

        <!-- Hero Section -->
        <div class="main-container">
            <h1>EVENT POLL CREATED</h1>
            <p>Share the link below with participants to vote</p>
        </div>

        <!-- Success Message -->
        <div class="success-container">
            <h2>Event Poll Created Successfully!</h2>
            <p>Share the link below with participants to vote:</p>
            <div class="alert alert-light">
                <a href="$eventUrl" target="_blank">$eventUrl</a>
            </div>
            <button class="copy-btn" onclick="copyToClipboard('$eventUrl')">Copy Link</button>
            <br><br>
            <a href="../html/event_poll.html" class="btn btn-secondary">Create Another Poll</a>
        </div>

        <script>
            function copyToClipboard(link) {
                navigator.clipboard.writeText(link).then(() => {
                    alert("Link copied to clipboard!");
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            }
        </script>
    </body>
    </html>
HTML;
    exit;
}

$conn->close();
