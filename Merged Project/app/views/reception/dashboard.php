<h1>Front desk</h1>
<p class="muted">Check in arriving patients and keep the day&rsquo;s queue up to date.</p>

<a href="<?php echo BASE_URL; ?>/index.php?url=reception/walkin" class="btn">
    <?php echo icon("plus"); ?> New walk-in
</a>

<div class="stat-row">
    <div class="stat"><span class="stat-num"><?php echo count($queue); ?></span>
        <span class="stat-label">Appointments</span></div>
    <div class="stat"><span class="stat-num"><?php echo $checkedIn; ?></span>
        <span class="stat-label">Checked in</span></div>
    <div class="stat"><span class="stat-num"><?php echo $waiting; ?></span>
        <span class="stat-label">Waiting</span></div>
    <div class="stat"><span class="stat-num"><?php echo $dropped; ?></span>
        <span class="stat-label">Cancelled / no-show</span></div>
</div>

<div class="card">
    <form method="get" action="<?php echo BASE_URL; ?>/index.php">
        <input type="hidden" name="url" value="reception/dashboard">
        <label for="date">Showing the queue for</label>
        <input type="date" id="date" name="date" value="<?php echo $date; ?>">
        <input type="submit" value="Show" class="btn btn-small">
    </form>
</div>

<div class="card">
    <div class="table-wrap"><table>
        <tr><th>Time</th><th>Patient</th><th>Phone</th><th>Doctor</th><th>Department</th><th>Status</th><th></th></tr>
        <?php foreach ($queue as $q): ?>
        <tr>
            <td><?php echo $q["time_slot"]; ?></td>
            <td><?php echo $q["patient_name"]; ?></td>
            <td><?php echo $q["patient_phone"]; ?></td>
            <td><?php echo $q["doctor_name"]; ?></td>
            <td><span class="pill"><?php echo $q["dept_name"]; ?></span></td>
            <td><span class="status status-<?php echo $q["status"]; ?>"><?php echo $q["status"]; ?></span></td>
            <td>
                <?php if ($q["status"] == "pending"): ?>
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=reception/dashboard">
                    <input type="hidden" name="appt_id" value="<?php echo $q["appt_id"]; ?>">
                    <button type="submit" name="new_status" value="confirmed" class="btn btn-small">Check in</button>
                    <button type="submit" name="new_status" value="cancelled" class="btn btn-small btn-ghost">No-show</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
