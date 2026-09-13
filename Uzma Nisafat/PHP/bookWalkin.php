<?php

session_start();

header("Content-Type: application/json");

require_once "db.php";

function respond($success, $message, $extra = []) {
    global $conn;
    echo json_encode(array_merge(
        ["success" => $success, "message" => $message],
        $extra
    ));
    if (isset($conn)) {
        $conn->close();
    }
    exit;
}

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "receptionist") {
    respond(false, "You are not authorized to do this.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(false, "Invalid request.");
}

$patientName = trim($_POST["patientName"] ?? "");
$patientPhone = trim($_POST["patientPhone"] ?? "");
$doctorId = (int) ($_POST["doctorId"] ?? 0);
$slot = trim($_POST["slot"] ?? "");

if ($patientName === "") {
    respond(false, "Please enter the patient's name.");
}

if ($patientPhone === "" || !preg_match("/^[0-9+\-\s]{7,15}$/", $patientPhone)) {
    respond(false, "Please enter a valid phone number.");
}

if ($doctorId <= 0) {
    respond(false, "Please select a doctor.");
}

if ($slot === "") {
    respond(false, "Please select a time slot.");
}

$timeSlot = ($slot === "next") ? "Next available" : $slot;


$sql = "SELECT u.full_name
        FROM doctors d
        JOIN users u ON d.user_id = u.user_id
        WHERE d.doctor_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    respond(false, "Database error.");
}

$stmt->bind_param("i", $doctorId);
$stmt->execute();
$doctorResult = $stmt->get_result();

if ($doctorResult->num_rows === 0) {
    $stmt->close();
    respond(false, "Selected doctor was not found.");
}

$doctorRow = $doctorResult->fetch_assoc();
$doctorName = $doctorRow["full_name"];
$stmt->close();

$sql = "SELECT user_id, full_name
        FROM users
        WHERE phone = ? AND role = 'patient'
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    respond(false, "Database error.");
}

$stmt->bind_param("s", $patientPhone);
$stmt->execute();
$patientResult = $stmt->get_result();

if ($patientResult->num_rows > 0) {

    $patientRow = $patientResult->fetch_assoc();
    $patientId = $patientRow["user_id"];
    $stmt->close();

} else {

    $stmt->close();

    
    $digitsOnly = preg_replace("/[^0-9]/", "", $patientPhone);
    $placeholderEmail = "walkin" . $digitsOnly . time() . "@doctorconnect.local";
    $placeholderPassword = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, phone, password, role)
            VALUES (?, ?, ?, ?, 'patient')";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        respond(false, "Database error.");
    }

    $stmt->bind_param(
        "ssss",
        $patientName,
        $placeholderEmail,
        $patientPhone,
        $placeholderPassword
    );

    if (!$stmt->execute()) {
        $stmt->close();
        respond(false, "Could not create the patient's account.");
    }

    $patientId = $stmt->insert_id;
    $stmt->close();
}

$today = date("Y-m-d");

$sql = "INSERT INTO appointments (patient_id, doctor_id, appt_date, time_slot, status)
        VALUES (?, ?, ?, ?, 'pending')";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    respond(false, "Database error.");
}

$stmt->bind_param("iiss", $patientId, $doctorId, $today, $timeSlot);

if (!$stmt->execute()) {
    $stmt->close();
    respond(false, "Could not book the walk-in appointment.");
}

$apptId = $stmt->insert_id;
$stmt->close();

respond(true, "Walk-in added to today's queue!", [
    "apptId" => $apptId,
    "patientName" => $patientName,
    "doctorName" => $doctorName,
    "timeSlot" => $timeSlot
]);

?>
