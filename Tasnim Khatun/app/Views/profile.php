<?php $activePage = 'profile'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoctorConnect — My Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main">
        <div class="topbar">
            <div>
                <h1>My Profile</h1>
                <p>Update your specialization, consultation fee and available time</p>
            </div>
        </div>

        <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="panel" style="max-width:640px;">
            <div class="panel-header"><h2>Account details</h2></div>
            <div class="profile-grid">
                <div class="field">
                    <label>Name</label>
                    <input type="text" value="<?= htmlspecialchars($doctor['full_name']) ?>" disabled>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="text" value="<?= htmlspecialchars($doctor['email']) ?>" disabled>
                </div>
                <div class="field">
                    <label>Department</label>
                    <input type="text" value="<?= htmlspecialchars($doctor['dept_name']) ?>" disabled>
                </div>
                <div class="field">
                    <label>Phone</label>
                    <input type="text" value="<?= htmlspecialchars($doctor['phone'] ?: '—') ?>" disabled>
                </div>
            </div>
            <p class="sub" style="margin-top:-4px;">Name, email, phone and department are managed by the admin.</p>
        </div>

        <div class="panel" style="max-width:640px;">
            <div class="panel-header"><h2>Practice details</h2></div>
            <form method="post" action="index.php?route=profile.update">
                <div class="profile-grid">
                    <div class="field">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization"
                               value="<?= htmlspecialchars($doctor['specialization']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="consultation_fee">Consultation fee (৳)</label>
                        <input type="number" step="0.01" min="0" id="consultation_fee" name="consultation_fee"
                               value="<?= htmlspecialchars($doctor['consultation_fee']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="available_time">Available time</label>
                        <input type="text" id="available_time" name="available_time"
                               placeholder="e.g. 9:00 AM - 2:00 PM"
                               value="<?= htmlspecialchars($doctor['available_time']) ?>">
                    </div>
                    <div class="field">
                        <label for="room">Room</label>
                        <input type="text" id="room" name="room"
                               placeholder="e.g. R-204"
                               value="<?= htmlspecialchars($doctor['room']) ?>">
                    </div>
                </div>
                <button type="submit" class="btn">Save changes</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
