
<?php
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Both fields are required!";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $session_token = bin2hex(random_bytes(32));

                $stmt2 = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, created_at) VALUES (?, ?, NOW())");
                $stmt2->bind_param("is", $user_id, $session_token);
                $stmt2->execute();
                $stmt2->close();

                $_SESSION['user_id'] = $user_id;
                $_SESSION['session_token'] = $session_token;
                header("Location: profile.php?user_id=" . $user_id);
                exit;
            } else {
                $error_message = "Invalid password!";
            }
        } else {
            $error_message = "No account found with that email!";
        }

        $stmt->close();
    }
    $conn->close();
}
?>
