<?php

class Router
{
    public function dispatch($url)
    {
        $url = trim($url, "/");

        if ($url === "") {
            $parts = array(DEFAULT_CONTROLLER, DEFAULT_ACTION);
        } else {
            $parts = explode("/", filter_var($url, FILTER_SANITIZE_URL));
        }

        $controllerName = ucfirst(strtolower($parts[0])) . "Controller";
        $actionName     = isset($parts[1]) ? strtolower($parts[1]) : DEFAULT_ACTION;
        $params         = array_slice($parts, 2);

        $file = APP_PATH . "/controllers/" . $controllerName . ".php";

        if (!file_exists($file)) {
            http_response_code(404);
            die("Controller not found: " . htmlspecialchars($controllerName));
        }

        require_once $file;
        $controller = new $controllerName();

        if (!method_exists($controller, $actionName)) {
            http_response_code(404);
            die("Action not found: " . htmlspecialchars($actionName));
        }

        call_user_func_array(array($controller, $actionName), $params);
    }
}
