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
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    /* Navbar styling */
    .navbar {
      font-family: Anton;
      background-color: #000;
      color: #fff;
      padding: 1rem;
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

    /* Main content styling */
    .main-container {
      text-align: center;
      padding: 2rem;
      background-color: #ec1b2e;
      color: #fff;
    }

    .main-container h2 {
      font-family: Anton;
      font-size: 36px;
      margin-bottom: 1rem;
    }

    .calendar-container {
      max-width: 900px;
      margin: 2rem auto;
      background: #fff;
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* FullCalendar styling overrides */
    .fc {
      font-family: Arial, sans-serif;
    }

    .fc-toolbar {
      background-color: #ec1b2e;
      color: #fff;
      border-radius: 8px;
      padding: 0.5rem;
    }

    .fc-toolbar .fc-button {
      background-color: #000;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 5px;
    }

    .fc-toolbar .fc-button:hover {
      background-color: #ec1b2e;
    }

    .fc-toolbar-title {
      font-size: 18px;
      font-weight: bold;
    }

    .fc-view-harness {
      background-color: #f4f4f4;
    }

    .fc-daygrid-day {
      background-color: #fff;
      border: 1px solid #ddd;
    }

    .fc-daygrid-day:hover {
      background-color: #f9e5e5;
    }

    .fc-day-today {
      background-color: #ffe6e6;
    }

    .fc-event {
      background-color: #ec1b2e;
      color: #fff;
      border: none;
      padding: 0.25rem;
      border-radius: 3px;
    }

    .fc-event:hover {
      background-color: #ec1b2e;
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
            <a href="#contact">ABOUT</a>
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
        initialDate: '2024-12-07',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '../php/fetch_events.php',
      });

      calendar.render();
    });
  </script>
</body>
</html>
