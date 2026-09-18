<?php

class DoctorController extends Controller
{
    private function me()
    {
        $doctorModel = $this->model("Doctor");
        $doctor      = $doctorModel->findByUserId(Auth::id());

        if (!$doctor) {
            $this->redirect("auth/logout");
        }
        return $doctor;
    }

    public function dashboard()
    {
        $this->requireRole("doctor");

        $doctor    = $this->me();
        $apptModel = $this->model("Appointment");

        $date = $this->query("date");
        if (!valid_date($date)) {
            $date = date("Y-m-d");
        }

        $rows = $apptModel->forDoctorOnDate($doctor["doctor_id"], $date);

        $completed = 0;
        $pending   = 0;
        foreach ($rows as $r) {
            if ($r["status"] == "completed")      { $completed++; }
            elseif ($r["status"] != "cancelled")  { $pending++; }
        }

        $this->view("doctor/dashboard", array(
            "pageTitle"    => "Appointments",
            "doctor"       => $doctor,
            "date"         => $date,
            "appointments" => $rows,
            "completed"    => $completed,
            "pending"      => $pending,
            "expectedFee"  => $pending * (float) $doctor["consultation_fee"],
        ));
    }

    public function visit($apptId = 0)
    {
        $this->requireRole("doctor");

        $doctor    = $this->me();
        $apptModel = $this->model("Appointment");

        $appt = $apptModel->findForDoctor((int) $apptId, $doctor["doctor_id"]);

        if (!$appt) {
            $this->redirect("doctor/dashboard");
        }

        $error = "";

        if ($this->isPost()) {
            $diagnosis = $this->input("diagnosis");
            $note      = $this->input("visit_note");

            if ($diagnosis == "") {
                $error = "Please write a diagnosis before saving.";
            } else {
                $apptModel->completeVisit((int) $apptId, $doctor["doctor_id"], $diagnosis, $note);
                $this->redirect("doctor/dashboard");
            }
        }

        $this->view("doctor/visit", array(
            "pageTitle" => "Visit Details",
            "appt"      => $appt,
            "error"     => $error,
        ));
    }

    public function profile()
    {
        $this->requireRole("doctor");

        $doctor      = $this->me();
        $doctorModel = $this->model("Doctor");
        $deptModel   = $this->model("Department");

        $error   = "";
        $success = "";

        if ($this->isPost()) {
            $spec = $this->input("specialization");
            $fee  = $this->input("consultation_fee");
            $time = $this->input("available_time");
            $room = $this->input("room");

            if (!is_numeric($fee) || $fee < 0) {
                $error = "Consultation fee must be a positive number.";
            } else {
                $doctorModel->updateOwnProfile($doctor["doctor_id"], $spec, $fee, $time, $room);
                $success = "Your profile has been updated.";
                $doctor  = $doctorModel->findByUserId(Auth::id());
            }
        }

        $dept = $deptModel->findById($doctor["dept_id"]);

        $this->view("doctor/profile", array(
            "pageTitle" => "My Profile",
            "doctor"    => $doctor,
            "dept"      => $dept,
            "error"     => $error,
            "success"   => $success,
        ));
    }
}
