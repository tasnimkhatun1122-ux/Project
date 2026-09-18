<?php

define("DB_HOST", "127.0.0.1");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "doctorconnect_db");

define("APP_NAME", "DoctorConnect");

define("BASE_URL", "/Web-Technology-/doctorconnect-mvc");

define("APP_PATH",  dirname(__DIR__) . "/app");
define("VIEW_PATH", APP_PATH . "/views");

define("DEFAULT_CONTROLLER", "home");
define("DEFAULT_ACTION",     "index");

$GLOBALS["TIME_SLOTS"] = array(
    "9:00 AM - 9:30 AM",  "10:00 AM - 10:30 AM", "11:00 AM - 11:30 AM",
    "4:00 PM - 4:30 PM",  "5:00 PM - 5:30 PM",   "6:00 PM - 6:30 PM",
    "7:00 PM - 7:30 PM",  "8:00 PM - 8:30 PM"
);

function time_slots() {
    return $GLOBALS["TIME_SLOTS"];
}

function valid_date($d) {
    return preg_match("/^\d{4}-\d{2}-\d{2}$/", $d) === 1;
}
