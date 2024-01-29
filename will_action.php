<?php
$page = "Dashboard";
include_once("./adders/header.php");

$id = $action = "";
if (isset($_GET['id']))
    $id = decryption($_GET['id']);

if (isset($_GET['gid']))
    $gid = decryption($_GET['gid']);

if (isset($_GET['action']))
    $action = $_GET['action'];

$result = $willUpdateResult = '';
$isWillUpdated = false;

if ($action == 'Ae' && !empty($id) && !empty($gid)) {
    $result = json_decode(update_query($con, "will_guarantor_master", "hasApproved='1', updatedBy=" . $_SESSION['uid'], "enabled ='1' AND willId=" . $id . " AND guarantorId=" . $gid));

    // Check if will have atleast 2 approvals by non-lawyer guarantor
    $will_guarantors_approval_data = json_decode(select_query($con, "wgm.id", "will_guarantor_master wgm LEFT JOIN guarantor_master gm ON wgm.guarantorId=gm.id", "wgm.enabled='1' AND wgm.hasApproved='1' AND wgm.willId=$id AND gm.isLawyer!='1'", "", "", ""));

    if (count($will_guarantors_approval_data) >= 2) {
        // update will as approved
        $isWillUpdated = true;
        $willUpdateResult = json_decode(update_query($con, "will_master", "approved='1'", "id=$id"));
    }
} else if ($action == 'Rt' && !empty($id) && !empty($gid))
    $result = json_decode(update_query($con, "will_guarantor_master", "hasApproved='2'", "enabled ='1' AND id=" . $id . " AND guarantorId=" . $gid));

if ($result && $action == 'Ae')
    echo '<script>swal({title: "Will approved successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "dashboard.php";});</script>';
else if ($result && $action == 'Rt')
    echo '<script>swal({title: "Will rejected successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "dashboard.php";});</script>';
else
    echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"}).then(function() {window.location.href = "dashboard.php";});</script>';

include_once("./adders/footer.php");
