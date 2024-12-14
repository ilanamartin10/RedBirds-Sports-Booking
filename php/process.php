<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_db"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "DB connection failed: " . $conn->connect_error]));
}

// Voting
if (isset($_GET['vote_option_id']) && isset($_GET['event_id'])) {
    $voteOptionId = intval($_GET['vote_option_id']);
    $eventId = intval($_GET['event_id']);

    // Increment vote count
    $conn->query("UPDATE event_votes SET vote_count = vote_count + 1 WHERE option_id = $voteOptionId AND event_id = $eventId");

    // Return updated poll data as JSON
    echo json_encode(["success" => true]);
    exit;
}

// Load Poll Data
if (isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);
    $eventRes = $conn->query("SELECT * FROM events WHERE id = $eventId");
    if ($eventRes && $eventRes->num_rows > 0) {
        $eventData = $eventRes->fetch_assoc();

        $optionsRes = $conn->query("SELECT o.id, o.option_datetime, v.vote_count
                                    FROM event_options o
                                    JOIN event_votes v ON o.id = v.option_id
                                    WHERE o.event_id = $eventId");

        $options = [];
        while ($row = $optionsRes->fetch_assoc()) {
            $options[] = $row;
        }

        echo json_encode([
            "success" => true,
            "poll" => [
                "title" => $eventData["title"],
                "description" => $eventData["description"],
                "location" => $eventData["location"],
                "options" => $options
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Event not found."]);
    }
    exit;
}

// Handle Event Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['event-title']);
    $description = $conn->real_escape_string($_POST['event-description']);
    $location = $conn->real_escape_string($_POST['event-location']);
    $maxParticipants = !empty($_POST['max-participants']) ? intval($_POST['max-participants']) : NULL;
    $visibility = isset($_POST['visibility']) && $_POST['visibility'] === 'private' ? 'private' : 'public';
    $privateEmails = ($visibility === 'private' && !empty($_POST['private-emails'])) ? $conn->real_escape_string($_POST['private-emails']) : NULL;

    $datetimeOptions = $_POST['datetime-options'] ?? [];
    if (empty($datetimeOptions)) {
        // No datetime options, redirect back with error or handle gracefully
        // For simplicity, just show error:
        die("Please add at least one date/time option.");
    }

    // Insert event
    $conn->query("INSERT INTO events (title, description, location, max_participants, visibility, private_emails, created_at) 
                  VALUES ('$title', '$description', '$location', " . ($maxParticipants === NULL ? "NULL" : $maxParticipants) . ", '$visibility', " . ($privateEmails === NULL ? "NULL" : "'$privateEmails'") . ", NOW())");
    $eventId = $conn->insert_id;

    // Insert options
    foreach ($datetimeOptions as $option) {
        $conn->query("INSERT INTO event_options (event_id, option_datetime) VALUES ($eventId, '$option')");
        $optionId = $conn->insert_id;
        $conn->query("INSERT INTO event_votes (event_id, option_id, vote_count) VALUES ($eventId, $optionId, 0)");
    }

    // Redirect to index.html with event_id in URL
    header("Location: index.html?event_id=$eventId");
    exit;
}

echo json_encode(["success" => false, "error" => "No valid request."]);
