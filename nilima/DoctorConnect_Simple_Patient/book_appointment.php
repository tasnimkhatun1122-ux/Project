<?php
session_start();

if (!isset($_SESSION["patient_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$patient_id = $_SESSION["patient_id"];
$doctor_id = $_POST["doctor_id"];
$date = $_POST["appointment_date"];
$time = $_POST["appointment_time"];

// Check if the selected slot is already booked.
$check = $conn->prepare("
    SELECT id FROM appointments
    WHERE doctor_id = ?
    AND appointment_date = ?
    AND appointment_time = ?
    AND status = 'pending'
");

$check->bind_param("iss", $doctor_id, $date, $time);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('This time slot is already booked. Please choose another time.');
        window.location='search_doctor.php';
    </script>";
    exit();
}

// Insert appointment.
$stmt = $conn->prepare("
    INSERT INTO appointments
    (patient_id, doctor_id, appointment_date, appointment_time, status)
    VALUES (?, ?, ?, ?, 'pending')
");

$stmt->bind_param("iiss", $patient_id, $doctor_id, $date, $time);

if ($stmt->execute()) {
    echo "<script>
        alert('Appointment booked successfully!');
        window.location='my_appointments.php';
    </script>";
} else {
    echo "Booking failed.";
}
?>