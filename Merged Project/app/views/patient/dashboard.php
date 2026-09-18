<h1>Good day, <?php echo explode(" ", Auth::name())[0]; ?></h1>
<p class="muted">Here is what is happening with your appointments today.</p>

<a href="<?php echo BASE_URL; ?>/index.php?url=patient/doctors" class="btn">
    <?php echo icon("search"); ?> Find a doctor
</a>

<div class="stat-row">
    <div class="stat">
        <span class="stat-num"><?php echo count($upcoming); ?></span>
        <span class="stat-label">Upcoming appointments</span>
    </div>
    <div class="stat">
        <span class="stat-num"><?php echo isset($counts["completed"]) ? $counts["completed"] : 0; ?></span>
        <span class="stat-label">Completed visits</span>
    </div>
    <div class="stat">
        <span class="stat-num"><?php echo $deptCount; ?></span>
        <span class="stat-label">Departments available</span>
    </div>
</div>

<?php if (count($upcoming) > 0): $next = $upcoming[0]; ?>
<div class="card">
    <div class="nav-label">Next appointment</div>
    <h2><?php echo $next["doctor_name"]; ?> &mdash; <?php echo $next["dept_name"]; ?></h2>
    <p class="muted">
        <?php echo date("l, j M Y", strtotime($next["appt_date"])); ?> &middot;
        <?php echo $next["time_slot"]; ?> &middot;
        Room <?php echo $next["room"]; ?> &middot;
        Fee <?php echo (int) $next["consultation_fee"]; ?> Tk &middot;
        <span class="status status-<?php echo $next["status"]; ?>"><?php echo $next["status"]; ?></span>
    </p>
    <a href="<?php echo BASE_URL; ?>/index.php?url=patient/appointments" class="btn">View details</a>
</div>
<?php endif; ?>

<div class="card">
    <div class="nav-label">Recent appointments</div>
    <div class="table-wrap"><table>
        <tr><th>Doctor</th><th>Department</th><th>Date &amp; time</th><th>Status</th></tr>
        <?php foreach ($recent as $a): ?>
        <tr>
            <td><?php echo $a["doctor_name"]; ?></td>
            <td><span class="pill"><?php echo $a["dept_name"]; ?></span></td>
            <td><?php echo date("d M Y", strtotime($a["appt_date"])); ?>, <?php echo $a["time_slot"]; ?></td>
            <td><span class="status status-<?php echo $a["status"]; ?>"><?php echo $a["status"]; ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table></div>
    <a href="<?php echo BASE_URL; ?>/index.php?url=patient/appointments" class="btn btn-ghost">
        See all my appointments
    </a>
</div>
