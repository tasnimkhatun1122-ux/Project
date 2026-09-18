<?php

require_once __DIR__ . "/../models/User.php";

class AuthController
{
    private $userModel;

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }

    public function login()
    {
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

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            http_response_code(401);
            die("Invalid email or password.");
        }

        if (!password_verify($password, $user["password"])) {
            http_response_code(401);
            die("Invalid email or password.");
        }

        if ($user["role"] !== "receptionist") {
            http_response_code(403);
            die("You are not authorized to access the receptionist dashboard.");
        }

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];

        header("Location: ../public/index.php?action=dashboard");
        exit;
    }

    public function register()
    {
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

        if (
            $phone !== "" &&
            !preg_match("/^[0-9+\-\s]{7,20}$/", $phone)
        ) {
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

        if ($this->userModel->emailExists($email)) {
            die("An account with this email already exists.");
        }

        if ($this->userModel->createUser(
            $fullName,
            $email,
            $phone,
            $password,
            $role
        )) {
            echo "Account created successfully!";
        } else {
            echo "Registration failed.";
        }
    }
}
?>
