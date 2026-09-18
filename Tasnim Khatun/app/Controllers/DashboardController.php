<?php
class DashboardController
{
    public function __construct()
    {
        Auth::check();
    }

    public function index()
    {
        $doctorUserId = $_SESSION['user_id'];
        $doctor = Doctor::findByUserId($doctorUserId);

        if (!$doctor)
        {
            die("No doctor profile is linked to this account yet. Please contact the admin.");
        }

        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = date('Y-m-d');
        }

        $statusCounts = Appointment::getStatusCounts($doctor['doctor_id'], $selectedDate);
        $total = array_sum($statusCounts);
        $appointments = Appointment::getByDoctorAndDate($doctor['doctor_id'], $selectedDate);
        $flash = $_GET['flash'] ?? '';

        view('doctor/dashboard', compact('doctor', 'selectedDate', 'statusCounts', 'total', 'appointments', 'flash'));
    }

    public function complete()
    {
        $doctorUserId = $_SESSION['user_id'];
        $apptId    = (int) ($_POST['appt_id'] ?? 0);
        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $visitNote = trim($_POST['visit_note'] ?? '');
        $date      = $_POST['date'] ?? date('Y-m-d');

        if ($apptId > 0 && $diagnosis !== '' && Appointment::belongsToDoctor($apptId, $doctorUserId)) {
            Appointment::markCompleted($apptId, $diagnosis, $visitNote);
            header("Location: index.php?route=dashboard&date=" . urlencode($date) . "&flash=completed");
            exit();
        }

        header("Location: index.php?route=dashboard&date=" . urlencode($date));
        exit();
    }
}
