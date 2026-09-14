<?php

require_once __DIR__ . '/app/Config/Database.php';
$conn = Database::getConnection();

$messages = [];

// 1. Department
$conn->query("INSERT IGNORE INTO departments (dept_id, dept_name) VALUES (1, 'Cardiology')");
$messages[] = "Department 'Cardiology' ready.";

// 2. Doctor user account
$doctorPassword = password_hash("doctor123", PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO users (user_id, full_name, email, password, phone, role) VALUES (1, ?, ?, ?, ?, 'doctor')");
$name = "Dr. Tasnim Khatun Mim";
$email = "doctor@doctorconnect.test";
$phone = "01700000000";
$stmt->bind_param("ssss", $name, $email, $doctorPassword, $phone);
$stmt->execute();
$messages[] = "Doctor account ready — email: doctor@doctorconnect.test / password: doctor123";

// 3. Doctor profile row
$conn->query("INSERT IGNORE INTO doctors (doctor_id, user_id, dept_id, specialization, consultation_fee, available_time, room)
              VALUES (1, 1, 1, 'Cardiologist', 800.00, '9:00 AM - 2:00 PM', 'R-204')");
$messages[] = "Doctor profile ready.";

// 4. Sample patients
$patientPassword = password_hash("patient123", PASSWORD_DEFAULT);
$patients = [
    [2, "Nilima Salam", "nilima@doctorconnect.test", "01711111111"],
    [3, "Rahim Uddin", "rahim@doctorconnect.test", "01722222222"],
];
$stmt = $conn->prepare("INSERT IGNORE INTO users (user_id, full_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?, 'patient')");
foreach ($patients as $p) {
    $stmt->bind_param("issss", $p[0], $p[1], $p[2], $patientPassword, $p[3]);
    $stmt->execute();
}
$messages[] = "Sample patients ready.";

// 5. Sample appointments for today
$today = date("Y-m-d");
$conn->query("INSERT IGNORE INTO appointments (appt_id, patient_id, doctor_id, appt_date, time_slot, status) VALUES
    (1, 2, 1, '$today', '09:30 AM', 'pending'),
    (2, 3, 1, '$today', '10:00 AM', 'confirmed'),
    (3, 2, 1, '$today', '11:00 AM', 'completed')");
$messages[] = "Sample appointments for today ready.";

echo "<h2>DoctorConnect (MVC) — seed complete</h2><ul>";
foreach ($messages as $m) {
    echo "<li>$m</li>";
}
echo "</ul><p><a href='public/index.php?route=login'>Go to login</a></p>";
echo "<p style='color:#b00'>For security, delete seed.php now that setup is done.</p>";
