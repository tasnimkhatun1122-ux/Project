<h1>My profile</h1>
<p class="muted">These details are what patients see when they search for you.</p>

<?php if ($error != ""): ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success != ""): ?><div class="alert-ok"><?php echo $success; ?></div><?php endif; ?>

<div class="card">
    <p><strong><?php echo Auth::name(); ?></strong></p>
    <p class="muted">Department: <span class="pill"><?php echo $dept ? $dept["dept_name"] : "&mdash;"; ?></span></p>

    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=doctor/profile">
        <label for="specialization">Specialization</label>
        <input type="text" id="specialization" name="specialization"
               value="<?php echo $doctor["specialization"]; ?>">

        <label for="consultation_fee">Consultation fee (Tk)</label>
        <input type="text" id="consultation_fee" name="consultation_fee"
               value="<?php echo (int) $doctor["consultation_fee"]; ?>">

        <label for="available_time">Available time</label>
        <input type="text" id="available_time" name="available_time"
               value="<?php echo $doctor["available_time"]; ?>">

        <label for="room">Room</label>
        <input type="text" id="room" name="room" value="<?php echo $doctor["room"]; ?>">

        <input type="submit" value="Save changes" class="btn">
    </form>
</div>
