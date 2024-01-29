<?php
include_once "config.php";

$mysqliDriver = new mysqli_driver();
$mysqliDriver->report_mode = (MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$con = new mysqli(db_servername, db_username, db_password);

// Check connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

mysqli_select_db($con, db_database);