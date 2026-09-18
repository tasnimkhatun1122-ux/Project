<div class="hero">
    <h1>Book a doctor without standing in a queue.</h1>
    <p class="muted">DoctorConnect is an online appointment management system for
       hospitals &mdash; patients, doctors, receptionists and administrators in one place.</p>

    <div class="hero-actions">
        <a href="<?php echo BASE_URL; ?>/index.php?url=auth/register" class="btn">Create an account</a>
        <a href="<?php echo BASE_URL; ?>/index.php?url=auth/login" class="btn btn-ghost">Log in</a>
    </div>
</div>

<div class="stat-row">
    <div class="stat"><span class="stat-num"><?php echo $doctorCount; ?></span>
        <span class="stat-label">Doctors available</span></div>
    <div class="stat"><span class="stat-num"><?php echo $deptCount; ?></span>
        <span class="stat-label">Departments</span></div>
    <div class="stat"><span class="stat-num">4</span>
        <span class="stat-label">User roles</span></div>
</div>

<div class="card">
    <h2>Departments</h2>
    <div class="table-wrap"><table>
        <tr><th>Department</th><th>Doctors</th></tr>
        <?php foreach ($departments as $d): ?>
        <tr>
            <td><?php echo $d["dept_name"]; ?></td>
            <td><?php echo $d["doctor_count"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
