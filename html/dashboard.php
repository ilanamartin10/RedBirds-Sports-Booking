<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    header("Location: ../html/login.html");
    exit;
}

// Validate session token
include '../php/db_connect.php';
$stmt = $conn->prepare("SELECT id FROM user_sessions WHERE user_id = ? AND session_token = ?");
$stmt->bind_param("is", $_SESSION['user_id'], $_SESSION['session_token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header("Location: ../html/login.html");
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customized Calendar</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <style>
    /* Same styling as before */
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <img src="../assets/images/logo.png" alt="Redbird Bookings Logo" class="logo" width="180" height="140">
    <nav>
        <a href="#features">FEATURES</a>
        <a href="#about">ABOUT</a>
        <a href="#contact">CONTACT</a>
        <a href="../php/logout.php" class="btn">LOG OUT</a>
    </nav>
  </div>

  <div class="main-container">
    <h2>Appointment Dashboard</h2>
    <p style="font-family:'Open Sans';">Your schedule at a glance</p>
  </div>

  <!-- Calendar Container -->
  <div class="calendar-container">
    <div id="calendar"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: '2024-11-07',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '../php/fetch_bookings.php',
      });

      calendar.render();
    });
  </script>
</body>
</html>
