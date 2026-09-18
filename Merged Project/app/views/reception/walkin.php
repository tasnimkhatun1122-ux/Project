<h1>Book a walk-in</h1>
<p class="muted">Register a patient who has arrived without an appointment.</p>

<?php if ($error != ""): ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success != ""): ?><div class="alert-ok"><?php echo $success; ?></div><?php endif; ?>

<div class="card">
    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=reception/walkin">

        <div class="nav-label">Patient</div>

        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name">

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone">

        <label for="email">Email <span class="muted">(optional)</span></label>
        <input type="text" id="email" name="email" placeholder="Leave blank for a walk-in record">

        <div class="nav-label">Appointment</div>

        <label for="doctor_id">Doctor</label>
        <select id="doctor_id" name="doctor_id">
            <option value="">-- Select a doctor --</option>
            <?php foreach ($doctors as $d): ?>
            <option value="<?php echo $d["doctor_id"]; ?>">
                <?php echo $d["full_name"]; ?> &mdash; <?php echo $d["dept_name"]; ?>
                (<?php echo (int) $d["consultation_fee"]; ?> Tk)
            </option>
            <?php endforeach; ?>
        </select>

        <label for="appt_date">Date</label>
        <input type="date" id="appt_date" name="appt_date" value="<?php echo date("Y-m-d"); ?>">

        <label for="time_slot">Time slot</label>
        <select id="time_slot" name="time_slot">
            <option value="">-- Select a time --</option>
            <?php foreach ($slots as $s): ?>
            <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Book walk-in" class="btn">
    </form>
</div>
