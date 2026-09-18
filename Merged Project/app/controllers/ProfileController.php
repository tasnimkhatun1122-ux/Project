<?php

class ProfileController extends Controller
{
    public function index()
    {
        $this->requireLogin();

        $userModel = $this->model("User");

        $error   = "";
        $success = "";

        if ($this->isPost()) {
            $action = $this->input("action");

            if ($action == "details") {
                $name  = $this->input("full_name");
                $email = $this->input("email");
                $phone = $this->input("phone");

                if ($name == "" || $email == "") {
                    $error = "Name and email are required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Please enter a valid email address.";
                } elseif ($userModel->emailExists($email, Auth::id())) {
                    $error = "Another account already uses that email.";
                } else {
                    $userModel->updateDetails(Auth::id(), $name, $email, $phone);
                    $_SESSION["full_name"] = $name;
                    $success = "Your details have been updated.";
                }

            } elseif ($action == "password") {
                $current = isset($_POST["current_password"]) ? $_POST["current_password"] : "";
                $new     = isset($_POST["new_password"])     ? $_POST["new_password"]     : "";
                $confirm = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";

                $hash = $userModel->currentPasswordHash(Auth::id());

                if (!password_verify($current, $hash)) {
                    $error = "Your current password is not correct.";
                } elseif (strlen($new) < 4) {
                    $error = "The new password must be at least 4 characters.";
                } elseif ($new !== $confirm) {
                    $error = "The two new passwords do not match.";
                } else {
                    $userModel->updatePassword(Auth::id(), $new);
                    $success = "Your password has been changed.";
                }
            }
        }

        $this->view("profile/index", array(
            "pageTitle" => "My Account",
            "user"      => $userModel->findById(Auth::id()),
            "error"     => $error,
            "success"   => $success,
        ));
    }
}
