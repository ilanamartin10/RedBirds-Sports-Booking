<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['user_id'];

// Fetch receiver details
$stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$stmt->bind_result($receiver_first_name, $receiver_last_name);
if (!$stmt->fetch()) {
    die("User not found.");
}
$stmt->close();

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $receiver_id, $message);
    $stmt->execute();
    $stmt->close();
}

// Fetch conversation
$stmt = $conn->prepare("
    SELECT m.sender_id, m.message, m.timestamp, u.first_name
    FROM messages m
    INNER JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.timestamp
");
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message <?= htmlspecialchars($receiver_first_name . ' ' . $receiver_last_name) ?></title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-family: Anton;
            color: #000;
            margin-bottom: 1rem;
        }

        .messages-container {
            width: 100%;
            height: 400px;
            overflow-y: auto;
            border: 2px solid #000;
            background-color: #fff;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .messages-container p {
            margin: 0.5rem 0;
        }

        .messages-container .sender {
            font-weight: bold;
            color: #ec1b2e;
        }

        form {
            width: 100%;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 0.5rem;
            font-family: 'Open Sans', sans-serif;
            border: 2px solid #000;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        button {
            background-color: #ec1b2e;
            color: #fff;
            border: none;
            padding: 0.7rem 2rem;
            font-size: 1rem;
            font-family: 'Open Sans', sans-serif;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #000;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
        <h1>Chat with <?= htmlspecialchars($receiver_first_name . ' ' . $receiver_last_name) ?></h1>
        <div class="messages-container">
            <?php foreach ($messages as $msg): ?>
                <p>
                    <span class="sender"><?= htmlspecialchars($msg['first_name']) ?>:</span>
                    <?= htmlspecialchars($msg['message']) ?> 
                    <small>(<?= htmlspecialchars($msg['timestamp']) ?>)</small>
                </p>
            <?php endforeach; ?>
        </div>
        <form method="POST">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
