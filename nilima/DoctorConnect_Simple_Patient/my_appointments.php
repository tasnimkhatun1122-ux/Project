<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$patient_id = $_SESSION["patient_id"];

$stmt = $conn->prepare("
    SELECT
        appointments.*,
        doctors.name AS doctor_name,
        doctors.specialization,
        departments.name AS department
    FROM appointments
    JOIN doctors ON appointments.doctor_id = doctors.id
    JOIN departments ON doctors.department_id = departments.id
    WHERE appointments.patient_id = ?
    ORDER BY appointment_date DESC, appointment_time DESC
");

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$appointments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorConnect - My Appointments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">

    <div class="logo">Doctor<span>Connect</span></div>

    <nav>
        <a href="search_doctor.php">Find Doctor</a>
        <a class="active" href="my_appointments.php">My Appointments</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="patient-name">
        <?= htmlspecialchars($_SESSION["patient_name"]) ?>
    </div>

</header>

<main class="container">

    <div class="page-heading">
        <h1>My Appointments</h1>
        <p>View your appointment history and status.</p>
    </div>

    <div class="table-card">

        <table>

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($appointments->num_rows > 0): ?>

                <?php while ($appointment = $appointments->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?= date("d M Y", strtotime($appointment["appointment_date"])) ?>
                        </td>

                        <td>
                            <?= date("h:i A", strtotime($appointment["appointment_time"])) ?>
                        </td>

                        <td>
                            <b><?= htmlspecialchars($appointment["doctor_name"]) ?></b>
                            <br>
                            <small>
                                <?= htmlspecialchars($appointment["specialization"]) ?>
                            </small>
                        </td>

                        <td>
                            <?= htmlspecialchars($appointment["department"]) ?>
                        </td>

                        <td>

                            <?php if ($appointment["status"] == "pending"): ?>

                                <span class="status pending">Pending</span>

                            <?php elseif ($appointment["status"] == "completed"): ?>

                                <span class="status completed">Completed</span>

                            <?php elseif ($appointment["status"] == "cancelled"): ?>

                                <span class="status cancelled">Cancelled</span>

                            <?php else: ?>

                                <span class="status">
                                    <?= htmlspecialchars($appointment["status"]) ?>
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="no-data">
                        You have no appointments yet.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>