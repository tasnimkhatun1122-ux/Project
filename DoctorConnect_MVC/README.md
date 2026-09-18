# DoctorConnect — MVC PHP Project

Built from the supplied project proposal: HTML, CSS, JavaScript, PHP, MySQL/XAMPP; four roles: Admin, Doctor, Patient, Receptionist. The proposal specifies authentication, profiles, dashboards, doctor search/booking/history, doctor schedules/visit notes, receptionist queue/check-in/no-show/walk-in, and admin management.

## Root structure
Only three directories are used: `model/`, `views/`, `controllers/`. CSS and JS are directly inside `views/`; there are no assets/config/database/uploads/role subfolders. Root files are the front controller, SQL and README.

## Setup
1. Start Apache + MySQL in XAMPP.
2. Import `doctorconnect.sql` in phpMyAdmin.
3. Put this folder in `C:/xampp/htdocs/`.
4. Open `http://localhost/DoctorConnect/`.

## Demo accounts
Admin `admin@doctorconnect.com` / `admin123`
Doctor `doctor@doctorconnect.com` / `doctor123`
Patient `patient@doctorconnect.com` / `patient123`
Receptionist `reception@doctorconnect.com` / `reception123`

## MVC
Model = PDO + data access. Controllers = routing workflow, validation and authorization. Views = HTML/CSS/JS presentation. `index.php` is the root front controller.
