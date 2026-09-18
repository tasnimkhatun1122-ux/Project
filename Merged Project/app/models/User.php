<?php

class User extends Model
{
    public function findByEmail($email)
    {
        return $this->db->selectOne(
            "SELECT user_id, full_name, email, password, phone, role
             FROM users WHERE email = ?",
            array($email)
        );
    }

    public function findById($id)
    {
        return $this->db->selectOne(
            "SELECT user_id, full_name, email, phone, role, created_at
             FROM users WHERE user_id = ?",
            array((int) $id)
        );
    }

    public function emailExists($email, $exceptId = 0)
    {
        if ($exceptId > 0) {
            return $this->db->selectOne(
                "SELECT user_id FROM users WHERE email = ? AND user_id != ?",
                array($email, (int) $exceptId)
            ) !== null;
        }

        return $this->db->selectOne(
            "SELECT user_id FROM users WHERE email = ?",
            array($email)
        ) !== null;
    }

    public function create($name, $email, $plainPassword, $phone, $role = "patient")
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        return $this->db->insert(
            "INSERT INTO users (full_name, email, password, phone, role)
             VALUES (?, ?, ?, ?, ?)",
            array($name, $email, $hash, $phone, $role)
        );
    }

    public function verifyPassword($plain, $hash)
    {
        return password_verify($plain, $hash);
    }

    public function updateDetails($id, $name, $email, $phone)
    {
        return $this->db->execute(
            "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?",
            array($name, $email, $phone, (int) $id)
        );
    }

    public function updatePassword($id, $plainPassword)
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        return $this->db->execute(
            "UPDATE users SET password = ? WHERE user_id = ?",
            array($hash, (int) $id)
        );
    }

    public function currentPasswordHash($id)
    {
        $row = $this->db->selectOne(
            "SELECT password FROM users WHERE user_id = ?",
            array((int) $id)
        );
        return $row ? $row["password"] : null;
    }

    public function allPatients()
    {
        return $this->db->select(
            "SELECT user_id, full_name, phone FROM users
             WHERE role = 'patient' ORDER BY full_name"
        );
    }

    public function delete($id)
    {
        return $this->db->execute(
            "DELETE FROM users WHERE user_id = ?",
            array((int) $id)
        );
    }
}
