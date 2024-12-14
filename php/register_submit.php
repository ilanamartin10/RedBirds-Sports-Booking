<?php
// Connect to MySQL
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to manage login state
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        die("All fields are required!");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Create default profile for the user
        $stmt2 = $conn->prepare("INSERT INTO profiles (user_id, favorite_sports, major, minor, about) VALUES (?, '', '', '', '')");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $stmt2->close();

        // Generate a session token
        $session_token = bin2hex(random_bytes(32));

        // Store session token in the database
        $stmt3 = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, created_at) VALUES (?, ?, NOW())");
        $stmt3->bind_param("is", $user_id, $session_token);
        $stmt3->execute();
        $stmt3->close();

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['session_token'] = $session_token;

        // Redirect to profile page
        header("Location: profile.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
