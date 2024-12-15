<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['session_token']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

if (!$isLoggedIn) {
    header("Location: ../html/login.html");
    exit;
}

// Check if the user already has a membership for this semester
$currentSemesterStart = new DateTime('2024-09-01'); // Start of semester
$currentSemesterEnd = new DateTime('2024-12-31');   // End of semester
$semesterStartStr = $currentSemesterStart->format('Y-m-d');
$semesterEndStr = $currentSemesterEnd->format('Y-m-d');

$alreadyHasMembership = false;
$existingMembershipQuery = $conn->prepare("
    SELECT id FROM memberships 
    WHERE user_id = ? 
    AND purchase_date BETWEEN ? AND ?
");
$existingMembershipQuery->bind_param("iss", $user_id, $semesterStartStr, $semesterEndStr);
$existingMembershipQuery->execute();
$existingMembershipQuery->store_result();
if ($existingMembershipQuery->num_rows > 0) {
    $alreadyHasMembership = true;
}
$existingMembershipQuery->close();

// Default values
$membership_name = "";
$price = 0.0;
$role_selected = false;
$success = false;
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyHasMembership) {
    // Check if the role is selected
    if (isset($_POST['role']) && ($_POST['role'] === 'undergraduate' || $_POST['role'] === 'graduate')) {
        $role_selected = true;

        // Determine membership type and price
        $membership_name = ($_POST['role'] === 'undergraduate') ? "Undergraduate Membership" : "Graduate Membership";
        $price = ($_POST['role'] === 'undergraduate') ? 55.99 : 65.99;

        // If purchase is confirmed
        if (isset($_POST['confirm_purchase'])) {
            $stmt = $conn->prepare("INSERT INTO memberships (user_id, membership_type, price, purchase_date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("isd", $user_id, $membership_name, $price);

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Error processing purchase: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Please select your role before proceeding.";
    }
} elseif ($alreadyHasMembership) {
    $error = "You already purchased a membership for this semester. You cannot buy it again.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>RedBird Bookings - Buy Membership</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
    <style>
        /* Navbar styling */
        .navbar {
            font-family: Anton;
            background-color: #000;
            color: #fff;
            padding: 0.09rem;
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
            font-family: Anton;
            padding: 0.5rem 1rem;
            font-size: 20px;
            border-radius: 5px;
            letter-spacing: 0.05rem;
        }

        .navbar nav a:hover {
            background-color: #ec1b2e;
        }

        .container {
            margin: 50px auto;
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-family: Anton;
            color: #ec1b2e;
        }

        p {
            font-family: 'Open Sans';
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .btn {
            font-family: 'Open Sans';
            background-color: #ec1b2e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: black;
        }

        .form-control {
            font-family: 'Open Sans';
            padding: 10px;
            margin: 20px auto;
            width: 50%;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <img src="../assets/images/logo.png" alt="Redbird Bookings Logo" class="logo" width="180" height="140">
        <nav>
            <a href="equipment_bookings.php">BOOK EQUIPMENT</a>
            <a href="court_bookings.php">BOOK A COURT</a>
            <?php if ($isLoggedIn): ?>
                <a href="../php/profile.php?user_id=<?= htmlspecialchars($user_id) ?>">MY PROFILE</a>
                <a href="logout.php">LOG OUT</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="container">
        <h1>Buy Gym Membership</h1>

        <?php if ($alreadyHasMembership): ?>
            <div class="alert alert-danger">
                You already purchased a membership for this semester. You cannot buy it again.
            </div>
            <a href="profile.php" class="btn">Back to Profile</a>
        <?php else: ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">Membership purchased successfully!</div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!$role_selected): ?>
                <form method="POST">
                    <p>Select to view membership options:</p>
                    <select name="role" class="form-control" required>
                        <option value="" disabled selected>Select</option>
                        <option value="undergraduate">Undergraduate</option>
                        <option value="graduate">Graduate</option>
                    </select>
                    <button type="submit" class="btn mt-3">View Membership Options</button>
                </form>
            <?php else: ?>
                <p>Membership: <strong><?php echo htmlspecialchars($membership_name); ?></strong></p>
                <p>Price: <strong>$<?php echo number_format($price, 2); ?></strong> (plus applicable taxes)</p>
                <form method="POST">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($_POST['role']); ?>">
                    <button type="submit" name="confirm_purchase" class="btn mt-3">Purchase Membership</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>
