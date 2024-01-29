<?php
ob_start();
session_start();

include_once "../config/database.php";
include_once "./functions.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

$result = [];
$request_id = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['requestId'])));
$flag = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['flag'])));
$hospital_id = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['hospitalId'])));

$mail = new PHPMailer(true);

// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->isSMTP();
//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->Host = 'smtp.gmail.com'; // old - "relay-hosting.secureserver.net" // new - "smtp.gmail.com"
$mail->SMTPAuth = true;
$mail->Username = "vaibhav.mandlik1@gmail.com";
$mail->Password = "Welcome@8242";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //
$mail->Port = 587; // old "25" // neww - "587"
$mail->CharSet = "UTF-8";

$mail->setFrom('vaibhav.mandlik1@gmail.com', 'LifeShare');

$mail->isHTML(true);

if ($flag == 'accept') {
    $result[] = update_query($con, "doner_request", "is_accepted='1',accepted_by=$hospital_id", "id=" . $request_id);
    $getUserDetails = json_decode(select_query($con, "*", "doner_request", "id=" . $request_id, "", "", ""));
    $getHospitalDetails = json_decode(select_query($con, "*", "hospital_master", "id=" . $hospital_id, "", "", ""));

    if (!empty($getUserDetails)) {
        $mail->addAddress($getUserDetails[0]->email, $getUserDetails[0]->fname . "  " . $getUserDetails[0]->lname);
        $mail->Subject = "Someone has accepted your donation request";
        $mail->Body = "Your request has been accepted by " . $getHospitalDetails[0]->name . "<br>Contact Number: " . $getHospitalDetails[0]->phone . "<br>Email Id: " . $getHospitalDetails[0]->email . "<br>Address: " . $getHospitalDetails[0]->address. "<br>Contact Person: " . $getHospitalDetails[0]->contact_person;
        if (!$mail->Send()) {
            //echo 'Email Failed To Send.'; 
        } else {
            // echo 'Email Was Successfully Sent.'; 
        }
    }
}

if($flag == 'deny')  {
    $result[] = insert_query($con, array('request_id', 'hospital_id'), array($request_id, $hospital_id), "denied_requests");
}

echo json_encode($result);
