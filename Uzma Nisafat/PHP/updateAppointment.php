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

$apptId = (int) ($_POST["apptId"] ?? 0);
$action = trim($_POST["action"] ?? "");

$allowedActions = ["checkin", "noshow", "rebook"];

if ($apptId <= 0 || !in_array($action, $allowedActions, true)) {
    respond(false, "Invalid request.");
}


$newStatus = null;
$requiredCurrent = null;

if ($action === "checkin") {
    $newStatus = "checked_in";
    $requiredCurrent = "pending";
} elseif ($action === "noshow") {
    $newStatus = "no_show";
    $requiredCurrent = "pending";
} elseif ($action === "rebook") {
    $newStatus = "pending";
    $requiredCurrent = "no_show";
}

$sql = "UPDATE appointments
        SET status = ?
        WHERE appt_id = ? AND status = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    respond(false, "Database error.");
}

$stmt->bind_param("sis", $newStatus, $apptId, $requiredCurrent);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    $stmt->close();
    respond(false, "This appointment was already updated. Refresh the page.");
}

$stmt->close();

respond(true, "Updated.", ["status" => $newStatus]);

?>
