<?php

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->requireRole("admin");

        $apptModel   = $this->model("Appointment");
        $doctorModel = $this->model("Doctor");
        $deptModel   = $this->model("Department");

        $this->view("admin/dashboard", array(
            "pageTitle"   => "Overview",
            "counts"      => $apptModel->statusCounts(),
            "doctorCount" => $doctorModel->countAll(),
            "deptCount"   => $deptModel->countAll(),
            "deptStats"   => $apptModel->departmentStats(),
            "recent"      => $apptModel->recent(10),
        ));
    }

    public function doctors()
    {
        $this->requireRole("admin");

        $doctorModel = $this->model("Doctor");
        $deptModel   = $this->model("Department");
        $userModel   = $this->model("User");
        $apptModel   = $this->model("Appointment");

        $error   = "";
        $success = "";

        if ($this->isPost()) {
            $action = $this->input("action");

            if ($action == "add") {
                $name   = $this->input("full_name");
                $email  = $this->input("email");
                $phone  = $this->input("phone");
                $deptId = (int) $this->input("dept_id", 0);
                $spec   = $this->input("specialization");
                $fee    = $this->input("consultation_fee");
                $time   = $this->input("available_time");
                $room   = $this->input("room");

                if ($name == "" || $email == "" || $deptId == 0) {
                    $error = "Name, email and department are required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Please enter a valid email address.";
                } elseif (!is_numeric($fee) || $fee < 0) {
                    $error = "Consultation fee must be a positive number.";
                } elseif ($userModel->emailExists($email)) {
                    $error = "A user with that email already exists.";
                } else {
                    $userId = $userModel->create($name, $email, "1234", $phone, "doctor");
                    $doctorModel->create($userId, $deptId, $spec, $fee, $time, $room);
                    $success = "Doctor added. Default password is 1234.";
                }

            } elseif ($action == "edit") {
                $doctorId = (int) $this->input("doctor_id", 0);
                $deptId   = (int) $this->input("dept_id", 0);
                $spec     = $this->input("specialization");
                $fee      = $this->input("consultation_fee");
                $time     = $this->input("available_time");
                $room     = $this->input("room");

                if (!is_numeric($fee) || $fee < 0) {
                    $error = "Consultation fee must be a positive number.";
                } else {
                    $doctorModel->update($doctorId, $deptId, $spec, $fee, $time, $room);
                    $success = "Doctor updated.";
                }

            } elseif ($action == "delete") {
                $doctorId = (int) $this->input("doctor_id", 0);

                if ($apptModel->countForDoctor($doctorId) > 0) {
                    $error = "This doctor has appointments and cannot be deleted.";
                } else {
                    $doctor = $doctorModel->findById($doctorId);
                    if ($doctor) {
                        $doctorModel->delete($doctorId);
                        $userModel->delete($doctor["user_id"]);
                        $success = "Doctor removed.";
                    }
                }
            }
        }

        $this->view("admin/doctors", array(
            "pageTitle"   => "Doctors",
            "doctors"     => $doctorModel->all(),
            "departments" => $deptModel->all(),
            "error"       => $error,
            "success"     => $success,
        ));
    }

    public function departments()
    {
        $this->requireRole("admin");

        $deptModel = $this->model("Department");

        $error   = "";
        $success = "";

        if ($this->isPost()) {
            $action = $this->input("action");
            $name   = $this->input("dept_name");

            if ($action == "add") {
                if ($name == "") {
                    $error = "Department name is required.";
                } elseif ($deptModel->findByName($name)) {
                    $error = "That department already exists.";
                } else {
                    $deptModel->create($name);
                    $success = "Department added.";
                }

            } elseif ($action == "edit") {
                $deptId = (int) $this->input("dept_id", 0);

                if ($name == "") {
                    $error = "Department name is required.";
                } else {
                    $deptModel->update($deptId, $name);
                    $success = "Department updated.";
                }

            } elseif ($action == "delete") {
                $deptId = (int) $this->input("dept_id", 0);

                if ($deptModel->doctorCount($deptId) > 0) {
                    $error = "This department still has doctors and cannot be deleted.";
                } else {
                    $deptModel->delete($deptId);
                    $success = "Department removed.";
                }
            }
        }

        $this->view("admin/departments", array(
            "pageTitle"   => "Departments",
            "departments" => $deptModel->withDoctorCounts(),
            "error"       => $error,
            "success"     => $success,
        ));
    }
}
