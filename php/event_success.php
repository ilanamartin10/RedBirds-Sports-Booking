<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure the link is passed via GET
$eventUrl = $_GET['link'] ?? null;
if (!$eventUrl) {
    die("Invalid access.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Poll Created</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet'>
    <style>
        /* Global Styling */
        body {
            margin: 0;
            font-family: 'Open Sans', Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
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
            border-radius: 5px;
        }

        .navbar nav a:hover {
            background-color: #ec1b2e;
        }

        /* Success Section */
        .main-container {
            background-color: #ec1b2e;
            color: #fff;
            text-align: center;
            padding: 3rem;
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

        h2 {
            color: #ec1b2e;
            font-family: Anton;
            margin-bottom: 1.5rem;
        }

        p {
            font-family: 'Open Sans', Arial, sans-serif;
            /* Corrected here */
            font-size: 18px;
            margin-bottom: 1.5rem;
        }

        .link-box {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 1rem;
            font-size: 16px;
            word-wrap: break-word;
            color: #333;
        }

        .copy-btn {
            background-color: #ec1b2e;
            color: #fff;
            border: none;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            font-family: Anton;
            border-radius: 5px;
            margin-top: 1rem;
        }

        .copy-btn:hover {
            background-color: #cc0000;
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

    <!-- Success Message Container -->
    <div class="success-container">
        <h2>Event Poll Created Successfully!</h2>
        <p>Share the link below with participants to vote:</p>
        <div class="link-box" id="link-box">
            <?php echo htmlspecialchars($eventUrl); ?>
        </div>
        <button class="copy-btn" onclick="copyToClipboard()">Copy Link</button>
    </div>

    <!-- Copy to Clipboard Function -->
    <script>
        function copyToClipboard() {
            const linkBox = document.getElementById('link-box').innerText;
            navigator.clipboard.writeText(linkBox).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Error copying text: ', err);
            });
        }
    </script>
</body>

</html>