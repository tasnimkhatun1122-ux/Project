<div class="split">

    <aside class="split-brand">
        <a href="<?php echo BASE_URL; ?>/index.php" class="split-logo">
            <?php echo logo_mark(40); ?>
            <span><?php echo APP_NAME; ?></span>
        </a>

        <h1 class="split-title">Book your doctor,<br>skip the queue.</h1>

        <p class="split-sub">An online appointment system for the hospital reception
           desk &mdash; patients, doctors, receptionists and admin in one place.</p>

        <ul class="split-points">
            <li>Search doctors by department</li>
            <li>Book an open time slot instantly</li>
            <li>Track appointment status online</li>
        </ul>
    </aside>

    <main class="split-form">
        <div class="card auth-card">
            <h1>Log in</h1>
            <p class="muted">Enter your account details to continue.</p>

            <?php if ($error != ""): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=auth/login">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php echo $email; ?>"
                       placeholder="you@example.com">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="********">

                <input type="submit" name="submit" value="Log in" class="btn btn-block">
            </form>

            <p class="auth-alt">Don&rsquo;t have an account?
               <a href="<?php echo BASE_URL; ?>/index.php?url=auth/register">Register</a></p>

            <div class="demo-box">
                <strong>Demo accounts</strong> (password for all: <code>1234</code>)<br>
                Admin: admin@doctorconnect.com<br>
                Doctor: salma@doctorconnect.com<br>
                Receptionist: reception@doctorconnect.com<br>
                Patient: nusrat@gmail.com
            </div>
        </div>
    </main>

</div>
