<?php

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            $this->redirect(Auth::dashboardFor(Auth::role()));
        }

        $error = "";
        $email = "";

        if ($this->isPost()) {
            $email    = $this->input("email");
            $password = isset($_POST["password"]) ? $_POST["password"] : "";

            if ($email == "" || $password == "") {
                $error = "Please fill in both fields.";
            } else {
                $userModel = $this->model("User");
                $user      = $userModel->findByEmail($email);

                if ($user && $userModel->verifyPassword($password, $user["password"])) {
                    Auth::login($user);
                    $this->redirect(Auth::dashboardFor($user["role"]));
                } else {
                    $error = "Wrong email or password.";
                }
            }
        }

        $this->view("auth/login", array(
            "pageTitle" => "Login",
            "error"     => $error,
            "email"     => $email,
        ), "auth");
    }

    public function register()
    {
        if (Auth::check()) {
            $this->redirect(Auth::dashboardFor(Auth::role()));
        }

        $error = "";
        $name  = "";
        $email = "";
        $phone = "";

        if ($this->isPost()) {
            $name     = $this->input("full_name");
            $email    = $this->input("email");
            $phone    = $this->input("phone");
            $password = isset($_POST["password"]) ? $_POST["password"] : "";
            $confirm  = isset($_POST["confirm"])  ? $_POST["confirm"]  : "";

            $userModel = $this->model("User");

            if ($name == "" || $email == "" || $password == "") {
                $error = "Name, email and password are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
            } elseif (strlen($password) < 4) {
                $error = "Password must be at least 4 characters.";
            } elseif ($password !== $confirm) {
                $error = "The two passwords do not match.";
            } elseif ($userModel->emailExists($email)) {
                $error = "An account with that email already exists.";
            } else {
                $userModel->create($name, $email, $password, $phone, "patient");
                $this->redirect("auth/login");
            }
        }

        $this->view("auth/register", array(
            "pageTitle" => "Register",
            "error"     => $error,
            "name"      => $name,
            "email"     => $email,
            "phone"     => $phone,
        ), "auth");
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect("auth/login");
    }
}
