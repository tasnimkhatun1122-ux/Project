<?php

class Auth
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(array(
                "httponly" => true,
                "samesite" => "Lax",
                "path"     => "/",
            ));
            session_start();
        }
    }

    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION["user_id"]   = $user["user_id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["role"]      = $user["role"];
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    public static function check()
    {
        return isset($_SESSION["user_id"]);
    }

    public static function id()
    {
        return self::check() ? (int) $_SESSION["user_id"] : 0;
    }

    public static function role()
    {
        return self::check() ? $_SESSION["role"] : "";
    }

    public static function name()
    {
        return self::check() ? $_SESSION["full_name"] : "";
    }

    public static function dashboardFor($role)
    {
        if ($role == "admin")             { return "admin/dashboard"; }
        elseif ($role == "doctor")        { return "doctor/dashboard"; }
        elseif ($role == "receptionist")  { return "reception/dashboard"; }
        else                              { return "patient/dashboard"; }
    }

    public static function clean($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
