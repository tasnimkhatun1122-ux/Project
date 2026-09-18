<?php

require_once __DIR__ . "/../models/Doctor.php";
require_once __DIR__ . "/../models/Appointment.php";

class ReceptionistController
{
    private $conn;
    private $doctorModel;
    private $appointmentModel;

    public function __construct($conn)
    {
        $this->conn = $conn;

        $this->doctorModel = new Doctor($conn);
        $this->appointmentModel = new Appointment($conn);
    }

    private function authorize()
    {
        if (
            !isset($_SESSION["user_id"]) ||
            $_SESSION["role"] !== "receptionist"
        ) {
            http_response_code(403);
            die("You are not authorized to access this page.");
        }
    }

    public function dashboard()
    {
        $this->authorize();

        $date = $_GET["date"] ?? date("Y-m-d");

        $sql = "SELECT a.appointment_id AS appt_id,
                       a.appointment_time AS time_slot,
                       a.status,
                       pu.full_name AS patient_name,
                       du.full_name AS doctor_name
                FROM appointments a
                JOIN users pu ON a.patient_id = pu.user_id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users du ON d.user_id = du.user_id
                WHERE a.appointment_date = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Database error: " . $this->conn->error);
        }

        $stmt->bind_param("s", $date);
        $stmt->execute();

        $result = $stmt->get_result();

        $queue = [];

        while ($row = $result->fetch_assoc()) {
            $queue[] = $row;
        }

        $stmt->close();

        usort($queue, function ($a, $b) {
            $timeA = strtotime($a["time_slot"]);
            $timeB = strtotime($b["time_slot"]);

            return $timeA <=> $timeB;
        });

        $statAppointments = count($queue);
        $statCheckedIn = 0;
        $statWaiting = 0;
        $statNoShow = 0;

        foreach ($queue as $appointment) {
            if (
                $appointment["status"] === "checked_in" ||
                $appointment["status"] === "completed"
            ) {
                $statCheckedIn++;
            } elseif ($appointment["status"] === "pending") {
                $statWaiting++;
            } elseif ($appointment["status"] === "no_show") {
                $statNoShow++;
            }
        }

        $doctors = $this->doctorModel->getAll();

        $receptionistName = $_SESSION["full_name"] ?? "Receptionist";

        require __DIR__ . "/../views/receptionist/dashboard.php";
    }

    public function bookWalkIn()
    {
        $this->authorize();

        header("Content-Type: application/json");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->jsonResponse(false, "Invalid request.");
        }

        $patientName = trim($_POST["patientName"] ?? "");
        $patientPhone = trim($_POST["patientPhone"] ?? "");
        $doctorId = (int) ($_POST["doctorId"] ?? 0);
        $slot = trim($_POST["slot"] ?? "");

        if ($patientName === "") {
            $this->jsonResponse(
                false,
                "Please enter the patient's name."
            );
        }

        if (
            $patientPhone === "" ||
            !preg_match("/^[0-9+\-\s]{7,15}$/", $patientPhone)
        ) {
            $this->jsonResponse(
                false,
                "Please enter a valid phone number."
            );
        }

        if ($doctorId <= 0) {
            $this->jsonResponse(
                false,
                "Please select a doctor."
            );
        }

        if ($slot === "") {
            $this->jsonResponse(
                false,
                "Please select a time slot."
            );
        }

        $doctor = $this->doctorModel->findById($doctorId);

        if (!$doctor) {
            $this->jsonResponse(
                false,
                "Selected doctor was not found."
            );
        }

        $timeSlot = ($slot === "next")
            ? "Next available"
            : $slot;

        $patient = $this->appointmentModel
            ->findPatientByPhone($patientPhone);

        if ($patient) {
            $patientId = $patient["user_id"];
        } else {
            $patientId = $this->appointmentModel
                ->createWalkInPatient(
                    $patientName,
                    $patientPhone
                );

            if (!$patientId) {
                $this->jsonResponse(
                    false,
                    "Could not create the patient's account."
                );
            }
        }

        $today = date("Y-m-d");

        $apptId = $this->appointmentModel
            ->createWalkInAppointment(
                $patientId,
                $doctorId,
                $today,
                $timeSlot
            );

        if (!$apptId) {
            $this->jsonResponse(
                false,
                "Could not book the walk-in appointment."
            );
        }

        $this->jsonResponse(
            true,
            "Walk-in added to today's queue!",
            [
                "apptId" => $apptId,
                "patientName" => $patientName,
                "doctorName" => $doctor["full_name"],
                "timeSlot" => $timeSlot
            ]
        );
    }

    public function updateAppointment()
    {
        $this->authorize();

        header("Content-Type: application/json");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->jsonResponse(false, "Invalid request.");
        }

        $apptId = (int) ($_POST["apptId"] ?? 0);
        $action = trim($_POST["action"] ?? "");

        $allowedActions = [
            "checkin",
            "noshow",
            "rebook"
        ];

        if (
            $apptId <= 0 ||
            !in_array($action, $allowedActions, true)
        ) {
            $this->jsonResponse(false, "Invalid request.");
        }

        $newStatus = $this->appointmentModel
            ->updateStatus($apptId, $action);

        if (!$newStatus) {
            $this->jsonResponse(
                false,
                "This appointment was already updated. Refresh the page."
            );
        }

        $this->jsonResponse(
            true,
            "Updated.",
            [
                "status" => $newStatus
            ]
        );
    }

    private function jsonResponse(
        $success,
        $message,
        $extra = []
    ) {
        echo json_encode(
            array_merge(
                [
                    "success" => $success,
                    "message" => $message
                ],
                $extra
            )
        );

        exit;
    }
}
?>
