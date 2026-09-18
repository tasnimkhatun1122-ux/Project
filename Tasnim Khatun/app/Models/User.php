<?php
class User
{
    /** Looks up a doctor account by email (used at login). */
    public static function findDoctorByEmail($email)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT user_id, full_name, password, role
             FROM users
             WHERE email = ? AND role = 'doctor'"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
