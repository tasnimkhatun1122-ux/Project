<?php
session_start();
$action=$_GET['action']??'login';
$public=['login','register'];
if(!isset($_SESSION['user'])&&!in_array($action,$public,true)){header('Location:index.php?action=login');exit;}
$map=['login'=>'controllers/auth.php','register'=>'controllers/auth.php','logout'=>'controllers/auth.php','dashboard'=>'controllers/dashboard.php','profile'=>'controllers/profile.php','doctors'=>'controllers/patient.php','book'=>'controllers/patient.php','appointments'=>'controllers/patient.php','cancel'=>'controllers/patient.php','doctor_schedule'=>'controllers/doctor.php','complete_visit'=>'controllers/doctor.php','reception'=>'controllers/reception.php','checkin'=>'controllers/reception.php','noshow'=>'controllers/reception.php','walkin'=>'controllers/reception.php','admin'=>'controllers/admin.php','save_doctor'=>'controllers/admin.php','delete_doctor'=>'controllers/admin.php','save_department'=>'controllers/admin.php','delete_department'=>'controllers/admin.php'];
if(!isset($map[$action]))$action='dashboard'; require __DIR__.'/'.$map[$action];
?>