<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session first!
session_start();

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "redbird_bookings"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in by verifying session token
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    // Not logged in, redirect to login
    header("Location: ../php/login_submit.php"); // Adjust the path as needed
    exit;
} else {
    // Validate session token in the database
    $user_id = intval($_SESSION['user_id']);
    $session_token = $_SESSION['session_token'];

    $stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
    $stmt->bind_param("is", $user_id, $session_token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // Invalid session token, redirect to login
        $stmt->close();
        header("Location: ../php/login_submit.php");
        exit;
    }
    $stmt->close();
}

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
}

// Handle form submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['event-title']);
    $description = $conn->real_escape_string($_POST['event-description']);
    $location = $conn->real_escape_string($_POST['event-location']);
    $maxParticipants = !empty($_POST['max-participants']) ? intval($_POST['max-participants']) : NULL;
    $visibility = isset($_POST['visibility']) && $_POST['visibility'] === 'private' ? 'private' : 'public';
    $privateEmails = $visibility === 'private' ? $conn->real_escape_string($_POST['private-emails']) : NULL;

    $datetimeOptions = $_POST['datetime-options'] ?? [];

    if (!empty($datetimeOptions)) {
        // Insert event
        $conn->query("INSERT INTO events (title, description, location, max_participants, visibility, private_emails, created_at) 
                      VALUES ('$title', '$description', '$location', " . ($maxParticipants === NULL ? "NULL" : $maxParticipants) . ", '$visibility', " . ($privateEmails === NULL ? "NULL" : "'$privateEmails'") . ", NOW())");
        $eventId = $conn->insert_id;

        // Insert date/time options
        foreach ($datetimeOptions as $option) {
            $conn->query("INSERT INTO event_options (event_id, option_datetime) VALUES ($eventId, '$option')");
            $optionId = $conn->insert_id;
            // Initialize votes
            $conn->query("INSERT INTO event_votes (event_id, option_id, vote_count) VALUES ($eventId, $optionId, 0)");
        }

        $showPoll = true;
    } else {
        echo "<script>alert('Please add at least one date/time option.');</script>";
        header("Location: ../html/event_poll.html");
        exit;
    }
}

// Fetch poll details
if ($showPoll && $eventId) {
    $result = $conn->query("SELECT * FROM events WHERE id = $eventId");
    if ($result && $result->num_rows > 0) {
        $eventData = $result->fetch_assoc();

        $optionsResult = $conn->query("SELECT o.id, o.option_datetime, v.vote_count 
                                       FROM event_options o 
                                       JOIN event_votes v ON o.id = v.option_id 
                                       WHERE o.event_id = $eventId");
        while ($row = $optionsResult->fetch_assoc()) {
            $optionsData[] = $row;
        }

        // Calculate total votes
        $totalVotes = 0;
        foreach ($optionsData as $opt) {
            $totalVotes += $opt['vote_count'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>RedBird Polling - Event Poll</title>
    <link rel="stylesheet" href="../assets/styles/event_poll.css">
</head>

<body>
    <header>
        <div class="container-fluid">
            <div class="logo">
                <img src="../assets/images/logo.png" alt="RedBird Polling Logo">
            </div>
            <nav>
                <a class="nav-link" href="#">Features</a>
                <a class="nav-link" href="#">About</a>
                <a class="nav-link" href="#">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="../php/logout.php">Log Out</a>
                <?php else: ?>
                    <a class="nav-link login-btn" href="../html/login.html">Log In</a>
                    <a class="nav-link signup-btn" href="../html/signup.html">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="event-hero-section">
        <h1>EVENT POLL</h1>
        <p>Review and vote on available times.</p>
    </section>

    <section class="poll-display-section" id="poll-display-section">
        <div class="poll-container">
            <div class="poll-details">
                <h2 id="poll-title"><?php echo htmlspecialchars($eventData['title'] ?? "No Event Found"); ?></h2>
                <p id="poll-description"><?php echo nl2br(htmlspecialchars($eventData['description'] ?? "")); ?></p>
                <p id="poll-location">Location: <?php echo htmlspecialchars($eventData['location'] ?? ""); ?></p>
                <p id="poll-creator">Created by: You</p>
            </div>

            <!-- Voting Options -->
            <h3>Vote for Your Preferred Date & Time</h3>
            <div class="voting-options" id="voting-options">
                <?php foreach ($optionsData as $opt):
                    $formatted = date('Y-m-d H:i', strtotime($opt['option_datetime']));
                ?>
                    <a href="?event_id=<?php echo $eventId; ?>&vote_option_id=<?php echo $opt['id']; ?>">
                        <button type="button"><?php echo $formatted; ?></button>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</body>

</html>