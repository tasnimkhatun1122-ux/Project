<h1>Today&rsquo;s schedule</h1>
<p class="muted">Appointments booked with you for the selected date.</p>

<a href="<?php echo BASE_URL; ?>/index.php?url=doctor/profile" class="btn btn-ghost">
    <?php echo icon("stethoscope"); ?> My profile
</a>

<div class="stat-row">
    <div class="stat"><span class="stat-num"><?php echo count($appointments); ?></span>
        <span class="stat-label">Appointments on this date</span></div>
    <div class="stat"><span class="stat-num"><?php echo $completed; ?></span>
        <span class="stat-label">Completed</span></div>
    <div class="stat"><span class="stat-num"><?php echo $pending; ?></span>
        <span class="stat-label">Still to see</span></div>
    <div class="stat"><span class="stat-num"><?php echo (int) $expectedFee; ?></span>
        <span class="stat-label">Expected fees (Tk)</span></div>
</div>

<div class="card">
    <form method="get" action="<?php echo BASE_URL; ?>/index.php">
        <input type="hidden" name="url" value="doctor/dashboard">
        <label for="date">Showing appointments for</label>
        <input type="date" id="date" name="date" value="<?php echo $date; ?>">
        <input type="submit" value="Show" class="btn btn-small">
    </form>
</div>

<div class="card">
    <div class="table-wrap"><table>
        <tr><th>Time</th><th>Patient</th><th>Phone</th><th>Diagnosis</th><th>Status</th><th></th></tr>
        <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?php echo $a["time_slot"]; ?></td>
            <td><?php echo $a["patient_name"]; ?></td>
            <td><?php echo $a["patient_phone"]; ?></td>
            <td><?php echo $a["diagnosis"] != "" ? $a["diagnosis"] : "&mdash;"; ?></td>
            <td><span class="status status-<?php echo $a["status"]; ?>"><?php echo $a["status"]; ?></span></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/index.php?url=doctor/visit/<?php echo $a["appt_id"]; ?>"
                   class="btn btn-small">
                   <?php echo $a["status"] == "completed" ? "View visit" : "Start visit"; ?>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
