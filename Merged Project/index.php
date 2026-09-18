<?php

require_once __DIR__ . "/config/config.php";

require_once APP_PATH . "/core/Icons.php";
require_once APP_PATH . "/core/Database.php";
require_once APP_PATH . "/core/Model.php";
require_once APP_PATH . "/core/Auth.php";
require_once APP_PATH . "/core/Controller.php";
require_once APP_PATH . "/core/Router.php";

Auth::start();

$url = isset($_GET["url"]) ? $_GET["url"] : "";

$router = new Router();
$router->dispatch($url);
