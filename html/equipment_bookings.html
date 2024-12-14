<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['session_token']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>RedBird Bookings - Equipment</title>
  <!-- Bootstrap CSS -->
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>

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

    /* Hero Section */
.title-section,
.event-hero-section {
    background-color: #ec1b2e; /* Bright red background */
    padding: 100px 20px;
    text-align: center;
    color: #ffffff;
}

.title-section h1,
.event-hero-section h1 {
    font-size: 72px;
    font-weight: bold;
}

.title-section p,
.event-hero-section p {
    font-size: 24px;
    margin-top: 20px;
}

/* Category Grid */
.category-card {
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.category-card img {
    height: 200px;
    object-fit: cover;
}

.category-card .card-body {
    background-color: #000000;
}

.category-card .card-title {
    color: #ffffff;
    font-size: 1.25rem;
}

/* Modal Styling */
.modal-content {
    background-color: #ffffff;
    color: #000000;
}

.modal-header {
    border-bottom: none;
}

.modal-title {
    color: #ec1b2e;
    font-weight: bold;
}

.list-group-item {
    background-color: #f8f9fa;
    color: #000000;
    border: 1px solid #dee2e6;
}

.btn-danger {
    background-color: #ec1b2e;
    border: none;
}

.btn-danger:hover {
    background-color: #cc0000;
}


    </style>
<body>

  <!-- Header Section -->
  <header>
    <div class="container-fluid">
        <!-- Navbar -->
        <div class="navbar">
          <img src="../assets/images/logo.png" alt="Redbird Bookings Logo" class="logo" width="180" height="140">
          <nav>
            <a href="#features">FEATURES</a>
            <a href="#about">ABOUT</a>
            <a href="#contact">CONTACT</a>
            <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="btn">LOG OUT</a>
            <?php else: ?>
            <a href="login.html" class="btn">LOG IN</a>
            <a href="signup.html" class="btn">SIGN UP</a>
            <?php endif; ?>
          </nav>
        </div>
      </div>
    </div>
  </header>
  </head>
  <!-- Main Content -->
  <main>
    <!-- Title Section -->
    <section class="title-section text-center text-white">
      <h1 class="display-4">BOOKINGS</h1>
      <p class="lead">Select a Service</p>
    </section>

    <!-- Category Grid -->
    <section class="category-grid container my-5">
      <div class="row g-4">
        <!-- Category 1: Balls -->
        <div class="col-md-4">
          <div class="card category-card" data-bs-toggle="modal" data-bs-target="#ballsModal">
            <img src="../assets/images/balls.jpg" class="card-img-top" alt="Balls">
            <div class="card-body">
              <h5 class="card-title text-center">Balls</h5>
            </div>
          </div>
        </div>
        <!-- Category 2: Games -->
        <div class="col-md-4">
          <div class="card category-card" data-bs-toggle="modal" data-bs-target="#gamesModal">
            <img src="../assets/images/games.jpg" class="card-img-top" alt="Games">
            <div class="card-body">
              <h5 class="card-title text-center">Games</h5>
            </div>
          </div>
        </div>
        <!-- Category 3: Workout/Training Equipment -->
        <div class="col-md-4">
          <div class="card category-card" data-bs-toggle="modal" data-bs-target="#workoutModal">
            <img src="../assets/images/workout.jpg" class="card-img-top" alt="Workout/Training Equipment">
            <div class="card-body">
              <h5 class="card-title text-center">Workout/Training</h5>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Modals -->
  <div class="modal fade" id="ballsModal" tabindex="-1" aria-labelledby="ballsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ballsModalLabel">Balls</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Basketballs
              <button class="btn btn-danger btn-sm book-btn" data-item="Basketballs">Book</button>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Foosball balls
              <button class="btn btn-danger btn-sm book-btn" data-item="Foosball balls">Book</button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.book-btn').forEach(button => {
      button.addEventListener('click', async (e) => {
        const item = e.target.getAttribute('data-item');

        try {
          // Check if user is logged in by making an API call
          const sessionResponse = await fetch('../php/check_session.php');
          const sessionResult = await sessionResponse.json();

          if (!sessionResult.isLoggedIn) {
            // If not logged in, redirect to login page
            alert('You must log in to book an item.');
            window.location.href = '../html/login.html';
            return;
          }

          // If logged in, proceed with the booking
          const bookingResponse = await fetch('../php/book_equipment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `item=${encodeURIComponent(item)}`
          });

          const bookingResult = await bookingResponse.text();
          alert(bookingResult); // Display success or error message
        } catch (error) {
          console.error('Error:', error);
          alert('An error occurred while booking.');
        }
      });
    });
  </script>
</body>

</html>