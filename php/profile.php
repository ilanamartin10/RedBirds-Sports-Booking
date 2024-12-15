<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    error_log("Session not set.");
    header("Location: ../html/login.html");
    exit;
}

$isLoggedIn = true;
$user_id = $_SESSION['user_id'];

// Validate session token
$stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
$stmt->bind_param("is", $user_id, $_SESSION['session_token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    error_log("Session validation failed for user ID: " . $user_id);
    header("Location: ../html/login.html");
    exit;
}
$stmt->close();

// Determine profile to fetch
$profile_user_id = $_GET['user_id'] ?? $user_id;
$is_own_profile = ($profile_user_id == $user_id);

// Fetch profile details
$stmt = $conn->prepare("SELECT u.first_name, u.last_name, u.email, p.favorite_sports, p.major, p.minor, p.about 
                        FROM profiles p 
                        INNER JOIN users u ON p.user_id = u.id 
                        WHERE p.user_id = ?");
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $favorite_sports, $major, $minor, $about);

if (!$stmt->fetch()) {
    error_log("Profile not found for user ID: " . $profile_user_id);
    header("Location: error_page.php?error=profile_not_found");
    exit;
}
$stmt->close();

// Fetch membership details for the logged-in user only
$membershipEndDate = new DateTime('2024-12-31');
$hasMembership = false;
$remainingDays = null;

if ($is_own_profile) {
    $membershipQuery = $conn->prepare("SELECT purchase_date FROM memberships 
                                        WHERE user_id = ? AND membership_type LIKE '%Membership%' 
                                        ORDER BY purchase_date DESC LIMIT 1");
    $membershipQuery->bind_param("i", $user_id);
    $membershipQuery->execute();
    $membershipQuery->bind_result($purchaseDate);
    $hasMembership = $membershipQuery->fetch();
    $membershipQuery->close();

    // Calculate remaining days if a membership exists
    if ($hasMembership) {
        $today = new DateTime();
        $remainingInterval = $today->diff($membershipEndDate);
        $remainingDays = ($remainingInterval->invert === 0) ? $remainingInterval->days : 0; // Ensure no negative values
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RedBird Bookings - Profile</title>
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image: linear-gradient(to right, white 0%, white 40%, #ec1b2e 40%, #ec1b2e 100%);
            font-family: Arial, sans-serif;
        }
       
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

        /* Profile Container */
        .profile-container {
            font-family: 'Open Sans';
            display: flex;
            width: 100%;
            height: calc(100vh - 120px); 
        }

        /* Left Profile Section */
        .profile-left {
            width: 40%;
            font-family: 'Open Sans';
            background: white;
            color: black;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .profile-pic {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            text-align: center;
        }

        .profile-info h1 {
          font-family: 'Open Sans';
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .profile-details {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        .profile-details li {
          font-family: 'Open Sans';
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .find-partner-btn {
          font-family: 'Open Sans';
            margin-top: 20px;
            background-color: #ec1b2e;
            color: white;
            font-weight: bold;
        }

        /* Right Profile Section */
        .profile-right {
            font-family: 'Open Sans';
            width: 60%;
            background: #ec1b2e;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .about-section {
            text-align: center;
        }

        .about-section h2 {
            font-family: 'Anton';
            font-size: 3rem;
            border-bottom: 2px solid white;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .about-section p {
            font-family: 'Open Sans';
            line-height: 1.8;
            max-width: 80%;
            margin: 0 auto;
        }

        .edit-profile-btn {
            background-color: black;
            font-family: 'Open Sans';
            color: white;
            font-weight: bold;
            padding: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .edit-profile-btn:hover {
            background-color: white;
            color: black;
            text-decoration: none;
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
            <?php if ($isLoggedIn): ?>
                <a href="http://localhost/redbird_bookings/php/profile.php?user_id=<?= htmlspecialchars($user_id) ?>" class="btn">MY PROFILE</a>
                <a href="logout.php" class="btn">LOG OUT</a>
            <?php else: ?>
                <a href="login.html" class="btn">LOG IN</a>
                <a href="signup.html" class="btn">SIGN UP</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="profile-container">
        <div class="profile-left">
            <div class="profile-pic">
                <img src="../assets/images/picture_placeholder.png" alt="Profile Picture">
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($first_name); ?></h1>
                <ul class="profile-details">
                    <li>📧 Email Address: <?php echo htmlspecialchars($email); ?></li>
                    <li>⚽ Favorite Sports: <?php echo htmlspecialchars($favorite_sports); ?></li>
                    <li>🎓 Major: <?php echo htmlspecialchars($major); ?></li>
                    <li>📜 Minor: <?php echo htmlspecialchars($minor); ?></li>
                    <?php if ($is_own_profile && $hasMembership): ?>
                        <li>🏋️ Gym Membership: 
                    <strong>
            <?= $remainingDays > 0 ? "Your membership expires in $remainingDays days" : "Your membership has expired" ?>
        </strong>
                    <?php endif; ?>
                </ul>
                <?php if (!$is_own_profile): ?>
                    <a href="message_user.php?user_id=<?= htmlspecialchars($profile_user_id) ?>">
                        <button class="btn edit-profile-btn">Message</button>
                    </a>
                <?php endif; ?>

                <?php if ($is_own_profile): ?>
                    <a href="messages.php">
                        <button class="btn edit-profile-btn">View Messages</button>
                    </a>
                    <a href="../html/find_a_partner.php">
                        <button class="btn edit-profile-btn">Find a Partner</button>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-right">
            <div class="about-section">
                <h2>About Me</h2>
                <p><?php echo htmlspecialchars($about); ?></p>
                <?php if ($is_own_profile): ?>
                    <form method="POST" action="update_profile.php">
                        <button class="btn edit-profile-btn" type="button" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                    </form>
                    <br><br><br>
                    <div class="btn-group">
                        <a href="../html/dashboard.php">
                            <button class="btn edit-profile-btn">Dashboard</button>
                        </a>
                        &nbsp;&nbsp;
                        <a href="../html/event_poll.html">
                            <button class="btn edit-profile-btn">Event Polling</button>
                        </a>
                        <?php if (!$hasMembership): ?>
                            <div class="btn-group">
                                &nbsp;&nbsp;
                                <a href="buy_membership.php">
                                    <button class="btn edit-profile-btn">Buy Gym Membership</button>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label for="favoriteSports" class="form-label">Favorite Sports</label>
                            <input type="text" class="form-control" id="favoriteSports" name="favorite_sports" value="<?php echo htmlspecialchars($favorite_sports); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="major" class="form-label">Major</label>
                            <input type="text" class="form-control" id="major" name="major" value="<?php echo htmlspecialchars($major); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="minor" class="form-label">Minor</label>
                            <input type="text" class="form-control" id="minor" name="minor" value="<?php echo htmlspecialchars($minor); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="about" class="form-label">About Me</label>
                            <textarea class="form-control" id="about" name="about"><?php echo htmlspecialchars($about); ?></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" id="saveProfileButton">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('saveProfileButton').addEventListener('click', function () {
            const formData = new FormData(document.getElementById('editProfileForm'));

            fetch('update_profile.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Profile updated successfully!');
                        location.reload(); // Refresh to show updated data
                    } else {
                        alert('Error updating profile: ' + data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>
