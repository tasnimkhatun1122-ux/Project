<?php

class Appointment
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findPatientByPhone($phone)
    {
        $sql = "SELECT user_id, full_name
                FROM users
                WHERE phone = ? AND role = 'patient'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $phone);
        $stmt->execute();

        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();

        $stmt->close();

        return $patient;
    }

    public function createWalkInPatient($name, $phone)
    {
        $digitsOnly = preg_replace("/[^0-9]/", "", $phone);

        $placeholderEmail =
            "walkin" . $digitsOnly . time() . "@doctorconnect.local";

        $placeholderPassword = password_hash(
            bin2hex(random_bytes(8)),
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users
                (full_name, email, phone, password, role)
                VALUES (?, ?, ?, ?, 'patient')";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssss",
            $name,
            $placeholderEmail,
            $phone,
            $placeholderPassword
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $patientId = $stmt->insert_id;

        $stmt->close();

        return $patientId;
    }

    public function createWalkInAppointment(
        $patientId,
        $doctorId,
        $date,
        $timeSlot
    ) {
        $sql = "INSERT INTO appointments
                (patient_id, doctor_id, appointment_date, appointment_time, status)
                VALUES (?, ?, ?, ?, 'pending')";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iiss",
            $patientId,
            $doctorId,
            $date,
            $timeSlot
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $appointmentId = $stmt->insert_id;

        $stmt->close();

        return $appointmentId;
    }

    public function updateStatus($apptId, $action)
    {
        $newStatus = null;
        $requiredCurrent = null;

        if ($action === "checkin") {
            $newStatus = "checked_in";
            $requiredCurrent = "pending";
        } elseif ($action === "noshow") {
            $newStatus = "no_show";
            $requiredCurrent = "pending";
        } elseif ($action === "rebook") {
            $newStatus = "pending";
            $requiredCurrent = "no_show";
        } else {
            return false;
        }

        $sql = "UPDATE appointments
                SET status = ?
                WHERE appointment_id = ? AND status = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sis",
            $newStatus,
            $apptId,
            $requiredCurrent
        );

        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $stmt->close();
            return false;
        }

        $stmt->close();

        return $newStatus;
    }
}
?>
