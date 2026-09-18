<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoctorConnect — Doctor Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>DoctorConnect</h1>
            <p class="sub">Sign in to manage your appointments</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="index.php?route=login.submit">
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-block">Log in</button>
            </form>
            <p class="sub" style="margin-top:18px;">Test account: doctor@doctorconnect.test / doctor123</p>
        </div>
    </div>
</body>
</html>
