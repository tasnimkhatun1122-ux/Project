<?php
class Doctor
{
    /** Full profile (doctor + department + account) for the logged-in user. */
    public static function findByUserId($userId)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT d.doctor_id, d.specialization, d.consultation_fee, d.available_time, d.room,
                    dep.dept_name, u.full_name, u.email, u.phone
             FROM doctors d
             JOIN departments dep ON dep.dept_id = d.dept_id
             JOIN users u ON u.user_id = d.user_id
             WHERE d.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function updateProfile($doctorId, $specialization, $fee, $availableTime, $room)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "UPDATE doctors
             SET specialization = ?, consultation_fee = ?, available_time = ?, room = ?
             WHERE doctor_id = ?"
        );
        $stmt->bind_param("sdssi", $specialization, $fee, $availableTime, $room, $doctorId);
        return $stmt->execute();
    }
}
