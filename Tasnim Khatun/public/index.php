<?php
session_start();

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Core/View.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Doctor.php';
require_once __DIR__ . '/../app/Models/Appointment.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/ProfileController.php';

// Every request looks like: index.php?route=dashboard
$route = $_GET['route'] ?? 'login';

$routes = [
    'login'                 => ['AuthController', 'showLogin'],
    'login.submit'          => ['AuthController', 'login'],
    'logout'                => ['AuthController', 'logout'],
    'dashboard'             => ['DashboardController', 'index'],
    'appointment.complete'  => ['DashboardController', 'complete'],
    'profile'               => ['ProfileController', 'index'],
    'profile.update'        => ['ProfileController', 'update'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    echo "Page not found.";
    exit();
}

[$controllerName, $method] = $routes[$route];
$controller = new $controllerName();
$controller->$method();
