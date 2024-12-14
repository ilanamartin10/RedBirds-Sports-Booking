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
    .navbar { font-family: Anton; background-color: #000; color: #fff; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
    .navbar h1 { margin: 0; font-size: 24px; }
    .navbar nav { display: flex; gap: 1rem; }
    .navbar nav a { color: #fff; text-decoration: none; font-family: Anton; font-weight: bold; padding: 0.5rem 1rem; border-radius: 5px; }
    .navbar nav a:hover { background-color: #e74c3c; }
    .page-container { max-width: 900px; margin: 2rem auto; padding: 1rem; background: #fff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    .page-container h2 { font-family: Anton; font-size: 32px; color: #e74c3c; text-align: center; }
    .post-form { margin-bottom: 2rem; }
    .post-form label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .post-form input, .post-form textarea { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 5px; font-family: 'Open Sans'; }
    .post-form button { font-family: Anton; background-color: #e74c3c; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; cursor: pointer; }
    .post-form button:hover { background-color: #c0392b; }
    .posts { display: flex; flex-direction: column; gap: 1.5rem; }
    .post-item { padding: 1rem; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; font-family: 'Open Sans'; }
    .post-item h3 { margin: 0; font-size: 20px; color: #e74c3c; }
    .post-item p { margin: 0.5rem 0; }
    .post-item a { color: #e74c3c; text-decoration: none; font-weight: bold; }
    .post-item a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <img src="../assets/images/logo.png" alt="Logo" class="logo" width="180" height="140">
    <nav>
      <a href="#features">FEATURES</a>
      <a href="#about">ABOUT</a>
      <a href="#contact">CONTACT</a>
      <a href="../php/logout.php" style="color: red;">LOG OUT</a>
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

      <button type="submit">Post</button>
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
