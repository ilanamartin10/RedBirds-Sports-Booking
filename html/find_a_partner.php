<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find a Partner</title>
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; }
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
    .page-container { max-width: 900px; margin: 2rem auto; padding: 1rem; background: #fff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    .page-container h2 { font-family: Anton; font-size: 32px; color: #e74c3c; text-align: center; }
    .post-form { font-family: 'Open Sans';margin-bottom: 2rem; }
    .post-form label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .post-form input, .post-form textarea { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 5px; font-family: 'Open Sans'; }
    .post-form button { font-family: Anton; background-color: #e74c3c; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; }
    .post-form button:hover { background-color: #c0392b; }
    .posts { font-family: 'Open Sans'; display: flex; flex-direction: column; gap: 1.5rem; }
    .post-item { font-family: 'Open Sans'; padding: 1rem; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; font-family: 'Open Sans'; }
    .post-item h3 { margin: 0; font-size: 20px; color: #e74c3c; }
    .post-item p { margin: 0.5rem 0; }
    .post-item a { color: #e74c3c; text-decoration: none; font-weight: bold; }
    .post-item a:hover { text-decoration: underline; }
    .post-button {
    font-family: 'Open Sans';
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
            <a href="http://localhost/redbird_bookings/php/profile.php?user_id=<?= htmlspecialchars($user_id) ?>" class="btn">MY PROFILE</a>
        <a href="../php/logout.php" class="btn">LOG OUT</a>
    </nav>
  </div>

  <!-- Main content -->
  <div class="page-container">
    <h2>Find a Partner</h2>

    <!-- Success Message -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div style="color: green; text-align: center; margin-bottom: 1rem;">
      Post created successfully!
    </div>
    <?php endif; ?>

    <!-- Post Form -->
    <form class="post-form" action="../php/create_post.php" method="POST">
      <label for="title">Title:</label>
      <input type="text" id="title" name="title" placeholder="Enter a catchy title" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="4" placeholder="Describe what you’re looking for" required></textarea>

      <button type="submit" class="post-button">Post</button>
    </form>

    <!-- Posts Listing -->
    <div class="posts">
      <?php
      // Connect to MySQL
      $conn = new mysqli('localhost', 'root', '', 'redbird_bookings');
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Fetch posts from the database
      $sql = "SELECT posts.id, posts.title, posts.description, posts.user_id, users.first_name, users.last_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          // Output each post
          while ($row = $result->fetch_assoc()) {
              echo '<div class="post-item">';
              echo '<h3><a href="../php/profile.php?user_id=' . htmlspecialchars($row['user_id']) . '">' . htmlspecialchars($row['title']) . '</a></h3>';
              echo '<p>' . htmlspecialchars($row['description']) . '</p>';
              echo '<p><strong>Posted by:</strong> ' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</p>';
              echo '</div>';
          }
      } else {
          echo '<p>No posts found. Be the first to create one!</p>';
      }

      $conn->close();
      ?>
    </div>
  </div>
</body>
</html>
