<?php

session_start();

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request.");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    http_response_code(400);
    die("Email and password are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die("Invalid email address.");
}

$sql = "SELECT user_id, full_name, email, password, role
        FROM users
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die("Database error.");
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();

    http_response_code(401);
    die("Invalid email or password.");
}

$user = $result->fetch_assoc();

$stmt->close();

if (!password_verify($password, $user["password"])) {
    $conn->close();

    http_response_code(401);
    die("Invalid email or password.");
}

if ($user["role"] !== "receptionist") {
    $conn->close();

    http_response_code(403);
    die("You are not authorized to access the receptionist dashboard.");
}


session_regenerate_id(true);

$_SESSION["user_id"] = $user["user_id"];
$_SESSION["full_name"] = $user["full_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];

$conn->close();


header("Location: receptionistDashboard.php");
exit;

?>