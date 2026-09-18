<?php

class Doctor
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findById($doctorId)
    {
        $sql = "SELECT d.doctor_id, u.full_name
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                WHERE d.doctor_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $doctorId);
        $stmt->execute();

        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();

        $stmt->close();

        return $doctor;
    }

    public function getAll()
    {
        $sql = "SELECT d.doctor_id, u.full_name
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                ORDER BY u.full_name";

        $result = $this->conn->query($sql);

        if (!$result) {
            return [];
        }

        $doctors = [];

        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }

        return $doctors;
    }
}
?>
