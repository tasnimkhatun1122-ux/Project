<?php

class Department extends Model
{
    public function all()
    {
        return $this->db->select("SELECT * FROM departments ORDER BY dept_name");
    }

    public function countAll()
    {
        return $this->db->count("SELECT COUNT(*) AS total FROM departments");
    }

    public function findByName($name)
    {
        return $this->db->selectOne(
            "SELECT dept_id FROM departments WHERE dept_name = ?",
            array($name)
        );
    }

    public function findById($id)
    {
        return $this->db->selectOne(
            "SELECT * FROM departments WHERE dept_id = ?",
            array((int) $id)
        );
    }

    public function create($name)
    {
        return $this->db->insert(
            "INSERT INTO departments (dept_name) VALUES (?)",
            array($name)
        );
    }

    public function update($id, $name)
    {
        return $this->db->execute(
            "UPDATE departments SET dept_name = ? WHERE dept_id = ?",
            array($name, (int) $id)
        );
    }

    public function delete($id)
    {
        return $this->db->execute(
            "DELETE FROM departments WHERE dept_id = ?",
            array((int) $id)
        );
    }

    public function doctorCount($deptId)
    {
        return $this->db->count(
            "SELECT COUNT(*) AS total FROM doctors WHERE dept_id = ?",
            array((int) $deptId)
        );
    }

    public function withDoctorCounts()
    {
        return $this->db->select(
            "SELECT dep.dept_id, dep.dept_name, COUNT(d.doctor_id) AS doctors
             FROM departments dep
             LEFT JOIN doctors d ON d.dept_id = dep.dept_id
             GROUP BY dep.dept_id, dep.dept_name
             ORDER BY dep.dept_name"
        );
    }
}
