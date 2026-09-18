<h1>Visit &mdash; <?php echo $appt["patient_name"]; ?></h1>
<p class="muted">
    <?php echo date("l, j M Y", strtotime($appt["appt_date"])); ?> &middot;
    <?php echo $appt["time_slot"]; ?> &middot;
    <span class="status status-<?php echo $appt["status"]; ?>"><?php echo $appt["status"]; ?></span>
</p>

<?php if ($error != ""): ?>
    <div class="alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="nav-label">Patient details</div>
    <p><strong><?php echo $appt["patient_name"]; ?></strong></p>
    <p class="muted"><?php echo $appt["patient_email"]; ?> &middot; <?php echo $appt["patient_phone"]; ?></p>
</div>

<div class="card">
    <div class="nav-label">Consultation record</div>

    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=doctor/visit/<?php echo $appt["appt_id"]; ?>">
        <label for="diagnosis">Diagnosis</label>
        <input type="text" id="diagnosis" name="diagnosis"
               value="<?php echo $appt["diagnosis"]; ?>"
               placeholder="e.g. Hypertension, stage 1">

        <label for="visit_note">Visit notes</label>
        <textarea id="visit_note" name="visit_note" rows="6"
                  placeholder="Symptoms, advice, prescription..."><?php echo $appt["visit_note"]; ?></textarea>

        <input type="submit" value="Save and mark completed" class="btn">
        <a href="<?php echo BASE_URL; ?>/index.php?url=doctor/dashboard" class="btn btn-ghost">Back</a>
    </form>
</div>
