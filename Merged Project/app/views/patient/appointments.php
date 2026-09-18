<h1>My appointments</h1>
<p class="muted">Every appointment you have booked, newest first.</p>

<div class="tabs">
    <a href="<?php echo BASE_URL; ?>/index.php?url=patient/appointments"
       class="tab<?php echo $status == "" ? " on" : ""; ?>">
        All (<?php echo array_sum($counts); ?>)
    </a>
    <?php foreach (array("pending", "confirmed", "completed", "cancelled") as $s): ?>
    <a href="<?php echo BASE_URL; ?>/index.php?url=patient/appointments&amp;status=<?php echo $s; ?>"
       class="tab<?php echo $status == $s ? " on" : ""; ?>">
        <?php echo ucfirst($s); ?> (<?php echo isset($counts[$s]) ? $counts[$s] : 0; ?>)
    </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="table-wrap"><table>
        <tr>
            <th>Doctor</th><th>Department</th><th>Date &amp; time</th>
            <th>Room</th><th>Fee</th><th>Status</th><th></th>
        </tr>
        <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?php echo $a["doctor_name"]; ?></td>
            <td><span class="pill"><?php echo $a["dept_name"]; ?></span></td>
            <td><?php echo date("d M Y", strtotime($a["appt_date"])); ?>, <?php echo $a["time_slot"]; ?></td>
            <td><?php echo $a["room"]; ?></td>
            <td><?php echo (int) $a["consultation_fee"]; ?> Tk</td>
            <td><span class="status status-<?php echo $a["status"]; ?>"><?php echo $a["status"]; ?></span></td>
            <td>
                <?php if ($a["status"] == "pending" || $a["status"] == "confirmed"): ?>
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=patient/appointments">
                    <input type="hidden" name="cancel_id" value="<?php echo $a["appt_id"]; ?>">
                    <button type="submit" class="btn btn-small btn-ghost">Cancel</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
