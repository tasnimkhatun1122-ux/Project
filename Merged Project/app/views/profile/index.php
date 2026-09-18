<h1>My account</h1>
<p class="muted">Update your details or change your password.</p>

<?php if ($error != ""): ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success != ""): ?><div class="alert-ok"><?php echo $success; ?></div><?php endif; ?>

<div class="card">
    <div class="nav-label">Account details</div>
    <p class="muted">
        Role: <span class="role role-<?php echo $user["role"]; ?>"><?php echo $user["role"]; ?></span>
        &middot; Member since <?php echo date("d M Y", strtotime($user["created_at"])); ?>
    </p>

    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=profile/index">
        <input type="hidden" name="action" value="details">

        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo $user["full_name"]; ?>">

        <label for="email">Email</label>
        <input type="text" id="email" name="email" value="<?php echo $user["email"]; ?>">

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?php echo $user["phone"]; ?>">

        <input type="submit" value="Save details" class="btn">
    </form>
</div>

<div class="card">
    <div class="nav-label">Change password</div>

    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=profile/index">
        <input type="hidden" name="action" value="password">

        <label for="current_password">Current password</label>
        <input type="password" id="current_password" name="current_password">

        <label for="new_password">New password</label>
        <input type="password" id="new_password" name="new_password">

        <label for="confirm_password">Confirm new password</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <input type="submit" value="Change password" class="btn">
    </form>
</div>
