<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
  error_log("Session variables not set. Debug info: " . print_r($_SESSION, true)); 
  header("Location: ../html/login.html");
  exit;
}

$isLoggedIn = true;
// Database connection
$conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate session token in the database
$stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "Session validation failed. Debug info:";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Session Token: " . $_SESSION['session_token'] . "<br>";
    header("Location: ../html/login.html");
    exit;
}

$stmt->close();

// Fetch user profile details
$user_id = $_SESSION['user_id'];

// Query the view to fetch profile details including first_name
$stmt = $conn->prepare("SELECT first_name, favorite_sports, major, minor, about FROM profiles_with_name WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $favorite_sports, $major, $minor, $about);

if (!$stmt->fetch()) {
    // Handle case where no profile exists for the user
    $first_name = "Unknown";
    $favorite_sports = "Not set";
    $major = "Not set";
    $minor = "Not set";
    $about = "Not set";
}

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include existing styles and libraries -->
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
      padding: 0.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h1 {
      margin: 0;
      font-size: 24px;
    }

    .navbar nav {
      display: flex;
      gap: 1rem;
    }

    .navbar nav a {
      color: #fff;
      text-decoration: none;
      font-family: Anton;
      font-weight: bold;
      padding: 0.5rem 1rem;
      border-radius: 5px;
    }

    .navbar nav a:hover {
      background-color: #ec1b2e;
    }

        /* Profile Container */
        .profile-container {
            display: flex;
            width: 100%;
            height: calc(100vh - 120px); /* Subtract navbar height */
        }

        /* Left Profile Section */
        .profile-left {
            width: 40%;
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
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .profile-details {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        .profile-details li {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .find-partner-btn {
            margin-top: 20px;
            background-color: #ec1b2e;
            color: white;
            font-weight: bold;
        }

        /* Right Profile Section */
        .profile-right {
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
            font-size: 3rem;
            border-bottom: 2px solid white;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .about-section p {
            line-height: 1.8;
            max-width: 80%;
            margin: 0 auto;
        }

        .edit-profile-btn {
            background-color: black;
            color: white;
            text-transform: uppercase;
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
            <a href="#contact">ABOUT</a>
            <?php if ($isLoggedIn): ?>
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
                </ul>
                <a href = "../html/find_a_partner.php">
                <button class="btn edit-profile-btn">Find a Partner</button>
              </a> 
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
            <?php endif; ?>
            <br><br><br>
            <div class="btn-group">
              <a href = "../html/dashboard.php">
                <button class="btn edit-profile-btn">Dashboard</button>
              </a>  
              &nbsp;&nbsp;
              <a href = "../html/event_poll.html">
                <button class="btn edit-profile-btn">Event Polling</button>
              </a> 
            </div>
        </div>

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
</html>