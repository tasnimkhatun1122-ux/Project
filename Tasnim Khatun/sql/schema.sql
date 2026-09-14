-- DoctorConnect database schema (Doctor module scope)
-- Import this in phpMyAdmin, or run: mysql -u root -p < schema.sql
-- After importing this, open seed.php once in your browser to add
-- sample departments, a test doctor account, patients and appointments
-- (it hashes the password correctly with PHP's password_hash()).

CREATE DATABASE IF NOT EXISTS doctorconnect;
USE doctorconnect;

-- ---------------------------------------------------------
-- USERS  (all four roles live here; role column tells them apart)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    phone       VARCHAR(20),
    role        ENUM('patient','doctor','receptionist','admin') NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- DEPARTMENTS
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS departments (
    dept_id     INT AUTO_INCREMENT PRIMARY KEY,
    dept_name   VARCHAR(100) NOT NULL UNIQUE
);

-- ---------------------------------------------------------
-- DOCTORS  (extends a users row with role = 'doctor')
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS doctors (
    doctor_id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id             INT NOT NULL,
    dept_id             INT NOT NULL,
    specialization      VARCHAR(100),
    consultation_fee    DECIMAL(10,2),
    available_time      VARCHAR(100),
    room                VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
);

-- ---------------------------------------------------------
-- APPOINTMENTS
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    appt_id     INT AUTO_INCREMENT PRIMARY KEY,
    patient_id  INT NOT NULL,
    doctor_id   INT NOT NULL,
    appt_date   DATE NOT NULL,
    time_slot   VARCHAR(30) NOT NULL,
    status      ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    diagnosis   VARCHAR(120),
    visit_note  TEXT,
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id)  REFERENCES doctors(doctor_id)
);
