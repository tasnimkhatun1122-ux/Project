<?php

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (!$this->conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->conn, "utf8mb4");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function connection()
    {
        return $this->conn;
    }

    private function bind($stmt, $params)
    {
        if (empty($params)) {
            return;
        }

        $types = "";
        foreach ($params as $p) {
            if (is_int($p))        { $types .= "i"; }
            elseif (is_float($p))  { $types .= "d"; }
            else                   { $types .= "s"; }
        }

        $args = array($stmt, $types);
        foreach ($params as $key => $value) {
            $args[] = &$params[$key];
        }

        call_user_func_array("mysqli_stmt_bind_param", $args);
    }

    public function select($sql, $params = array())
    {
        $stmt = mysqli_prepare($this->conn, $sql);
        $this->bind($stmt, $params);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $rows   = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function selectOne($sql, $params = array())
    {
        $rows = $this->select($sql, $params);
        return count($rows) > 0 ? $rows[0] : null;
    }

    public function execute($sql, $params = array())
    {
        $stmt = mysqli_prepare($this->conn, $sql);
        $this->bind($stmt, $params);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function insert($sql, $params = array())
    {
        $this->execute($sql, $params);
        return mysqli_insert_id($this->conn);
    }

    public function count($sql, $params = array())
    {
        $row = $this->selectOne($sql, $params);
        if ($row === null) {
            return 0;
        }
        return (int) array_values($row)[0];
    }
}
