<?php $activePage = $activePage ?? ''; ?>
<aside class="sidebar">
    <div class="brand">Doctor<span>Connect</span></div>
    <nav>
        <a href="index.php?route=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">My Schedule</a>
        <a href="index.php?route=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">My Profile</a>
        <a href="index.php?route=logout">Log out</a>
    </nav>
    <div class="doctor-tag">Logged in as<br><strong style="color:#fff"><?= htmlspecialchars($_SESSION['full_name']) ?></strong></div>
</aside>
