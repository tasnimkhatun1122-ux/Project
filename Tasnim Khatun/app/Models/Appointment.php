<?php
class Appointment
{
    /** Counts of each status for a doctor on a given date. */
    public static function getStatusCounts($doctorId, $date)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT status, COUNT(*) AS cnt
             FROM appointments
             WHERE doctor_id = ? AND appt_date = ?
             GROUP BY status"
        );
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();

        $counts = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = (int) $row['cnt'];
        }
        return $counts;
    }

    /** All appointments for a doctor on a given date, with patient info, time-ordered. */
    public static function getByDoctorAndDate($doctorId, $date)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT a.appt_id, a.time_slot, a.status, a.diagnosis, a.visit_note,
                    u.full_name AS patient_name, u.phone
             FROM appointments a
             JOIN users u ON u.user_id = a.patient_id
             WHERE a.doctor_id = ? AND a.appt_date = ?
             ORDER BY STR_TO_DATE(a.time_slot, '%h:%i %p')"
        );
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        return $stmt->get_result();
    }

    /** Ownership check so a doctor can only complete their own appointments. */
    public static function belongsToDoctor($apptId, $doctorUserId)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT a.appt_id
             FROM appointments a
             JOIN doctors d ON d.doctor_id = a.doctor_id
             WHERE a.appt_id = ? AND d.user_id = ?"
        );
        $stmt->bind_param("ii", $apptId, $doctorUserId);
        $stmt->execute();
        return (bool) $stmt->get_result()->fetch_assoc();
    }

    public static function markCompleted($apptId, $diagnosis, $visitNote)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "UPDATE appointments
             SET status = 'completed', diagnosis = ?, visit_note = ?
             WHERE appt_id = ?"
        );
        $stmt->bind_param("ssi", $diagnosis, $visitNote, $apptId);
        return $stmt->execute();
    }
}
