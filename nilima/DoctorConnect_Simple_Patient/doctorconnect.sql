CREATE DATABASE IF NOT EXISTS doctorconnect;
USE doctorconnect;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    role ENUM('patient','doctor','receptionist','admin') NOT NULL
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    consultation_fee DECIMAL(10,2) NOT NULL,
    visiting_time VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending','completed','cancelled') DEFAULT 'pending',
    diagnosis TEXT NULL,
    visit_note TEXT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

INSERT INTO users (name,email,password,role)
VALUES ('Nilima Salam','nilima@example.com','123456','patient');

INSERT INTO departments (name) VALUES
('Cardiology'),
('Medicine'),
('Dermatology'),
('Neurology');

INSERT INTO doctors
(name,specialization,consultation_fee,visiting_time,department_id)
VALUES
('Dr. Sarah Ahmed','Cardiologist',800,'Sat-Thu, 9:00 AM - 1:00 PM',1),
('Dr. Farhan Karim','Medicine Specialist',600,'Sun-Thu, 4:00 PM - 8:00 PM',2),
('Dr. Nusrat Jahan','Dermatologist',700,'Sat-Wed, 10:00 AM - 2:00 PM',3),
('Dr. Rahim Hasan','Neurologist',900,'Sun-Thu, 6:00 PM - 9:00 PM',4);

INSERT INTO appointments
(patient_id,doctor_id,appointment_date,appointment_time,status)
VALUES
(1,1,'2026-09-02','10:30:00','pending'),
(1,2,'2026-08-20','17:00:00','completed'),
(1,1,'2026-08-12','11:00:00','completed');
