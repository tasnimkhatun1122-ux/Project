<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$departments = $conn->query("SELECT * FROM departments ORDER BY name");

$doctors = $conn->query("
    SELECT doctors.*, departments.name AS department_name
    FROM doctors
    JOIN departments ON doctors.department_id = departments.id
    ORDER BY doctors.name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorConnect - Search Doctor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">Doctor<span>Connect</span></div>

    <nav>
        <a class="active" href="search_doctor.php">Find Doctor</a>
        <a href="my_appointments.php">My Appointments</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="patient-name">
        <?= htmlspecialchars($_SESSION["patient_name"]) ?>
    </div>
</header>

<main class="container">

    <div class="page-heading">
        <h1>Find a Doctor</h1>
        <p>Search for a doctor and book an available appointment.</p>
    </div>

    <div class="search-box">
        <input type="text" id="searchInput"
               placeholder="Search doctor by name..."
               onkeyup="searchDoctors()">

        <select id="departmentFilter" onchange="searchDoctors()">
            <option value="">All Departments</option>

            <?php while ($department = $departments->fetch_assoc()): ?>
                <option value="<?= strtolower($department["name"]) ?>">
                    <?= htmlspecialchars($department["name"]) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="doctor-list">

        <?php while ($doctor = $doctors->fetch_assoc()): ?>

        <div class="doctor-card"
             data-name="<?= strtolower($doctor["name"]) ?>"
             data-department="<?= strtolower($doctor["department_name"]) ?>">

            <div class="doctor-avatar">
                <?= strtoupper(substr($doctor["name"], 0, 1)) ?>
            </div>

            <div class="doctor-details">
                <h2><?= htmlspecialchars($doctor["name"]) ?></h2>

                <p class="specialization">
                    <?= htmlspecialchars($doctor["specialization"]) ?>
                </p>

                <p>
                    <b>Department:</b>
                    <?= htmlspecialchars($doctor["department_name"]) ?>
                </p>

                <p>
                    <b>Consultation Fee:</b>
                    ৳<?= htmlspecialchars($doctor["consultation_fee"]) ?>
                </p>

                <p>
                    <b>Visiting Time:</b>
                    <?= htmlspecialchars($doctor["visiting_time"]) ?>
                </p>

                <button class="btn"
                    onclick="openBooking(
                        <?= $doctor['id'] ?>,
                        '<?= htmlspecialchars($doctor['name'], ENT_QUOTES) ?>'
                    )">
                    Book Appointment
                </button>
            </div>
        </div>

        <?php endwhile; ?>

    </div>

</main>

<!-- Booking popup -->
<div id="bookingModal" class="modal">

    <div class="modal-content">

        <span class="close" onclick="closeBooking()">&times;</span>

        <h2>Book Appointment</h2>

        <p>Doctor:</p>
        <h3 id="selectedDoctor"></h3>

        <form action="book_appointment.php" method="POST">

            <input type="hidden" id="doctorId" name="doctor_id">

            <label>Choose Date</label>
            <input type="date" name="appointment_date" required>

            <label>Choose Time</label>
            <select name="appointment_time" required>
                <option value="">Select time</option>
                <option value="09:00:00">09:00 AM</option>
                <option value="10:30:00">10:30 AM</option>
                <option value="12:00:00">12:00 PM</option>
                <option value="04:00:00">04:00 PM</option>
                <option value="06:30:00">06:30 PM</option>
            </select>

            <button class="btn" type="submit">Confirm Booking</button>

        </form>

    </div>
</div>

<script src="script.js"></script>

</body>
</html>