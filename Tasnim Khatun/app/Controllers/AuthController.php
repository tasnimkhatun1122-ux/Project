<?php
class AuthController
{
    public function showLogin()
    {
        $error = $_SESSION['login_error'] ?? '';
        unset($_SESSION['login_error']);
        view('auth/login', ['error' => $error]);
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['login_error'] = "Please enter both email and password.";
            header("Location: index.php?route=login");
            exit();
        }

        $user = User::findDoctorByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            header("Location: index.php?route=dashboard");
            exit();
        }

        $_SESSION['login_error'] = $user ? "Incorrect password." : "No doctor account found with that email.";
        header("Location: index.php?route=login");
        exit();
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?route=login");
        exit();
    }
}
