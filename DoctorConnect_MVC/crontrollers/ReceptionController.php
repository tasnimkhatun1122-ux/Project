<?php

class ReceptionController extends Controller
{
    public function dashboard()
    {
        $this->requireRole("receptionist");

        $apptModel   = $this->model("Appointment");
        $doctorModel = $this->model("Doctor");

        if ($this->isPost() && isset($_POST["appt_id"], $_POST["new_status"])) {
            $status = $this->input("new_status");

            if (in_array($status, array("confirmed", "cancelled"))) {
                $apptModel->setStatusIfPending((int) $_POST["appt_id"], $status);
            }
            $this->redirect("reception/dashboard");
        }

        $date = $this->query("date");
        if (!valid_date($date)) {
            $date = date("Y-m-d");
        }

        $queue = $apptModel->queueForDate($date);

        $checkedIn = 0;
        $waiting   = 0;
        $dropped   = 0;

        foreach ($queue as $q) {
            if ($q["status"] == "confirmed" || $q["status"] == "completed") { $checkedIn++; }
            elseif ($q["status"] == "pending")                             { $waiting++; }
            elseif ($q["status"] == "cancelled")                           { $dropped++; }
        }

        $this->view("reception/dashboard", array(
            "pageTitle" => "Front Desk",
            "date"      => $date,
            "queue"     => $queue,
            "checkedIn" => $checkedIn,
            "waiting"   => $waiting,
            "dropped"   => $dropped,
            "doctors"   => $doctorModel->allByDepartment(0),
        ));
    }

    public function walkin()
    {
        $this->requireRole("receptionist");

        $userModel   = $this->model("User");
        $doctorModel = $this->model("Doctor");
        $apptModel   = $this->model("Appointment");

        $error   = "";
        $success = "";

        if ($this->isPost()) {
            $name     = $this->input("full_name");
            $phone    = $this->input("phone");
            $email    = $this->input("email");
            $doctorId = (int) $this->input("doctor_id", 0);
            $date     = $this->input("appt_date");
            $slot     = $this->input("time_slot");

            if ($name == "" || $doctorId == 0 || !valid_date($date) || $slot == "") {
                $error = "Patient name, doctor, date and time slot are all required.";
            } elseif (!in_array($slot, time_slots())) {
                $error = "That time slot is not available.";
            } elseif ($apptModel->isSlotTaken($doctorId, $date, $slot)) {
                $error = "That slot is already booked. Please pick another time.";
            } else {
                if ($email == "") {
                    $email = "walkin" . time() . "@doctorconnect.local";
                }

                $existing = $userModel->findByEmail($email);

                if ($existing) {
                    $patientId = $existing["user_id"];
                } else {
                    $patientId = $userModel->create($name, $email, "1234", $phone, "patient");
                }

                $apptModel->book($patientId, $doctorId, $date, $slot, "confirmed");
                $success = "Walk-in booked for " . $name . ".";
            }
        }

        $this->view("reception/walkin", array(
            "pageTitle" => "Book Walk-in",
            "doctors"   => $doctorModel->allByDepartment(0),
            "patients"  => $userModel->allPatients(),
            "slots"     => time_slots(),
            "error"     => $error,
            "success"   => $success,
        ));
    }
}
