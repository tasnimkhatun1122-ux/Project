<?php $activePage = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoctorConnect — My Schedule</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main">
        <div class="topbar">
            <div>
                <h1>My Schedule</h1>
                <p><?= htmlspecialchars($doctor['specialization']) ?> · <?= htmlspecialchars($doctor['dept_name']) ?></p>
            </div>
        </div>

        <?php if ($flash === 'completed'): ?>
            <div class="alert alert-success">Appointment marked as completed.</div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card"><div class="num"><?= $total ?></div><div class="label">Total for this day</div></div>
            <div class="stat-card"><div class="num"><?= $statusCounts['pending'] ?></div><div class="label">Pending</div></div>
            <div class="stat-card"><div class="num"><?= $statusCounts['confirmed'] ?></div><div class="label">Confirmed</div></div>
            <div class="stat-card"><div class="num"><?= $statusCounts['completed'] ?></div><div class="label">Completed</div></div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>Appointments</h2>
                <form method="get" class="date-filter">
                    <input type="hidden" name="route" value="dashboard">
                    <input type="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" onchange="this.form.submit()">
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Diagnosis / Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($appointments->num_rows === 0): ?>
                    <tr class="empty-row"><td colspan="6">No appointments on this date.</td></tr>
                <?php else: ?>
                    <?php while ($a = $appointments->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['time_slot']) ?></td>
                            <td><?= htmlspecialchars($a['patient_name']) ?></td>
                            <td><?= htmlspecialchars($a['phone'] ?: '—') ?></td>
                            <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
                            <td>
                                <?php if ($a['status'] === 'completed'): ?>
                                    <?= htmlspecialchars($a['diagnosis'] ?: '—') ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['status'] !== 'completed' && $a['status'] !== 'cancelled'): ?>
                                    <button class="action-link"
                                        onclick="openCompleteModal(<?= $a['appt_id'] ?>, '<?= htmlspecialchars($a['patient_name'], ENT_QUOTES) ?>')">
                                        Mark completed
                                    </button>
                                <?php else: ?>
                                    <span style="color:#9aa3a1;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Mark-completed modal -->
<div class="modal-overlay" id="completeModal">
    <div class="modal-box">
        <h3>Complete visit</h3>
        <p class="sub" id="completePatientName"></p>
        <form method="post" action="index.php?route=appointment.complete">
            <input type="hidden" name="appt_id" id="completeApptId">
            <input type="hidden" name="date" value="<?= htmlspecialchars($selectedDate) ?>">
            <div class="field">
                <label for="diagnosis">Diagnosis</label>
                <input type="text" id="diagnosis" name="diagnosis" maxlength="120" placeholder="e.g. Mild hypertension" required>
            </div>
            <div class="field">
                <label for="visit_note">Visit note</label>
                <textarea id="visit_note" name="visit_note" rows="3" placeholder="Follow-up advice, prescription summary, etc."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeCompleteModal()">Cancel</button>
                <button type="submit" class="btn">Save & mark completed</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCompleteModal(apptId, patientName) {
    document.getElementById('completeApptId').value = apptId;
    document.getElementById('completePatientName').textContent = 'Patient: ' + patientName;
    document.getElementById('completeModal').classList.add('open');
}
function closeCompleteModal() {
    document.getElementById('completeModal').classList.remove('open');
}
</script>
</body>
</html>
