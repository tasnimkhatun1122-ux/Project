<?php

session_start();

require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../Receptionist/Login.html");
    exit;
}

$userId = $_SESSION["user_id"];
$role = $_SESSION["role"];


$dashboardUrl = "receptionistDashboard.php";
$loginUrl = "../Receptionist/Login.html";

$infoMessage = "";
$errorMessage = "";



if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $formAction = $_POST["formAction"] ?? "";

    if ($formAction === "updateProfile") {

        $fullName = trim($_POST["fullName"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $phone = trim($_POST["phone"] ?? "");

        if ($fullName === "" || $email === "") {
            $errorMessage = "Full name and email are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Please enter a valid email address.";
        } elseif ($phone !== "" && !preg_match("/^[0-9+\-\s]{7,20}$/", $phone)) {
            $errorMessage = "Please enter a valid phone number.";
        } else {

            $checkSql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("si", $email, $userId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $errorMessage = "That email is already used by another account.";
                $checkStmt->close();
            } else {
                $checkStmt->close();

                $sql = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $fullName, $email, $phone, $userId);

                if ($stmt->execute()) {
                    $_SESSION["full_name"] = $fullName;
                    $_SESSION["email"] = $email;
                    $infoMessage = "Profile updated.";
                } else {
                    $errorMessage = "Could not update your profile.";
                }

                $stmt->close();
            }
        }

    } elseif ($formAction === "changePassword") {

        $currentPassword = $_POST["currentPassword"] ?? "";
        $newPassword = $_POST["newPassword"] ?? "";
        $confirmNewPassword = $_POST["confirmNewPassword"] ?? "";

        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row || !password_verify($currentPassword, $row["password"])) {
            $errorMessage = "Your current password is incorrect.";
        } elseif (strlen($newPassword) < 6) {
            $errorMessage = "New password must be at least 6 characters.";
        } elseif ($newPassword !== $confirmNewPassword) {
            $errorMessage = "New passwords do not match.";
        } else {

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashedPassword, $userId);

            if ($stmt->execute()) {
                $infoMessage = "Password changed.";
            } else {
                $errorMessage = "Could not change your password.";
            }

            $stmt->close();
        }

    } elseif ($formAction === "deleteAccount") {

        $confirmPassword = $_POST["confirmDeletePassword"] ?? "";

        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row || !password_verify($confirmPassword, $row["password"])) {
            $errorMessage = "Incorrect password. Account was not deleted.";
        } else {

            $sql = "DELETE FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            $conn->close();

            session_unset();
            session_destroy();

            header("Location: " . $loginUrl);
            exit;
        }
    }
}



$sql = "SELECT full_name, email, phone, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    header("Location: " . $loginUrl);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My profile</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 100%;
            min-height: 100vh;

            font-family: Arial, Helvetica, sans-serif;

            background-color: #f4f8fa;

            color: #18232b;
        }

        .profile-page {
            width: 100vw;
            min-height: 100vh;

            display: flex;

            background-color: #f4f8fa;
        }

        .profile-intro {
            width: 30%;

            background-color: #13838d;

            color: white;

            padding: 60px 50px;

            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: center;

            gap: 10px;

            margin-bottom: 36px;
        }

        .brand-icon {
            width: 33px;
            height: 33px;

            background-color: white;

            color: #13838d;

            border-radius: 8px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 25px;
            font-weight: bold;

            line-height: 1;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 700;
        }

        .back-link {
            display: inline-block;

            margin-top: auto;

            color: white;

            font-size: 12px;
            font-weight: 600;

            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .profile-section {
            width: 70%;

            padding: 50px 60px;

            overflow-y: auto;
        }

        .profile-title {
            font-size: 25px;
            font-weight: 700;

            margin-bottom: 6px;

            color: #17232b;
        }

        .profile-subtitle {
            font-size: 12.5px;

            color: #6c777d;

            margin-bottom: 24px;
        }

        .card {
            background-color: white;

            border-radius: 10px;

            padding: 24px 26px;

            box-shadow: 0 7px 20px rgba(30, 55, 65, 0.10);

            margin-bottom: 20px;

            max-width: 520px;
        }

        .card h2 {
            font-size: 15px;
            font-weight: 700;

            color: #17232b;

            margin-bottom: 16px;
        }

        .form-row {
            display: flex;
            gap: 14px;
        }

        .form-row .form-group {
            flex: 1;
            min-width: 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;

            font-size: 11px;
            font-weight: 600;

            color: #68747a;

            margin-bottom: 7px;
        }

        .form-input {
            width: 100%;
            height: 40px;

            border: 1px solid #dce4e7;
            border-radius: 6px;

            padding: 0 12px;

            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;

            color: #27343b;

            outline: none;

            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-input:focus {
            border-color: #13838d;

            box-shadow: 0 0 0 2px rgba(19, 131, 141, 0.08);
        }

        .form-input:disabled {
            background-color: #f4f8fa;

            color: #6c777d;
        }

        .btn {
            display: inline-block;

            height: 38px;

            padding: 0 18px;

            border: none;
            border-radius: 6px;

            font-family: Arial, Helvetica, sans-serif;
            font-size: 12.5px;
            font-weight: 600;

            cursor: pointer;

            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background-color: #13838d;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0f7079;
        }

        .btn-danger {
            background-color: white;
            color: #d64545;
            border: 1px solid #f0c9c9;
        }

        .btn-danger:hover {
            background-color: #fdf1f1;
        }

        .role-badge {
            display: inline-block;

            padding: 3px 10px;

            border-radius: 20px;

            font-size: 11px;
            font-weight: 600;

            background-color: #eef4f5;
            color: #13838d;

            text-transform: capitalize;
        }

        .info-row {
            display: flex;
            justify-content: space-between;

            font-size: 12.5px;

            padding: 8px 0;

            border-bottom: 1px solid #f2f5f7;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c777d;
        }

        .info-value {
            color: #27343b;
            font-weight: 600;
        }

        .alert {
            font-size: 12px;

            padding: 10px 14px;

            border-radius: 6px;

            margin-bottom: 18px;

            max-width: 520px;
        }

        .alert-success {
            background-color: #e7f6ef;
            color: #13838d;
        }

        .alert-error {
            background-color: #fdecec;
            color: #d64545;
        }

        .danger-zone {
            border: 1px solid #f0c9c9;
        }

        .danger-text {
            font-size: 11.5px;

            color: #6c777d;

            line-height: 1.5;

            margin-bottom: 14px;
        }

        @media (max-width: 800px) {

            .profile-page {
                flex-direction: column;
            }

            .profile-intro,
            .profile-section {
                width: 100%;
            }

            .profile-intro {
                padding: 30px;
            }

            .back-link {
                margin-top: 20px;
            }

            .profile-section {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>

    <main class="profile-page">

        <section class="profile-intro">

            <div class="brand">
                <div class="brand-icon">+</div>
                <span class="brand-name">DoctorConnect</span>
            </div>

            <p>
                Manage your account details, update your password,
                or close your account.
            </p>

            <a class="back-link" href="<?php echo htmlspecialchars($dashboardUrl); ?>">
                &larr; Back to dashboard
            </a>

        </section>

        <section class="profile-section">

            <h1 class="profile-title">My profile</h1>
            <p class="profile-subtitle">
                <span class="role-badge"><?php echo htmlspecialchars($user["role"]); ?></span>
            </p>

            <?php if ($infoMessage !== "") { ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($infoMessage); ?></div>
            <?php } ?>

            <?php if ($errorMessage !== "") { ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php } ?>

            <div class="card">
                <h2>Account overview</h2>

                <div class="info-row">
                    <span class="info-label">Full name</span>
                    <span class="info-value"><?php echo htmlspecialchars($user["full_name"]); ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?php echo htmlspecialchars($user["email"]); ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?php echo htmlspecialchars($user["phone"] !== "" ? $user["phone"] : "—"); ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Member since</span>
                    <span class="info-value"><?php echo htmlspecialchars(date("d M Y", strtotime($user["created_at"]))); ?></span>
                </div>
            </div>

            <div class="card">
                <h2>Edit profile</h2>

                <form method="POST">
                    <input type="hidden" name="formAction" value="updateProfile">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="fullName">Full name</label>
                            <input class="form-input" type="text" id="fullName" name="fullName"
                                value="<?php echo htmlspecialchars($user["full_name"]); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-input" type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($user["email"]); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input class="form-input" type="tel" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($user["phone"]); ?>">
                    </div>

                    <button class="btn btn-primary" type="submit">Save changes</button>
                </form>
            </div>

            <div class="card">
                <h2>Change password</h2>

                <form method="POST">
                    <input type="hidden" name="formAction" value="changePassword">

                    <div class="form-group">
                        <label class="form-label" for="currentPassword">Current password</label>
                        <input class="form-input" type="password" id="currentPassword" name="currentPassword" autocomplete="current-password">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="newPassword">New password</label>
                            <input class="form-input" type="password" id="newPassword" name="newPassword" autocomplete="new-password">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirmNewPassword">Confirm new password</label>
                            <input class="form-input" type="password" id="confirmNewPassword" name="confirmNewPassword" autocomplete="new-password">
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">Update password</button>
                </form>
            </div>

            <div class="card danger-zone">
                <h2>Delete account</h2>

                <p class="danger-text">
                    This permanently removes your DoctorConnect account. This cannot be undone.
                </p>

                <form method="POST" onsubmit="return confirm('Delete your account permanently? This cannot be undone.');">
                    <input type="hidden" name="formAction" value="deleteAccount">

                    <div class="form-group">
                        <label class="form-label" for="confirmDeletePassword">Confirm your password</label>
                        <input class="form-input" type="password" id="confirmDeletePassword" name="confirmDeletePassword" autocomplete="current-password">
                    </div>

                    <button class="btn btn-danger" type="submit">Delete my account</button>
                </form>
            </div>

        </section>

    </main>

</body>

</html>
