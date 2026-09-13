<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../Receptionist/Login.html");
    exit;
}

require_once "db.php";

$selectedDate = $_GET["date"] ?? date("Y-m-d");

if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $selectedDate)) {
    $selectedDate = date("Y-m-d");
}

$isToday = ($selectedDate === date("Y-m-d"));



$queue = [];

$sql = "SELECT a.appt_id, a.time_slot, a.status,
               pu.full_name AS patient_name,
               du.full_name AS doctor_name
        FROM appointments a
        JOIN users pu ON a.patient_id = pu.user_id
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users du ON d.user_id = du.user_id
        WHERE a.appt_date = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $queue[] = $row;
    }

    $stmt->close();
}

usort($queue, function ($a, $b) {

    $timeA = DateTime::createFromFormat("h:i A", trim($a["time_slot"]));
    $timeB = DateTime::createFromFormat("h:i A", trim($b["time_slot"]));

    if ($timeA === false && $timeB === false) {
        return $a["appt_id"] <=> $b["appt_id"];
    }

    if ($timeA === false) {
        return 1;
    }

    if ($timeB === false) {
        return -1;
    }

    return $timeA <=> $timeB;
});



$statAppointments = count($queue);
$statCheckedIn = 0;
$statWaiting = 0;
$statNoShow = 0;

foreach ($queue as $row) {

    if ($row["status"] === "checked_in" || $row["status"] === "completed") {
        $statCheckedIn++;
    } elseif ($row["status"] === "pending") {
        $statWaiting++;
    } elseif ($row["status"] === "no_show") {
        $statNoShow++;
    }
}


$doctors = [];

$sql = "SELECT d.doctor_id, u.full_name
        FROM doctors d
        JOIN users u ON d.user_id = u.user_id
        ORDER BY u.full_name";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

$receptionistName = $_SESSION["full_name"] ?? "Receptionist";

$conn->close();


function statusBadge($status) {

    if ($status === "pending") {
        return ["label" => "Waiting", "class" => "status-waiting"];
    }

    if ($status === "no_show") {
        return ["label" => "No show", "class" => "status-noshow"];
    }

    return ["label" => "Seen", "class" => "status-seen"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Front desk</title>

    <link rel="stylesheet" href="../CSS/receptionist.css">
</head>

<body>

    <div class="dashboard-layout">

        <aside class="sidebar">

            <div class="sidebar-top">

                <div class="brand">
                    <div class="brand-icon">
                        +
                    </div>

                    <span class="brand-name">
                        DoctorConnect
                    </span>
                </div>

                <ul class="sidebar-nav" id="sidebarNav">

                    <li class="nav-item active" data-nav="front-desk">
                        <span class="nav-dot"></span>
                        Front desk
                    </li>

                    <li class="nav-item" data-nav="book-walkin">
                        <span class="nav-dot"></span>
                        Book walk-in
                    </li>

                    <li class="nav-item" data-nav="todays-queue">
                        <span class="nav-dot"></span>
                        Today's queue
                    </li>

                    <li class="nav-item" data-nav="patients">
                        <span class="nav-dot"></span>
                        Patients
                    </li>

                    <li class="nav-item" data-nav="profile" onclick="window.location.href='profile.php'">
                        <span class="nav-dot"></span>
                        My profile
                    </li>

                    <li class="nav-item" data-nav="logout" onclick="window.location.href='logout.php'">
                        <span class="nav-dot"></span>
                        Logout
                    </li>

                </ul>

            </div>

            <div class="sidebar-bottom">

                <div class="user-avatar"></div>

                <div>
                    <div class="user-name">
                        <?php echo htmlspecialchars($receptionistName); ?>
                    </div>

                    <div class="user-role">
                        Receptionist
                    </div>
                </div>

            </div>

        </aside>


        <main class="main-content">

            <div class="page-header">

                <div>
                    <h1 class="page-title">
                        Front desk
                    </h1>

                    <p class="page-subtitle">
                        Check in arriving patients and book walk-in appointments.
                    </p>
                </div>

                <button
                    type="button"
                    id="newWalkinBtn"
                    class="btn-primary">
                    + New walk-in
                </button>

            </div>



            <section class="stats-grid">

                <div class="stat-card">

                    <div class="stat-value" id="statAppointments">
                        31
                    </div>

                    <div class="stat-label">
                        Appointments today
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-value" id="statCheckedIn">
                        18
                    </div>

                    <div class="stat-label">
                        Checked in
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-value amber" id="statWaiting">
                        9
                    </div>

                    <div class="stat-label">
                        Waiting
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-value red" id="statNoShow">
                        4
                    </div>

                    <div class="stat-label">
                        No show
                    </div>

                </div>

            </section>



            <section class="content-grid">

                <div class="card queue-card">

                    <h2 class="card-title">
                        Today's queue
                    </h2>

                    <div class="table-scroll">

                        <table class="queue-table">

                            <thead>

                                <tr>
                                    <th>Serial</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>

                            </thead>


                            <tbody id="queueBody">

                                <?php if (count($queue) === 0) { ?>

                                    <tr>
                                        <td colspan="6" style="text-align:center;color:#8a949a;">
                                            No appointments for this date yet.
                                        </td>
                                    </tr>

                                <?php } ?>

                                <?php foreach ($queue as $index => $row) {

                                    $badge = statusBadge($row["status"]);
                                    $serial = "#" . str_pad((string) ($index + 1), 2, "0", STR_PAD_LEFT);
                                    $doctorLabel = "Dr. " . $row["doctor_name"];

                                ?>

                                <tr data-appt-id="<?php echo (int) $row["appt_id"]; ?>">
                                    <td class="cell-serial"><?php echo htmlspecialchars($serial); ?></td>
                                    <td class="cell-patient"><?php echo htmlspecialchars($row["patient_name"]); ?></td>
                                    <td class="cell-doctor"><?php echo htmlspecialchars($doctorLabel); ?></td>
                                    <td class="cell-time"><?php echo htmlspecialchars($row["time_slot"]); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $badge["class"]; ?>">
                                            <?php echo $badge["label"]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row["status"] === "pending") { ?>

                                            <button
                                                type="button"
                                                class="action-link"
                                                data-action="checkin">
                                                Check in
                                            </button>
                                            &nbsp;/&nbsp;
                                            <button
                                                type="button"
                                                class="action-link"
                                                data-action="noshow">
                                                No show
                                            </button>

                                        <?php } elseif ($row["status"] === "no_show") { ?>

                                            <button
                                                type="button"
                                                class="action-link"
                                                data-action="rebook">
                                                Rebook
                                            </button>

                                        <?php } else { ?>

                                            <span class="action-none">—</span>

                                        <?php } ?>
                                    </td>
                                </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>


                <div class="card booking-card">

                    <h2 class="booking-title">
                        Quick walk-in booking
                    </h2>


                    <form id="walkinForm">

                        <div class="form-group">

                            <label
                                for="patientName"
                                class="form-label">
                                Patient name
                            </label>

                            <input
                                type="text"
                                id="patientName"
                                name="patientName"
                                class="form-input"
                                placeholder="Full name"
                                autocomplete="off">

                            <p
                                id="patientNameError"
                                class="error-message">
                                Please enter the patient's name.
                            </p>

                        </div>


                        <div class="form-group">

                            <label
                                for="patientPhone"
                                class="form-label">
                                Phone
                            </label>

                            <input
                                type="tel"
                                id="patientPhone"
                                name="patientPhone"
                                class="form-input"
                                placeholder="01XXXXXXXXX"
                                autocomplete="off">

                            <p
                                id="patientPhoneError"
                                class="error-message">
                                Please enter a valid phone number.
                            </p>

                        </div>


                        <div class="form-group">

                            <label
                                for="walkinDoctor"
                                class="form-label">
                                Doctor
                            </label>

                            <select
                                id="walkinDoctor"
                                name="doctorId"
                                class="form-select placeholder">

                                <option value="" disabled selected>
                                    Select doctor
                                </option>

                                <?php foreach ($doctors as $doctor) { ?>

                                    <option value="<?php echo (int) $doctor["doctor_id"]; ?>">
                                        Dr. <?php echo htmlspecialchars($doctor["full_name"]); ?>
                                    </option>

                                <?php } ?>

                            </select>

                            <p
                                id="walkinDoctorError"
                                class="error-message">
                                Please select a doctor.
                            </p>

                        </div>


                        <div class="form-group">

                            <label
                                for="walkinSlot"
                                class="form-label">
                                Time slot
                            </label>

                            <select
                                id="walkinSlot"
                                name="slot"
                                class="form-select placeholder">

                                <option value="" disabled selected>
                                    Next available
                                </option>

                                <option value="next">
                                    Next available
                                </option>

                                <option value="12:00 PM">
                                    12:00 PM
                                </option>

                                <option value="12:30 PM">
                                    12:30 PM
                                </option>

                                <option value="01:00 PM">
                                    01:00 PM
                                </option>

                            </select>

                            <p
                                id="walkinSlotError"
                                class="error-message">
                                Please select a time slot.
                            </p>

                        </div>


                        <button
                            type="submit"
                            class="btn-confirm">
                            Confirm walk-in
                        </button>


                        <p
                            id="bookingSuccess"
                            class="booking-success">
                            Walk-in added to today's queue!
                        </p>


                        <p class="booking-note">
                            Walk-in patients are added to the end of the selected doctor's queue for today.
                        </p>

                    </form>

                </div>

            </section>

        </main>

    </div>


    <script>

        const navItems =
            document.querySelectorAll(".nav-item");


        navItems.forEach(function (item) {

            item.addEventListener("click", function () {

                navItems.forEach(function (i) {
                    i.classList.remove("active");
                });

                item.classList.add("active");

                if (item.dataset.nav === "book-walkin") {

                    const form =
                        document.getElementById("walkinForm");

                    if (form) {
                        form.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }
                }

                if (item.dataset.nav === "todays-queue") {

                    const queue =
                        document.querySelector(".queue-card");

                    if (queue) {
                        queue.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }
                }

            });

        });


        const newWalkinBtn =
            document.getElementById("newWalkinBtn");

        const patientNameInput =
            document.getElementById("patientName");


        newWalkinBtn.addEventListener("click", function () {

            patientNameInput.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

            patientNameInput.focus();

        });




        const queueBody =
            document.getElementById("queueBody");


        queueBody.addEventListener("click", function (event) {

            const button =
                event.target.closest(".action-link");

            if (!button) {
                return;
            }

            const row =
                button.closest("tr");

            const apptId =
                row.dataset.apptId;

            const action =
                button.dataset.action;

            if (!apptId) {
                alert("Appointment ID is missing.");
                return;
            }

            button.disabled = true;

            const formData = new FormData();

            formData.append("apptId", apptId);
            formData.append("action", action);


            fetch("updateAppointment.php", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

                    if (result.success) {

                        window.location.reload();

                    } else {

                        alert(
                            result.message ||
                            "Could not update this appointment."
                        );

                        button.disabled = false;
                    }

                })
                .catch(function () {

                    alert("Unable to connect to the server.");

                    button.disabled = false;

                });

        });


        const walkinForm =
            document.getElementById("walkinForm");

        const patientPhoneInput =
            document.getElementById("patientPhone");

        const walkinDoctorSelect =
            document.getElementById("walkinDoctor");

        const walkinSlotSelect =
            document.getElementById("walkinSlot");

        const patientNameError =
            document.getElementById("patientNameError");

        const patientPhoneError =
            document.getElementById("patientPhoneError");

        const walkinDoctorError =
            document.getElementById("walkinDoctorError");

        const walkinSlotError =
            document.getElementById("walkinSlotError");

        const bookingSuccess =
            document.getElementById("bookingSuccess");


        function isValidPhone(phone) {

            const phonePattern =
                /^[0-9+\-\s]{7,15}$/;

            return phonePattern.test(phone);

        }


        function toggleSelectPlaceholder(select) {

            if (select.value === "") {

                select.classList.add("placeholder");

            } else {

                select.classList.remove("placeholder");

            }

        }


        [
            walkinDoctorSelect,
            walkinSlotSelect
        ].forEach(function (select) {

            select.addEventListener("change", function () {

                toggleSelectPlaceholder(select);

            });

        });


        walkinForm.addEventListener("submit", function (event) {

            event.preventDefault();


            patientNameError.classList.remove("show");

            patientPhoneError.classList.remove("show");

            walkinDoctorError.classList.remove("show");

            walkinSlotError.classList.remove("show");

            bookingSuccess.classList.remove("show");

            bookingSuccess.style.color = "";


            let isValid = true;


            const patientName =
                patientNameInput.value.trim();


            if (patientName === "") {

                patientNameError.classList.add("show");

                isValid = false;

            }


            const patientPhone =
                patientPhoneInput.value.trim();


            if (patientPhone === "") {

                patientPhoneError.textContent =
                    "Please enter the patient's phone number.";

                patientPhoneError.classList.add("show");

                isValid = false;

            } else if (!isValidPhone(patientPhone)) {

                patientPhoneError.textContent =
                    "Please enter a valid phone number.";

                patientPhoneError.classList.add("show");

                isValid = false;

            }


            const doctorValue =
                walkinDoctorSelect.value;


            if (doctorValue === "") {

                walkinDoctorError.classList.add("show");

                isValid = false;

            }


            const slotValue =
                walkinSlotSelect.value;


            if (slotValue === "") {

                walkinSlotError.classList.add("show");

                isValid = false;

            }


            if (!isValid) {
                return;
            }


            const submitButton =
                walkinForm.querySelector(".btn-confirm");

            submitButton.disabled = true;


            const formData =
                new FormData(walkinForm);


            fetch("bookWalkin.php", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

                    if (result.success) {

                        bookingSuccess.textContent =
                            result.message;

                        bookingSuccess.classList.add("show");

                        setTimeout(function () {

                            window.location.reload();

                        }, 700);

                    } else {

                        bookingSuccess.textContent =
                            result.message ||
                            "Could not book this walk-in.";

                        bookingSuccess.style.color =
                            "#d64545";

                        bookingSuccess.classList.add("show");

                        submitButton.disabled = false;
                    }

                })
                .catch(function () {

                    bookingSuccess.textContent =
                        "Unable to connect to the server.";

                    bookingSuccess.style.color =
                        "#d64545";

                    bookingSuccess.classList.add("show");

                    submitButton.disabled = false;

                });

        });


        [
            patientNameInput,
            patientPhoneInput
        ].forEach(function (input) {

            input.addEventListener("input", function () {

                bookingSuccess.classList.remove("show");

            });

        });


        patientNameInput.addEventListener("input", function () {

            if (patientNameInput.value.trim() !== "") {

                patientNameError.classList.remove("show");

            }

        });


        patientPhoneInput.addEventListener("input", function () {

            if (patientPhoneInput.value.trim() !== "") {

                patientPhoneError.classList.remove("show");

            }

        });

    </script>

</body>

</html>