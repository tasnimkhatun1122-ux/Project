<?php

class Appointment extends Model
{
    public function isSlotTaken($doctorId, $date, $slot)
    {
        $row = $this->db->selectOne(
            "SELECT appt_id FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND time_slot = ?
               AND status != 'cancelled'",
            array((int) $doctorId, $date, $slot)
        );
        return $row !== null;
    }

    public function takenSlots($doctorId, $date)
    {
        $rows = $this->db->select(
            "SELECT time_slot FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND status != 'cancelled'",
            array((int) $doctorId, $date)
        );

        $slots = array();
        foreach ($rows as $r) {
            $slots[] = $r["time_slot"];
        }
        return $slots;
    }

    public function book($patientId, $doctorId, $date, $slot, $status = "pending")
    {
        return $this->db->insert(
            "INSERT INTO appointments (patient_id, doctor_id, appt_date, time_slot, status)
             VALUES (?, ?, ?, ?, ?)",
            array((int) $patientId, (int) $doctorId, $date, $slot, $status)
        );
    }

    public function forPatient($patientId, $status = "")
    {
        $sql = "SELECT a.appt_id, a.appt_date, a.time_slot, a.status,
                       u.full_name AS doctor_name, dep.dept_name,
                       d.consultation_fee, d.room
                FROM appointments a
                JOIN doctors d       ON a.doctor_id = d.doctor_id
                JOIN users u         ON d.user_id = u.user_id
                JOIN departments dep ON d.dept_id = dep.dept_id
                WHERE a.patient_id = ?";

        $params = array((int) $patientId);

        if ($status != "") {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY a.appt_date DESC, a.appt_id DESC";

        return $this->db->select($sql, $params);
    }

    public function statusCountsForPatient($patientId)
    {
        $rows = $this->db->select(
            "SELECT status, COUNT(*) AS total FROM appointments
             WHERE patient_id = ? GROUP BY status",
            array((int) $patientId)
        );

        $counts = array();
        foreach ($rows as $r) {
            $counts[$r["status"]] = (int) $r["total"];
        }
        return $counts;
    }

    public function cancelByPatient($apptId, $patientId)
    {
        return $this->db->execute(
            "UPDATE appointments SET status = 'cancelled'
             WHERE appt_id = ? AND patient_id = ?
               AND status IN ('pending','confirmed')",
            array((int) $apptId, (int) $patientId)
        );
    }

    public function forDoctorOnDate($doctorId, $date)
    {
        return $this->db->select(
            "SELECT a.appt_id, a.time_slot, a.status, a.diagnosis,
                    p.full_name AS patient_name, p.phone AS patient_phone
             FROM appointments a
             JOIN users p ON a.patient_id = p.user_id
             WHERE a.doctor_id = ? AND a.appt_date = ?
             ORDER BY a.time_slot",
            array((int) $doctorId, $date)
        );
    }

    public function findForDoctor($apptId, $doctorId)
    {
        return $this->db->selectOne(
            "SELECT a.appt_id, a.appt_date, a.time_slot, a.status,
                    a.diagnosis, a.visit_note,
                    p.full_name AS patient_name, p.email AS patient_email,
                    p.phone AS patient_phone
             FROM appointments a
             JOIN users p ON a.patient_id = p.user_id
             WHERE a.appt_id = ? AND a.doctor_id = ?",
            array((int) $apptId, (int) $doctorId)
        );
    }

    public function completeVisit($apptId, $doctorId, $diagnosis, $note)
    {
        return $this->db->execute(
            "UPDATE appointments SET diagnosis = ?, visit_note = ?, status = 'completed'
             WHERE appt_id = ? AND doctor_id = ? AND status != 'cancelled'",
            array($diagnosis, $note, (int) $apptId, (int) $doctorId)
        );
    }

    public function queueForDate($date)
    {
        return $this->db->select(
            "SELECT a.appt_id, a.time_slot, a.status,
                    p.full_name AS patient_name, p.phone AS patient_phone,
                    du.full_name AS doctor_name, dep.dept_name
             FROM appointments a
             JOIN users p         ON a.patient_id = p.user_id
             JOIN doctors d       ON a.doctor_id = d.doctor_id
             JOIN users du        ON d.user_id = du.user_id
             JOIN departments dep ON d.dept_id = dep.dept_id
             WHERE a.appt_date = ?
             ORDER BY a.time_slot",
            array($date)
        );
    }

    public function setStatusIfPending($apptId, $status)
    {
        return $this->db->execute(
            "UPDATE appointments SET status = ? WHERE appt_id = ? AND status = 'pending'",
            array($status, (int) $apptId)
        );
    }

    public function countForDoctor($doctorId)
    {
        return $this->db->count(
            "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = ?",
            array((int) $doctorId)
        );
    }

    public function statusCounts()
    {
        $rows = $this->db->select(
            "SELECT status, COUNT(*) AS total FROM appointments GROUP BY status"
        );

        $counts = array();
        foreach ($rows as $r) {
            $counts[$r["status"]] = (int) $r["total"];
        }
        return $counts;
    }

    public function recent($limit = 10)
    {
        $limit = (int) $limit;
        return $this->db->select(
            "SELECT a.appt_id, a.appt_date, a.time_slot, a.status,
                    p.full_name AS patient_name, du.full_name AS doctor_name,
                    dep.dept_name
             FROM appointments a
             JOIN users p         ON a.patient_id = p.user_id
             JOIN doctors d       ON a.doctor_id = d.doctor_id
             JOIN users du        ON d.user_id = du.user_id
             JOIN departments dep ON d.dept_id = dep.dept_id
             ORDER BY a.appt_id DESC
             LIMIT $limit"
        );
    }

    public function departmentStats()
    {
        return $this->db->select(
            "SELECT dep.dept_name,
                    COUNT(DISTINCT d.doctor_id) AS doctors,
                    COUNT(a.appt_id) AS appointments
             FROM departments dep
             LEFT JOIN doctors d      ON d.dept_id = dep.dept_id
             LEFT JOIN appointments a ON a.doctor_id = d.doctor_id
             GROUP BY dep.dept_id, dep.dept_name
             ORDER BY dep.dept_name"
        );
    }
}
