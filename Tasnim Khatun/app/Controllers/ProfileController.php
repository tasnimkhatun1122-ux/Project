<?php
class ProfileController
{
    public function __construct()
    {
        Auth::check();
    }

    public function index()
    {
        $doctor = Doctor::findByUserId($_SESSION['user_id']);
        if (!$doctor) {
            die("No doctor profile is linked to this account yet. Please contact the admin.");
        }

        $success = $_SESSION['profile_success'] ?? '';
        $error   = $_SESSION['profile_error'] ?? '';
        unset($_SESSION['profile_success'], $_SESSION['profile_error']);

        view('doctor/profile', compact('doctor', 'success', 'error'));
    }

    public function update()
    {
        $doctor = Doctor::findByUserId($_SESSION['user_id']);

        $specialization = trim($_POST['specialization'] ?? '');
        $fee            = $_POST['consultation_fee'] ?? '';
        $availableTime  = trim($_POST['available_time'] ?? '');
        $room           = trim($_POST['room'] ?? '');

        if ($specialization === '' || $fee === '' || !is_numeric($fee) || $fee < 0) {
            $_SESSION['profile_error'] = "Please enter a valid specialization and consultation fee.";
        } else {
            Doctor::updateProfile($doctor['doctor_id'], $specialization, $fee, $availableTime, $room);
            $_SESSION['profile_success'] = "Profile updated successfully.";
        }

        header("Location: index.php?route=profile");
        exit();
    }
}
