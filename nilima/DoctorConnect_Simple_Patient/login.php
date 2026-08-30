<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? AND role = 'patient'");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Simple classroom login.
        // For a real project, use password_hash() and password_verify().
        if ($password == $user["password"]) {
            $_SESSION["patient_id"] = $user["id"];
            $_SESSION["patient_name"] = $user["name"];

            // PHP Cookie: remember the patient email for 7 days.
            setcookie("patient_email", $email, time() + (7 * 24 * 60 * 60), "/");

            header("Location: search_doctor.php");
            exit();
        }
    }

    $error = "Invalid patient email or password.";
}

$savedEmail = $_COOKIE["patient_email"] ?? "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorConnect - Patient Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

<div class="login-box">
    <div class="logo">Doctor<span>Connect</span></div>
    <h2>Patient Login</h2>
    <p class="small-text">Login to book your doctor appointment</p>

    <?php if ($error != ""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($savedEmail) ?>"
               placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password"
               placeholder="Enter your password" required>

        <button type="submit" class="btn">Login</button>
    </form>

    <p class="demo">Demo: nilima@example.com / 123456</p>
</div>

</body>
</html>