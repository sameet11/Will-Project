<?php
header('Access-Control-Allow-Origin: *');
define('SITE_ROOT', realpath(dirname(__FILE__)));

session_start();

include_once "./config/database.php";
include_once "./controller/functions.php";

if (isset($_SESSION['logged_in']))
    $user = json_decode(select_query($con, "*", "user_master", "id=" . $_SESSION['uid'], "", "", ""));

$will_pages = ['Will', 'Add Will', 'Add Will - House Property', 'Add Will - Bank Account', 'Add Will - Shares', 'Add Will - Mutual Fund', 'Add Will - Insurance', 'Add Will - MediClaim'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords($page); ?> | Will Portal</title>

    <link type="text/css" rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="./assets/css/sweetalert2.min.css" type="text/css">
    <link type="text/css" rel="stylesheet" href="./assets/css/style.css">

    <script src="./assets/js/jquery.js"></script>
    <script src="./assets/js/sweetalert2.min.js"></script>
    <script src="./assets/js/font-awesome.js" crossorigin="anonymous"></script>

    <!-- Datatable CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
</head>

<body id="body-pd">
    <?php if ($page != 'Will Download') { ?>
        <header class="header mb-5" id="header">
            <div class="header_toggle"> <i class='fas fa-bars' id="header-toggle"></i> </div>

            <?php if (isset($_SESSION['logged_in'])) { ?>
                <div class="border-right ml-auto mr-2">
                    <h6 class="mr-2"><?php echo ucwords($user[0]->first_name . ' ' . $user[0]->last_name); ?></h6>
                </div>
                <div class="dropdown">
                    <a class="nav-link nav-user text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="rounded-circle bg-dark p-3"><?php echo ucwords($user[0]->first_name[0] . '' . $user[0]->last_name[0]); ?></span>
                    </a>
                    <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuLink">
                        <li class="border-bottom"><a class="dropdown-item" href="./profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
                    </ul>
                </div>
            <?php } else { ?>
                <a href="login.php" class="nav-link">Login</a>
            <?php } ?>
        </header>
        <div class="l-navbar" id="nav-bar">
            <nav class="sidenav">
                <div>
                    <a href="index.php" class="nav_logo">
                        <i class="fas fa-scroll nav_logo-icon"></i>
                        <span class="nav_logo-name">Will Portal</span>
                    </a>

                    <div class="nav_list">
                        <a href="dashboard.php" class="nav_link <?php echo $page == 'Dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-th"></i>
                            <span class="nav_name">Dashboard</span>
                        </a>

                        <a href="./guarantor.php" class="nav_link <?php echo $page == 'Guarantor' ? 'active' : ''; ?>">
                            <i class="fas fa-user-check"></i>
                            <span class="nav_name">Add Guarantor</span>
                        </a>
                        <a href="./beneficiary.php" class="nav_link <?php echo $page == 'Beneficiary' ? 'active' : ''; ?>">
                            <i class="fas fa-award"></i>
                            <span class="nav_name">Add Beneficiary</span>
                        </a>
                        <a href="./will.php" class="nav_link <?php echo in_array($page, $will_pages) ? 'active' : ''; ?>">
                            <i class="fas fa-certificate"></i>
                            <span class="nav_name">Add Will</span>
                        </a>
                    </div>
                </div>
                <?php if (isset($_SESSION['uid'])) { ?>
                    <a href="logout.php" class="nav_link"> <i class="fas fa-sign-out-alt"></i> <span class="nav_name">SignOut</span> </a>
                <?php } ?>
            </nav>
        </div>
    <?php } ?>