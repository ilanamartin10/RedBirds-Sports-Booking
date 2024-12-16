<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "redbird_bookings");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Event Token
$eventToken = $_GET['token'] ?? null;
if (!$eventToken) {
    die("Invalid event token.");
}

// Fetch Event Details
$eventQuery = $conn->query("SELECT id, title, description, location FROM events WHERE event_token = '$eventToken'");
if ($eventQuery->num_rows === 0) {
    die("Event not found.");
}
$eventData = $eventQuery->fetch_assoc();

$voteSuccess = false;

// Handle Voting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $optionId = intval($_POST['option_id']);

    // Increment vote count for the selected option
    $updateVote = $conn->query("UPDATE event_options SET vote_count = vote_count + 1 WHERE id = $optionId");

    if ($updateVote) {
        $voteSuccess = true;
    }
}

// Re-fetch Event Options to show updated vote counts
$optionsQuery = $conn->query("SELECT id, option_datetime, vote_count FROM event_options WHERE event_id = " . $eventData['id']);
$optionsData = [];
while ($row = $optionsQuery->fetch_assoc()) {
    $optionsData[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vote on Event Poll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
    <style>
        /* Global Styling */
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
        }

        .main-container {
            background-color: #ec1b2e;
            color: #fff;
            text-align: center;
            padding: 2rem;
            font-family: Anton;
        }

        .poll-container {
            background-color: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            max-width: 600px;
            margin: 2rem auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #ec1b2e;
            text-align: center;
            font-family: Anton;
        }

        button {
            background-color: #ec1b2e;
            color: #fff;
            border: none;
            padding: 0.75rem;
            margin: 0.5rem 0;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
        }

        button:hover {
            background-color: #cc0000;
        }

        .success-message {
            text-align: center;
            color: #28a745;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href=“../index.html” class="button">
            <img src="../assets/images/logo.png" alt="Redbird Bookings Logo" class="logo" width="180" height="140">
        </a>
    </div>

    <!-- Event Details -->
    <div class="main-container">
        <h1><?php echo htmlspecialchars($eventData['title']); ?></h1>
        <p><?php echo htmlspecialchars($eventData['description']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($eventData['location']); ?></p>
    </div>

    <!-- Voting Section -->
    <div class="poll-container">
        <h2>Vote for Your Preferred Time</h2>
        <?php if ($voteSuccess): ?>
            <div class="success-message">
                Vote submitted successfully!
            </div>
        <?php endif; ?>
        <form method="POST">
            <?php foreach ($optionsData as $option): ?>
                <div>
                    <button type="submit" name="option_id" value="<?php echo $option['id']; ?>">
                        <?php echo date('Y-m-d H:i', strtotime($option['option_datetime'])); ?>
                        (Votes: <?php echo $option['vote_count']; ?>)
                    </button>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</body>

</html>