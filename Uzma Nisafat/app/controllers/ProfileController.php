<?php

require_once __DIR__ . "/../models/User.php";

class ProfileController
{
    private $userModel;

    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }

    private function authorize()
    {
        if (
            !isset($_SESSION["user_id"]) ||
            $_SESSION["role"] !== "receptionist"
        ) {
            header("Location: index.php?action=login");
            exit;
        }
    }

    public function profile()
    {
        $this->authorize();

        $userId = $_SESSION["user_id"];

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

                } elseif (
                    $phone !== "" &&
                    !preg_match("/^[0-9+\-\s]{7,20}$/", $phone)
                ) {
                    $errorMessage = "Please enter a valid phone number.";

                } elseif ($this->userModel->emailExistsForOtherUser($email, $userId)) {
                    $errorMessage =
                        "That email is already used by another account.";

                } else {

                    if (
                        $this->userModel->updateProfile(
                            $userId,
                            $fullName,
                            $email,
                            $phone
                        )
                    ) {
                        $_SESSION["full_name"] = $fullName;
                        $_SESSION["email"] = $email;

                        $infoMessage = "Profile updated.";
                    } else {
                        $errorMessage =
                            "Could not update your profile.";
                    }
                }

            } elseif ($formAction === "changePassword") {

                $currentPassword =
                    $_POST["currentPassword"] ?? "";

                $newPassword =
                    $_POST["newPassword"] ?? "";

                $confirmNewPassword =
                    $_POST["confirmNewPassword"] ?? "";

                $user = $this->userModel->findPasswordById($userId);

                if (
                    !$user ||
                    !password_verify(
                        $currentPassword,
                        $user["password"]
                    )
                ) {
                    $errorMessage =
                        "Your current password is incorrect.";

                } elseif (strlen($newPassword) < 6) {
                    $errorMessage =
                        "New password must be at least 6 characters.";

                } elseif ($newPassword !== $confirmNewPassword) {
                    $errorMessage =
                        "New passwords do not match.";

                } else {

                    if (
                        $this->userModel->updatePassword(
                            $userId,
                            $newPassword
                        )
                    ) {
                        $infoMessage = "Password changed.";
                    } else {
                        $errorMessage =
                            "Could not change your password.";
                    }
                }

            } elseif ($formAction === "deleteAccount") {

                $confirmPassword =
                    $_POST["confirmDeletePassword"] ?? "";

                $user =
                    $this->userModel->findPasswordById($userId);

                if (
                    !$user ||
                    !password_verify(
                        $confirmPassword,
                        $user["password"]
                    )
                ) {
                    $errorMessage =
                        "Incorrect password. Account was not deleted.";

                } else {

                    if ($this->userModel->deleteUser($userId)) {

                        $_SESSION = [];
                        session_destroy();

                        header("Location: index.php?action=login");
                        exit;

                    } else {
                        $errorMessage =
                            "Could not delete your account.";
                    }
                }
            }
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION = [];
            session_destroy();

            header("Location: index.php?action=login");
            exit;
        }

        require __DIR__ . "/../views/receptionist/profile.php";
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();

        header("Location: index.php?action=login");
        exit;
    }
}
?>
