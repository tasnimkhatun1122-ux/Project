<h1>Overview</h1>
<p class="muted">Everything happening across the hospital.</p>

<div class="stat-row">
    <div class="stat"><span class="stat-num"><?php echo array_sum($counts); ?></span>
        <span class="stat-label">Total appointments</span></div>
    <div class="stat"><span class="stat-num"><?php echo $doctorCount; ?></span>
        <span class="stat-label">Doctors</span></div>
    <div class="stat"><span class="stat-num"><?php echo $deptCount; ?></span>
        <span class="stat-label">Departments</span></div>
    <div class="stat"><span class="stat-num"><?php echo isset($counts["pending"]) ? $counts["pending"] : 0; ?></span>
        <span class="stat-label">Pending</span></div>
</div>

<div class="card">
    <div class="nav-label">Appointments by status</div>
    <div class="table-wrap"><table>
        <tr><th>Status</th><th>Total</th></tr>
        <?php foreach (array("pending", "confirmed", "completed", "cancelled") as $s): ?>
        <tr>
            <td><span class="status status-<?php echo $s; ?>"><?php echo $s; ?></span></td>
            <td><?php echo isset($counts[$s]) ? $counts[$s] : 0; ?></td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>

<div class="card">
    <div class="nav-label">By department</div>
    <div class="table-wrap"><table>
        <tr><th>Department</th><th>Doctors</th><th>Appointments</th></tr>
        <?php foreach ($deptStats as $d): ?>
        <tr>
            <td><?php echo $d["dept_name"]; ?></td>
            <td><?php echo $d["doctors"]; ?></td>
            <td><?php echo $d["appointments"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>

<div class="card">
    <div class="nav-label">Recent appointments</div>
    <div class="table-wrap"><table>
        <tr><th>Patient</th><th>Doctor</th><th>Department</th><th>Date &amp; time</th><th>Status</th></tr>
        <?php foreach ($recent as $a): ?>
        <tr>
            <td><?php echo $a["patient_name"]; ?></td>
            <td><?php echo $a["doctor_name"]; ?></td>
            <td><span class="pill"><?php echo $a["dept_name"]; ?></span></td>
            <td><?php echo date("d M Y", strtotime($a["appt_date"])); ?>, <?php echo $a["time_slot"]; ?></td>
            <td><span class="status status-<?php echo $a["status"]; ?>"><?php echo $a["status"]; ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
