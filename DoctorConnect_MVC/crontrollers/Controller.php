<?php

abstract class Controller
{
    protected function model($name)
    {
        require_once APP_PATH . "/models/" . $name . ".php";
        return new $name();
    }

    protected function view($path, $data = array(), $layout = "app")
    {
        extract($data);

        $viewFile = VIEW_PATH . "/" . $path . ".php";

        if (!file_exists($viewFile)) {
            die("View not found: " . $path);
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require VIEW_PATH . "/layouts/" . $layout . ".php";
    }

    protected function redirect($route)
    {
        header("Location: " . BASE_URL . "/index.php?url=" . $route);
        exit();
    }

    protected function requireLogin()
    {
        if (!Auth::check()) {
            $this->redirect("auth/login");
        }
    }

    protected function requireRole($role)
    {
        $this->requireLogin();

        if (Auth::role() != $role) {
            $this->redirect(Auth::dashboardFor(Auth::role()));
        }
    }

    protected function requireAnyRole($roles)
    {
        $this->requireLogin();

        if (!in_array(Auth::role(), $roles)) {
            $this->redirect(Auth::dashboardFor(Auth::role()));
        }
    }

    protected function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    protected function input($key, $default = "")
    {
        return isset($_POST[$key]) ? Auth::clean($_POST[$key]) : $default;
    }

    protected function query($key, $default = "")
    {
        return isset($_GET[$key]) ? Auth::clean($_GET[$key]) : $default;
    }
}
