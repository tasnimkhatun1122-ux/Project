-- DoctorConnect - demo data
-- Run after schema.sql:  mysql -u root < seed.sql
-- Every account below uses the password: 1234

USE doctorconnect_db;

DELETE FROM appointments;
DELETE FROM doctors;
DELETE FROM users;
DELETE FROM departments;

INSERT INTO departments (dept_id, dept_name) VALUES
  (1,'Cardiology'), (2,'Medicine'), (3,'Orthopedics'),
  (4,'Gynecology'), (5,'ENT'), (6,'Neurology');

-- password for all accounts is: 1234
SET @pw = '$2y$10$.GwJJMtC1mYuQ5CNeBtfM.TpmRZeRGssIjF6qnQKi4FuYWtlD7ow2';

INSERT INTO users (user_id, full_name, email, password, phone, role) VALUES
  (1,'System Admin',     'admin@doctorconnect.com',     @pw,'01700000000','admin'),
  (2,'Front Desk',       'reception@doctorconnect.com', @pw,'01766666666','receptionist'),
  (3,'Dr. Salma Akter',  'salma@doctorconnect.com',     @pw,'01711111111','doctor'),
  (4,'Dr. Rahim Uddin',  'rahim@doctorconnect.com',     @pw,'01822222222','doctor'),
  (5,'Dr. Tanvir Hasan', 'tanvir@doctorconnect.com',    @pw,'01933333333','doctor'),
  (6,'Dr. Farhana Yasmin','farhana@doctorconnect.com',  @pw,'01611111111','doctor'),
  (7,'Nusrat Jahan',     'nusrat@gmail.com',            @pw,'01644444444','patient'),
  (8,'Hasan Mahmud',     'hasan@gmail.com',             @pw,'01555555555','patient'),
  (9,'Kamrul Hasan',     'kamrul@gmail.com',            @pw,'01712345678','patient'),
  (10,'Farida Begum',    'farida@gmail.com',            @pw,'01911334455','patient'),
  (11,'Tanzim Rahman',   'tanzim@gmail.com',            @pw,'01877665544','patient');

INSERT INTO doctors (doctor_id, user_id, dept_id, specialization, consultation_fee, available_time, room) VALUES
  (1,3,1,'Heart Specialist, MBBS, MD',      800.00,'Sun-Thu, 5 PM - 8 PM','304'),
  (2,4,1,'Cardiology, MBBS',                600.00,'Sat-Wed, 6 PM - 9 PM','306'),
  (3,5,3,'Orthopedic Surgeon, MBBS, FCPS', 1000.00,'Fri, 4 PM - 7 PM',    '210'),
  (4,6,2,'Medicine, MBBS, FCPS',            700.00,'Sun-Thu, 9 AM - 12 PM','112');

INSERT INTO appointments (patient_id, doctor_id, appt_date, time_slot, status, diagnosis, visit_note) VALUES
  (7,1,CURDATE(),'9:00 AM - 9:30 AM','completed','Hypertension, stage 1','BP 148/92. Started Amlodipine 5mg daily. Low-salt diet advised. Review in 4 weeks.'),
  (8,1,CURDATE(),'10:00 AM - 10:30 AM','completed','Stable angina pectoris','Chest tightness on exertion, relieved by rest. ECG unremarkable.'),
  (9,1,CURDATE(),'5:00 PM - 5:30 PM','confirmed',NULL,NULL),
  (10,1,CURDATE(),'6:00 PM - 6:30 PM','pending',NULL,NULL),
  (11,4,CURDATE(),'9:00 AM - 9:30 AM','confirmed',NULL,NULL),
  (7,2,DATE_ADD(CURDATE(),INTERVAL 2 DAY),'6:00 PM - 6:30 PM','pending',NULL,NULL),
  (8,3,DATE_ADD(CURDATE(),INTERVAL 3 DAY),'4:00 PM - 4:30 PM','confirmed',NULL,NULL),
  (9,4,DATE_SUB(CURDATE(),INTERVAL 5 DAY),'11:00 AM - 11:30 AM','completed','Type 2 diabetes, newly diagnosed','Fasting glucose 9.1 mmol/L. Started Metformin 500mg twice daily.'),
  (10,1,DATE_SUB(CURDATE(),INTERVAL 8 DAY),'7:00 PM - 7:30 PM','completed','Dyslipidaemia','LDL 171. Started Atorvastatin 10mg at night.'),
  (11,2,DATE_SUB(CURDATE(),INTERVAL 12 DAY),'6:00 PM - 6:30 PM','cancelled',NULL,NULL);
