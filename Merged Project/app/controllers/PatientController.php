<?php

class PatientController extends Controller
{
    public function dashboard()
    {
        $this->requireRole("patient");

        $apptModel = $this->model("Appointment");
        $deptModel = $this->model("Department");

        $counts = $apptModel->statusCountsForPatient(Auth::id());
        $all    = $apptModel->forPatient(Auth::id());

        $upcoming = array();
        $today    = date("Y-m-d");
        foreach ($all as $a) {
            if ($a["appt_date"] >= $today && $a["status"] != "cancelled" && $a["status"] != "completed") {
                $upcoming[] = $a;
            }
        }

        $this->view("patient/dashboard", array(
            "pageTitle" => "Dashboard",
            "counts"    => $counts,
            "upcoming"  => $upcoming,
            "recent"    => array_slice($all, 0, 5),
            "deptCount" => $deptModel->countAll(),
        ));
    }

    public function doctors()
    {
        $this->requireAnyRole(array("patient", "receptionist"));

        $doctorModel = $this->model("Doctor");
        $deptModel   = $this->model("Department");

        $deptId = (int) $this->query("dept", 0);

        $this->view("patient/doctors", array(
            "pageTitle"   => "Find Doctors",
            "doctors"     => $doctorModel->allByDepartment($deptId),
            "departments" => $deptModel->all(),
            "deptId"      => $deptId,
        ));
    }

    public function book($doctorId = 0)
    {
        $this->requireRole("patient");

        $doctorModel = $this->model("Doctor");
        $apptModel   = $this->model("Appointment");

        $doctorId = (int) $doctorId;
        $doctor   = $doctorModel->findById($doctorId);

        if (!$doctor) {
            $this->redirect("patient/doctors");
        }

        $date  = $this->query("date");
        if (!valid_date($date)) {
            $date = date("Y-m-d");
        }

        $error   = "";
        $success = "";
        $slot    = "";

        if ($this->isPost()) {
            $date = $this->input("appt_date");
            $slot = $this->input("time_slot");

            if (!valid_date($date) || $slot == "") {
                $error = "Please choose both a date and a time slot.";
            } elseif (!in_array($slot, time_slots())) {
                $error = "That time slot is not available.";
            } elseif ($apptModel->isSlotTaken($doctorId, $date, $slot)) {
                $error = "That slot is already booked. Please pick another time.";
            } else {
                $apptModel->book(Auth::id(), $doctorId, $date, $slot);
                $this->redirect("patient/appointments");
            }
        }

        $this->view("patient/book", array(
            "pageTitle"  => "Book Appointment",
            "doctor"     => $doctor,
            "date"       => $date,
            "slot"       => $slot,
            "slots"      => time_slots(),
            "takenSlots" => $apptModel->takenSlots($doctorId, $date),
            "error"      => $error,
            "success"    => $success,
        ));
    }

    public function appointments()
    {
        $this->requireRole("patient");

        $apptModel = $this->model("Appointment");

        if ($this->isPost() && isset($_POST["cancel_id"])) {
            $apptModel->cancelByPatient((int) $_POST["cancel_id"], Auth::id());
            $this->redirect("patient/appointments");
        }

        $status = $this->query("status");
        $valid  = array("pending", "confirmed", "completed", "cancelled");

        if (!in_array($status, $valid)) {
            $status = "";
        }

        $this->view("patient/appointments", array(
            "pageTitle"    => "My Appointments",
            "appointments" => $apptModel->forPatient(Auth::id(), $status),
            "counts"       => $apptModel->statusCountsForPatient(Auth::id()),
            "status"       => $status,
        ));
    }
}
