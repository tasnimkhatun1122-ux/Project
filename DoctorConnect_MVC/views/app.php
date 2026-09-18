<?php
$role  = Auth::role();
$here  = isset($_GET["url"]) ? $_GET["url"] : "";
$title = isset($pageTitle) ? $pageTitle : APP_NAME;

$initials = "";
$bits = explode(" ", str_replace("Dr. ", "", Auth::name()));
if (count($bits) > 0 && $bits[0] != "") {
    $initials = strtoupper(substr($bits[0], 0, 1));
    if (count($bits) > 1) {
        $initials .= strtoupper(substr($bits[count($bits) - 1], 0, 1));
    }
}

function nav_item($route, $label, $iconName, $here) {
    $on = (strpos($here, $route) === 0) ? ' class="on"' : "";
    echo '<a href="' . BASE_URL . '/index.php?url=' . $route . '"' . $on . '>'
       . icon($iconName) . '<span>' . $label . '</span></a>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?> &mdash; <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
</head>
<body>

<div class="shell">
    <nav class="sidebar">
        <a href="<?php echo BASE_URL; ?>/index.php?url=<?php echo Auth::dashboardFor($role); ?>" class="brand">
            <?php echo logo_mark(30); ?>
            <span class="logo"><?php echo APP_NAME; ?></span>
        </a>

        <div class="nav-label"><?php
            if ($role == "admin")             { echo "Administration"; }
            elseif ($role == "receptionist")  { echo "Front desk"; }
            else                              { echo "Menu"; }
        ?></div>

        <div class="navlinks">
            <?php
            if ($role == "patient") {
                nav_item("patient/dashboard",    "Dashboard",       "grid",     $here);
                nav_item("patient/doctors",      "Find Doctors",    "search",   $here);
                nav_item("patient/appointments", "My Appointments", "calendar", $here);
            } elseif ($role == "doctor") {
                nav_item("doctor/dashboard",     "Appointments",    "calendar",    $here);
                nav_item("doctor/profile",       "My Profile",      "stethoscope", $here);
            } elseif ($role == "receptionist") {
                nav_item("reception/dashboard",  "Front Desk",      "clock",  $here);
                nav_item("reception/walkin",     "Book Walk-in",    "plus",   $here);
                nav_item("patient/doctors",      "Doctors",         "search", $here);
            } elseif ($role == "admin") {
                nav_item("admin/dashboard",      "Overview",        "grid",   $here);
                nav_item("admin/doctors",        "Doctors",         "users",  $here);
                nav_item("admin/departments",    "Departments",     "layers", $here);
            }
            ?>
        </div>

        <div class="nav-label">Account</div>
        <div class="navlinks">
            <?php nav_item("profile/index", "My Account", "user", $here); ?>
        </div>

        <div class="side-foot">
            <div class="whoami">
                <span class="avatar"><?php echo $initials; ?></span>
                <span>
                    <span class="whoami-name"><?php echo Auth::name(); ?></span>
                    <span class="role role-<?php echo $role; ?>"><?php echo $role; ?></span>
                </span>
            </div>
            <a href="<?php echo BASE_URL; ?>/index.php?url=auth/logout" class="btn-logout">
                <?php echo icon("logout"); ?> <span>Log out</span>
            </a>
        </div>
    </nav>

    <main class="main">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </main>
</div>

</body>
</html>
