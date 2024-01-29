<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'])
	header("Location: dashboard.php");
else
	header("Location: login.php");
