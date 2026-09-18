<div class="split">

    <aside class="split-brand">
        <a href="<?php echo BASE_URL; ?>/index.php" class="split-logo">
            <?php echo logo_mark(40); ?>
            <span><?php echo APP_NAME; ?></span>
        </a>

        <h1 class="split-title">Create your<br>patient account.</h1>

        <p class="split-sub">Register once, then book appointments with any doctor
           in the hospital and track them online.</p>

        <ul class="split-points">
            <li>Free to register</li>
            <li>See live slot availability</li>
            <li>Cancel or reschedule anytime</li>
        </ul>
    </aside>

    <main class="split-form">
        <div class="card auth-card">
            <h1>Register</h1>
            <p class="muted">Create a patient account to book appointments.</p>

            <?php if ($error != ""): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=auth/register">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo $name; ?>">

                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php echo $email; ?>">

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password">

                <label for="confirm">Confirm password</label>
                <input type="password" id="confirm" name="confirm">

                <input type="submit" name="submit" value="Create account" class="btn btn-block">
            </form>

            <p class="auth-alt">Already have an account?
               <a href="<?php echo BASE_URL; ?>/index.php?url=auth/login">Log in</a></p>
        </div>
    </main>

</div>
