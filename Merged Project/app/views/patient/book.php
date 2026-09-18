<h1>Book with <?php echo $doctor["full_name"]; ?></h1>
<p class="muted">
    <?php echo $doctor["specialization"]; ?> &middot;
    <span class="pill"><?php echo $doctor["dept_name"]; ?></span> &middot;
    <?php echo (int) $doctor["consultation_fee"]; ?> Tk
</p>

<?php if ($error != ""): ?>
    <div class="alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <label>Choose a day</label>
    <div class="daystrip">
        <?php for ($i = 0; $i < 7; $i++):
            $d   = date("Y-m-d", strtotime("+$i day"));
            $on  = ($d == $date) ? " on" : "";
        ?>
        <a class="day<?php echo $on; ?>"
           href="<?php echo BASE_URL; ?>/index.php?url=patient/book/<?php echo $doctor["doctor_id"]; ?>&amp;date=<?php echo $d; ?>">
            <span><?php echo date("D", strtotime($d)); ?></span>
            <strong><?php echo date("j", strtotime($d)); ?></strong>
            <span><?php echo date("M", strtotime($d)); ?></span>
        </a>
        <?php endfor; ?>
    </div>

    <form method="post"
          action="<?php echo BASE_URL; ?>/index.php?url=patient/book/<?php echo $doctor["doctor_id"]; ?>">

        <input type="hidden" name="appt_date" value="<?php echo $date; ?>">

        <label>Available time slots</label>
        <div class="slotgrid">
            <?php foreach ($slots as $s):
                $isTaken = in_array($s, $takenSlots);
            ?>
            <button type="submit" name="time_slot" value="<?php echo $s; ?>"
                    class="slot<?php echo $isTaken ? " taken" : ""; ?>"
                    <?php echo $isTaken ? "disabled" : ""; ?>>
                <?php echo $s; ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="slotkey">
            <span><i class="k-free"></i> Available</span>
            <span><i class="k-taken"></i> Already booked</span>
        </div>
    </form>
</div>
