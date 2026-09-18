<?php

class Doctor extends Model
{
    public function all()
    {
        return $this->db->select(
            "SELECT d.doctor_id, d.dept_id, d.specialization, d.consultation_fee,
                    d.available_time, d.room,
                    u.user_id, u.full_name, u.email, u.phone, dep.dept_name
             FROM doctors d
             JOIN users u        ON d.user_id = u.user_id
             JOIN departments dep ON d.dept_id = dep.dept_id
             ORDER BY u.full_name"
        );
    }

    public function allByDepartment($deptId = 0)
    {
        if ($deptId > 0) {
            return $this->db->select(
                "SELECT d.doctor_id, u.full_name, dep.dept_name, d.specialization,
                        d.consultation_fee, d.available_time, d.room
                 FROM doctors d
                 JOIN users u         ON d.user_id = u.user_id
                 JOIN departments dep ON d.dept_id = dep.dept_id
                 WHERE d.dept_id = ?
                 ORDER BY u.full_name",
                array((int) $deptId)
            );
        }

        return $this->db->select(
            "SELECT d.doctor_id, u.full_name, dep.dept_name, d.specialization,
                    d.consultation_fee, d.available_time, d.room
             FROM doctors d
             JOIN users u         ON d.user_id = u.user_id
             JOIN departments dep ON d.dept_id = dep.dept_id
             ORDER BY u.full_name"
        );
    }

    public function findById($doctorId)
    {
        return $this->db->selectOne(
            "SELECT d.doctor_id, d.user_id, d.dept_id, d.specialization,
                    d.consultation_fee, d.available_time, d.room,
                    u.full_name, u.email, u.phone, dep.dept_name
             FROM doctors d
             JOIN users u         ON d.user_id = u.user_id
             JOIN departments dep ON d.dept_id = dep.dept_id
             WHERE d.doctor_id = ?",
            array((int) $doctorId)
        );
    }

    public function findByUserId($userId)
    {
        return $this->db->selectOne(
            "SELECT * FROM doctors WHERE user_id = ?",
            array((int) $userId)
        );
    }

    public function countAll()
    {
        return $this->db->count("SELECT COUNT(*) AS total FROM doctors");
    }

    public function create($userId, $deptId, $spec, $fee, $time, $room)
    {
        return $this->db->insert(
            "INSERT INTO doctors (user_id, dept_id, specialization,
                                  consultation_fee, available_time, room)
             VALUES (?, ?, ?, ?, ?, ?)",
            array((int) $userId, (int) $deptId, $spec, $fee, $time, $room)
        );
    }

    public function update($doctorId, $deptId, $spec, $fee, $time, $room)
    {
        return $this->db->execute(
            "UPDATE doctors SET dept_id = ?, specialization = ?, consultation_fee = ?,
                    available_time = ?, room = ?
             WHERE doctor_id = ?",
            array((int) $deptId, $spec, $fee, $time, $room, (int) $doctorId)
        );
    }

    public function updateOwnProfile($doctorId, $spec, $fee, $time, $room)
    {
        return $this->db->execute(
            "UPDATE doctors SET specialization = ?, consultation_fee = ?,
                    available_time = ?, room = ?
             WHERE doctor_id = ?",
            array($spec, $fee, $time, $room, (int) $doctorId)
        );
    }

    public function delete($doctorId)
    {
        return $this->db->execute(
            "DELETE FROM doctors WHERE doctor_id = ?",
            array((int) $doctorId)
        );
    }

    public function departmentSummary()
    {
        return $this->db->select(
            "SELECT dep.dept_name, COUNT(d.doctor_id) AS doctor_count
             FROM departments dep
             LEFT JOIN doctors d ON dep.dept_id = d.dept_id
             GROUP BY dep.dept_id, dep.dept_name
             ORDER BY dep.dept_name"
        );
    }
}
