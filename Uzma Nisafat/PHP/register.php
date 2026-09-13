<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$fullName = trim($_POST["fullName"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$role = trim($_POST["role"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";



if (
    $fullName === "" ||
    $email === "" ||
    $role === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    die("Please fill in all required fields.");
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}


if ($phone !== "" && !preg_match("/^[0-9+\-\s]{7,20}$/", $phone)) {
    die("Invalid phone number.");
}


$allowedRoles = [
    "patient",
    "doctor",
    "receptionist",
    "admin"
];

if (!in_array($role, $allowedRoles, true)) {
    die("Invalid role.");
}


if (strlen($password) < 6) {
    die("Password must be at least 6 characters.");
}


if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}



$sql = "SELECT user_id FROM users WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error.");
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();
    $conn->close();

    die("An account with this email already exists.");
}

$stmt->close();


$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);


$sql = "
    INSERT INTO users
    (full_name, email, phone, password, role)
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error.");
}

$stmt->bind_param(
    "sssss",
    $fullName,
    $email,
    $phone,
    $hashedPassword,
    $role
);


if ($stmt->execute()) {

    echo "Account created successfully!";

} else {

    echo "Registration failed.";

}


$stmt->close();
$conn->close();

?>