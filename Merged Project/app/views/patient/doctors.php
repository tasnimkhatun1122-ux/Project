<h1>Find a doctor</h1>
<p class="muted">Browse by department, then pick an open time slot.</p>

<div class="tabs">
    <a href="<?php echo BASE_URL; ?>/index.php?url=patient/doctors"
       class="tab<?php echo $deptId == 0 ? " on" : ""; ?>">All</a>
    <?php foreach ($departments as $d): ?>
    <a href="<?php echo BASE_URL; ?>/index.php?url=patient/doctors&amp;dept=<?php echo $d["dept_id"]; ?>"
       class="tab<?php echo $deptId == $d["dept_id"] ? " on" : ""; ?>">
        <?php echo $d["dept_name"]; ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="doctor-grid">
    <?php foreach ($doctors as $doc): ?>
    <div class="card">
        <h3><?php echo $doc["full_name"]; ?></h3>
        <p class="muted"><?php echo $doc["specialization"]; ?></p>
        <p>
            <span class="pill"><?php echo $doc["dept_name"]; ?></span>
            <?php if ($doc["room"] != ""): ?>
                <span class="pill">Room <?php echo $doc["room"]; ?></span>
            <?php endif; ?>
        </p>
        <p class="muted"><?php echo $doc["available_time"]; ?></p>
        <p><strong><?php echo (int) $doc["consultation_fee"]; ?> Tk</strong> per visit</p>

        <?php if (Auth::role() == "patient"): ?>
        <a href="<?php echo BASE_URL; ?>/index.php?url=patient/book/<?php echo $doc["doctor_id"]; ?>"
           class="btn">Book appointment</a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
