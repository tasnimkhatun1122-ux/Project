<?php
class Auth
{
    public static function check()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
            header("Location: index.php?route=login");
            exit();
        }
    }
}
