USE doctorconnect;

-- A few more patients (skip if they already exist)
INSERT INTO users (full_name, email, password, phone, role)
SELECT * FROM (SELECT
    'Farhana Akter' AS full_name,
    'farhana@doctorconnect.test' AS email,
    (SELECT password FROM users WHERE email = 'nilima@doctorconnect.test') AS password,
    '01733333333' AS phone,
    'patient' AS role
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'farhana@doctorconnect.test');

INSERT INTO users (full_name, email, password, phone, role)
SELECT * FROM (SELECT
    'Kamal Hossain' AS full_name,
    'kamal@doctorconnect.test' AS email,
    (SELECT password FROM users WHERE email = 'nilima@doctorconnect.test') AS password,
    '01744444444' AS phone,
    'patient' AS role
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'kamal@doctorconnect.test');

-- Past appointments (already happened — mostly completed, one cancelled)
INSERT INTO appointments (patient_id, doctor_id, appt_date, time_slot, status, diagnosis, visit_note)
VALUES
((SELECT user_id FROM users WHERE email='nilima@doctorconnect.test'), 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '09:00 AM', 'completed', 'Seasonal flu', 'Prescribed rest and fluids, follow up if fever persists.'),
((SELECT user_id FROM users WHERE email='rahim@doctorconnect.test'),  1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '10:30 AM', 'completed', 'Mild hypertension', 'Advised low-salt diet, recheck BP in 2 weeks.'),
((SELECT user_id FROM users WHERE email='farhana@doctorconnect.test'),1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '11:00 AM', 'cancelled', NULL, NULL),
((SELECT user_id FROM users WHERE email='kamal@doctorconnect.test'),  1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '02:00 PM', 'completed', 'Routine checkup', 'No concerns, patient is healthy.');

-- Future appointments (upcoming — pending/confirmed)
INSERT INTO appointments (patient_id, doctor_id, appt_date, time_slot, status)
VALUES
((SELECT user_id FROM users WHERE email='rahim@doctorconnect.test'),  1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30 AM', 'confirmed'),
((SELECT user_id FROM users WHERE email='farhana@doctorconnect.test'),1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00 AM', 'pending'),
((SELECT user_id FROM users WHERE email='kamal@doctorconnect.test'),  1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '11:30 AM', 'pending'),
((SELECT user_id FROM users WHERE email='nilima@doctorconnect.test'), 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '01:00 PM', 'confirmed');
