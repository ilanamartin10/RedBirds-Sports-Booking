<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user conversations
$stmt = $conn->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN sender_id = ? THEN receiver_id 
            ELSE sender_id 
        END AS other_user_id,
        (SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = 
            CASE 
                WHEN sender_id = ? THEN receiver_id 
                ELSE sender_id 
            END) AS other_user_name,
        (SELECT message FROM messages WHERE 
            (sender_id = ? AND receiver_id = other_user_id) OR 
            (sender_id = other_user_id AND receiver_id = ?) 
            ORDER BY timestamp DESC LIMIT 1) AS last_message
    FROM messages
    WHERE sender_id = ? OR receiver_id = ?
");
$stmt->bind_param("iiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$conversations_result = $stmt->get_result();
$conversations = $conversations_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Messages</title>
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
    <style>
        body {
            background-color: #fff; 
            font-family: 'Open Sans', sans-serif;
            margin: 0;
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

        .navbar nav {
            display: flex;
            gap: 1rem;
        }

        .navbar nav a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            letter-spacing: 0.05rem;
        }

        .navbar nav a:hover {
            background-color: #ec1b2e;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border: 2px solid black;
            border-radius: 10px;
        }

        h1 {
            font-family: Anton;
            color: black;
            margin-bottom: 1rem;
        }

        .conversation-list {
            list-style: none;
            padding: 0;
        }

        .conversation-list li {
            margin: 1rem 0;
            padding: 1rem;
            border: 1px solid black;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .conversation-list a {
            text-decoration: none;
            color: #ec1b2e;
            font-family: 'Open Sans', sans-serif;
            font-size: 1rem;
            font-weight: bold;
            display: block;
        }

        .conversation-list a:hover {
            color: black;
        }

        .last-message {
            font-style: italic;
            color: #555;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
          <img src="../assets/images/logo.png" alt="Redbird Bookings Logo" class="logo" width="180" height="140">
          <nav>
            <a href="../html/equipment_bookings.php">BOOK EQUIPMENT</a>
            <a href="../html/court_bookings.php">BOOK A COURT</a>
            <a href="http://localhost/redbird_bookings/php/profile.php?user_id=<?= htmlspecialchars($user_id) ?>" class="btn">MY PROFILE</a>
            <a href="logout.php" class="btn">LOG OUT</a>
          </nav>
        </div>

    <div class="container">
        <h1>YOUR MESSAGES</h1>
        <ul class="conversation-list">
            <?php if (!empty($conversations)): ?>
                <?php foreach ($conversations as $conversation): ?>
                    <li>
                        <a href="message_user.php?user_id=<?= htmlspecialchars($conversation['other_user_id']) ?>">
                            Chat with <?= htmlspecialchars($conversation['other_user_name']) ?>
                        </a>
                        <p class="last-message"><?= htmlspecialchars($conversation['last_message'] ?? 'No messages yet.') ?></p>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no conversations yet.</p>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
