<?php

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $this->redirect(Auth::dashboardFor(Auth::role()));
        }

        $doctorModel = $this->model("Doctor");
        $deptModel   = $this->model("Department");

        $this->view("home/index", array(
            "pageTitle"   => "Welcome",
            "doctorCount" => $doctorModel->countAll(),
            "deptCount"   => $deptModel->countAll(),
            "departments" => $doctorModel->departmentSummary(),
        ), "plain");
    }
}
