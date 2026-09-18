<?php

session_start();

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/AuthController.php";
require_once __DIR__ . "/../app/controllers/ReceptionistController.php";
require_once __DIR__ . "/../app/controllers/ProfileController.php";

$authController = new AuthController($conn);
$receptionistController = new ReceptionistController($conn);
$profileController = new ProfileController($conn);

$action = $_GET["action"] ?? "login";

switch ($action) {

    case "login":
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $authController->login();
        } else {
            require __DIR__ . "/../app/views/auth/login.php";
        }
        break;

    case "register":
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $authController->register();
        } else {
            require __DIR__ . "/../app/views/auth/register.php";
        }
        break;

    case "dashboard":
        $receptionistController->dashboard();
        break;

    case "bookWalkIn":
        $receptionistController->bookWalkIn();
        break;

    case "updateAppointment":
        $receptionistController->updateAppointment();
        break;

    case "profile":
        $profileController->profile();
        break;

    case "logout":
        $profileController->logout();
        break;

    default:
        http_response_code(404);
        echo "Page not found.";
        break;
}
?>
