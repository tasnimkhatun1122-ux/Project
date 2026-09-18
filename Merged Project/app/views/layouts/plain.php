<?php $title = isset($pageTitle) ? $pageTitle : APP_NAME; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?> &mdash; <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>
<body>

<div class="plain-top">
    <div class="inner">
        <?php echo logo_mark(28); ?>
        <span class="logo"><?php echo APP_NAME; ?></span>
        <span class="tagline">Online Doctor Appointment Management System</span>
    </div>
</div>

<div class="plain-wrap">
    <?php echo $content; ?>
</div>

</body>
</html>
