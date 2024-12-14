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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/styles/court_bookings.css">
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
</head>

<body>

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

    <!-- Main Content -->
    <main>
        <!-- Title Section -->
        <section class="title-section text-center text-white">
            <h1 class="display-4">COURT BOOKINGS</h1>
            <p class="lead">Select a Court</p>
        </section>

        <!-- Category Grid -->
        <section class="category-grid container my-5">
            <div class="row g-4">
                <!-- Category 1: Courts -->
                <div class="col-md-4">
                    <div class="card category-card" data-bs-toggle="modal" data-bs-target="#ballsModal">
                        <img src="../assets/images/courts.jpg" class="card-img-top" alt="Balls">
                        <div class="card-body">
                            <h5 class="card-title text-center">Court Bookings</h5>
                        </div>
                    </div>
                </div>
                <!-- Category 2: PT -->
                <div class="col-md-4">
                    <div class="card category-card" data-bs-toggle="modal" data-bs-target="#gamesModal">
                        <img src="../assets/images/training.jpg" class="card-img-top" alt="Games">
                        <div class="card-body">
                            <h5 class="card-title text-center">Personal Training</h5>
                        </div>
                    </div>
                </div>
                <!-- Category 3: Pods -->
                <div class="col-md-4">
                    <div class="card category-card" data-bs-toggle="modal" data-bs-target="#workoutModal">
                        <img src="../assets/images/fitness_pods.jpg" class="card-img-top"
                            alt="Workout/Training Equipment">
                        <div class="card-body">
                            <h5 class="card-title text-center">Pods</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Modals -->

    <!-- Balls Modal -->
    <div class="modal fade" id="ballsModal" tabindex="-1" aria-labelledby="ballsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ballsModalLabel">Court Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form>
                        <!-- Court Selection -->
                        <div class="mb-3">
                            <label class="form-label">Select Court</label>
                            <div class="d-flex justify-content-around">
                                <!-- Tennis Court -->
                                <div>
                                    <img src="../assets/images/tennis.jpg" alt="Tennis Court"
                                        class="img-thumbnail court-option" data-court="Tennis" width="150">
                                    <div class="text-center mt-2">
                                        <input type="radio" name="courtType" value="Tennis" id="tennisCourt" required
                                            hidden>
                                        <label for="tennisCourt">Tennis Court</label>
                                    </div>
                                </div>
                                <!-- Squash Court -->
                                <div>
                                    <img src="../assets/images/squash.jpg" alt="Squash Court"
                                        class="img-thumbnail court-option" data-court="Squash" width="150">
                                    <div class="text-center mt-2">
                                        <input type="radio" name="courtType" value="Squash" id="squashCourt" required
                                            hidden>
                                        <label for="squashCourt">Squash Court</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Booking Date -->
                        <div class="mb-3">
                            <label for="bookingDate" class="form-label">Date:</label>
                            <input type="date" id="bookingDate" name="bookingDate" class="form-control" required>
                        </div>


                        <!-- Start Time Selection -->
                        <div class="mb-3">
                            <label for="startTime" class="form-label">Start Time</label>
                            <select id="startTime" name="startTime" class="form-control" required disabled>
                                <!-- Options populated via JavaScript -->
                            </select>
                        </div>

                        <!-- Duration -->
                        <div class="mb-3">
                            <label for="bookingDuration" class="form-label">Duration (minutes):</label>
                            <div class="duration-wrapper">
                                <button type="button" id="decreaseDuration" class="btn btn-outline-secondary"
                                    disabled>-</button>
                                <input type="number" id="bookingDuration" class="form-control text-center" value="30"
                                    min="30" max="120" step="30" readonly>
                                <button type="button" id="increaseDuration" class="btn btn-outline-secondary"
                                    disabled>+</button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-danger w-100" disabled>Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Other modals (Games, Workout) remain unchanged -->

    <!-- Bootstrap JS Bundle (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const courtOptions = document.querySelectorAll(".court-option");
    const bookingDate = document.getElementById("bookingDate");
    const startTimeDropdown = document.getElementById("startTime");
    const bookingDuration = document.getElementById("bookingDuration");
    const decreaseDuration = document.getElementById("decreaseDuration");
    const increaseDuration = document.getElementById("increaseDuration");
    const submitButton = document.querySelector("button[type='submit']");

    // Initially disable interactive fields
    startTimeDropdown.disabled = true;
    decreaseDuration.disabled = true;
    increaseDuration.disabled = true;
    submitButton.disabled = true;

    // Restrict date selection and disable manual typing
    const restrictDateRange = () => {
        const today = new Date();
        const todayStr = today.toISOString().split("T")[0];
        bookingDate.setAttribute("min", todayStr);
        bookingDate.addEventListener("keydown", (e) => e.preventDefault()); // Prevent manual input
    };

    restrictDateRange();

    // Populate start time dropdown
    // Populate start time dropdown with filtering for past times if today's date is selected
const populateStartTimeDropdown = () => {
    const today = new Date();
    const currentDateStr = today.toISOString().split("T")[0];

    const startTime = new Date();
    startTime.setHours(7, 0, 0, 0); // Start at 7:00 AM
    const endTime = new Date();
    endTime.setHours(21, 30, 0, 0); // End at 9:00 PM

    startTimeDropdown.innerHTML = ""; // Clear previous options

    while (startTime <= endTime) {
        const optionTime = new Date(startTime);
        const option = document.createElement("option");
        option.value = `${optionTime.getHours()}:${optionTime.getMinutes().toString().padStart(2, "0")}`;
        option.textContent = `${optionTime.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })}`;

        // Only add options for future times if today's date is selected
        if (bookingDate.value === currentDateStr) {
            if (optionTime >= today) {
                startTimeDropdown.appendChild(option);
            }
        } else {
            startTimeDropdown.appendChild(option); // Add all options for other dates
        }

        startTime.setMinutes(startTime.getMinutes() + 30);
    }
};

// Adjust the listener on `bookingDate` to re-populate times when the date changes
bookingDate.addEventListener("input", () => {
    const selectedCourt = document.querySelector(".court-option.selected");
    if (selectedCourt && bookingDate.value) {
        startTimeDropdown.disabled = false;
        decreaseDuration.disabled = false;
        increaseDuration.disabled = false;
        populateStartTimeDropdown(); // Re-populate times based on the selected date
    }
    validateForm();
});


    populateStartTimeDropdown();

    // Adjust maximum booking duration based on selected start time
    const adjustMaxDuration = () => {
        const startTime = startTimeDropdown.value; // e.g., "21:00"
        if (startTime) {
            const [hours, minutes] = startTime.split(":").map(Number);
            const startTimeInMinutes = hours * 60 + minutes; // Convert to total minutes
            const latestEndTimeInMinutes = 22 * 60; // 10:00 PM in total minutes

            let maxDuration = 120; // Default max duration is 120 minutes

            if (startTimeInMinutes >= 20 * 60) {
                // If start time is 8 PM or later, dynamically calculate max duration
                maxDuration = Math.max(0, latestEndTimeInMinutes - startTimeInMinutes);
            }

            if (parseInt(bookingDuration.value, 10) > maxDuration) {
                bookingDuration.value = maxDuration;
            }

            increaseDuration.disabled = parseInt(bookingDuration.value, 10) >= maxDuration;
            decreaseDuration.disabled = parseInt(bookingDuration.value, 10) <= 30;
        }
    };

    // Validate form: enable Submit button if all fields are properly filled
    const validateForm = () => {
        const selectedCourt = document.querySelector(".court-option.selected");
        const isDateValid = !!bookingDate.value;
        const isTimeValid = !!startTimeDropdown.value;
        const isDurationValid = bookingDuration.value >= 30 && bookingDuration.value <= 120;

        submitButton.disabled = !(selectedCourt && isDateValid && isTimeValid && isDurationValid);
    };

    // Enable fields when a valid date is selected
    bookingDate.addEventListener("input", () => {
        const selectedCourt = document.querySelector(".court-option.selected");
        if (selectedCourt && bookingDate.value) {
            startTimeDropdown.disabled = false;
            decreaseDuration.disabled = false;
            increaseDuration.disabled = false;
        }
        validateForm();
    });

    // Add event listeners for court selection
    courtOptions.forEach((court) => {
        court.addEventListener("click", () => {
            courtOptions.forEach((el) => el.classList.remove("selected"));
            court.classList.add("selected");
            if (bookingDate.value) {
                startTimeDropdown.disabled = false;
                decreaseDuration.disabled = false;
                increaseDuration.disabled = false;
            }
            validateForm();
        });
    });

    // Duration controls
    const updateDuration = (delta) => {
        let duration = parseInt(bookingDuration.value, 10);
        duration = isNaN(duration) ? 0 : duration + delta;
        const [hours, minutes] = startTimeDropdown.value.split(":").map(Number);
        const startTimeInMinutes = hours * 60 + minutes;
        const latestEndTimeInMinutes = 22 * 60;

        let maxDuration = 120; // Default max duration is 120 minutes

        if (startTimeInMinutes >= 20 * 60) {
            maxDuration = Math.max(0, latestEndTimeInMinutes - startTimeInMinutes);
        }

        if (duration >= 30 && duration <= maxDuration) {
            bookingDuration.value = duration;
        }

        increaseDuration.disabled = duration >= maxDuration;
        decreaseDuration.disabled = duration <= 30;

        validateForm();
    };

    decreaseDuration.addEventListener("click", () => updateDuration(-30));
    increaseDuration.addEventListener("click", () => updateDuration(30));

    // Listen for changes in the start time to adjust duration and validate time
    startTimeDropdown.addEventListener("change", () => {
        adjustMaxDuration();
        validateForm();
    });

    // Enable Submit button if all inputs are valid
    [bookingDate, startTimeDropdown, bookingDuration].forEach((el) =>
        el.addEventListener("input", validateForm)
    );

    // Form submission
    submitButton.addEventListener("click", async (e) => {
        e.preventDefault();
        const selectedCourt = document.querySelector(".court-option.selected").dataset.court;
        const date = bookingDate.value;
        const startTime = startTimeDropdown.value;
        const duration = bookingDuration.value;

        try {
            const response = await fetch("../php/book_court.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `court=${encodeURIComponent(selectedCourt)}&date=${encodeURIComponent(date)}&startTime=${encodeURIComponent(startTime)}&duration=${encodeURIComponent(duration)}`,
            });
            const result = await response.text();
            alert(result);
        } catch (err) {
            alert("An error occurred while booking the court.");
        }
    });
});

</script>


</body>

</html>