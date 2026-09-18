<?php

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT user_id, full_name, email, password, role
                FROM users
                WHERE email = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        return $user;
    }

    public function findById($userId)
    {
        $sql = "SELECT full_name, email, phone, role, created_at
                FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        return $user;
    }

    public function findPasswordById($userId)
    {
        $sql = "SELECT password
                FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        return $user;
    }

    public function emailExists($email)
    {
        $sql = "SELECT user_id
                FROM users
                WHERE email = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    public function emailExistsForOtherUser($email, $userId)
    {
        $sql = "SELECT user_id
                FROM users
                WHERE email = ?
                AND user_id != ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    public function createUser(
        $fullName,
        $email,
        $phone,
        $password,
        $role
    ) {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
            INSERT INTO users
            (full_name, email, phone, password, role)
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sssss",
            $fullName,
            $email,
            $phone,
            $hashedPassword,
            $role
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function updateProfile(
        $userId,
        $fullName,
        $email,
        $phone
    ) {
        $sql = "UPDATE users
                SET full_name = ?,
                    email = ?,
                    phone = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sssi",
            $fullName,
            $email,
            $phone,
            $userId
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

        $sql = "UPDATE users
                SET password = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $hashedPassword,
            $userId
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    public function deleteUser($userId)
    {
        $sql = "DELETE FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $userId);

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }
}
?>
