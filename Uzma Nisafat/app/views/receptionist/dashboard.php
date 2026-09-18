<?php

function statusBadge($status)
{
    if ($status === "pending") {
        return [
            "label" => "Waiting",
            "class" => "status-waiting"
        ];
    }

    if ($status === "no_show") {
        return [
            "label" => "No show",
            "class" => "status-noshow"
        ];
    }

    return [
        "label" => "Seen",
        "class" => "status-seen"
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Front desk</title>

    <link rel="stylesheet" href="css/receptionist.css">
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

                    <li
                        class="nav-item"
                        data-nav="profile"
                        onclick="window.location.href='../../controllers/ReceptionistController.php?action=profile'">
                        <span class="nav-dot"></span>
                        My profile
                    </li>

                    <li
                        class="nav-item"
                        data-nav="logout"
                        onclick="window.location.href='../../controllers/ReceptionistController.php?action=logout'">
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
                        <?php echo $statAppointments; ?>
                    </div>

                    <div class="stat-label">
                        Appointments today
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-value" id="statCheckedIn">
                        <?php echo $statCheckedIn; ?>
                    </div>

                    <div class="stat-label">
                        Checked in
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-value amber" id="statWaiting">
                        <?php echo $statWaiting; ?>
                    </div>

                    <div class="stat-label">
                        Waiting
                    </div>

                </div>


                <div class="stat-card">

                    <div class="stat-value red" id="statNoShow">
                        <?php echo $statNoShow; ?>
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

                                    $serial =
                                        "#" .
                                        str_pad(
                                            (string) ($index + 1),
                                            2,
                                            "0",
                                            STR_PAD_LEFT
                                        );

                                    $doctorLabel =
                                        "Dr. " . $row["doctor_name"];

                                ?>

                                <tr data-appt-id="<?php echo (int) $row["appt_id"]; ?>">

                                    <td class="cell-serial">
                                        <?php echo htmlspecialchars($serial); ?>
                                    </td>

                                    <td class="cell-patient">
                                        <?php echo htmlspecialchars($row["patient_name"]); ?>
                                    </td>

                                    <td class="cell-doctor">
                                        <?php echo htmlspecialchars($doctorLabel); ?>
                                    </td>

                                    <td class="cell-time">
                                        <?php echo htmlspecialchars($row["time_slot"]); ?>
                                    </td>

                                    <td>

                                        <span
                                            class="status-badge <?php echo $badge["class"]; ?>">
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

                                            <span class="action-none">
                                                —
                                            </span>

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

                                    <option
                                        value="<?php echo (int) $doctor["doctor_id"]; ?>">

                                        Dr.
                                        <?php echo htmlspecialchars($doctor["full_name"]); ?>

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


    <script src="js/receptionist.js"></script>

</body>

</html>
