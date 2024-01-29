<?php
$page = "Add Will - MediClaim";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));
$getGuarantor = json_decode(select_query($con, "*", "guarantor_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$mediclaimName = $mediclaimNameErr = $mediPolicyNumber = $mediPolicyNumberErr = $mediclaimAmount = $mediclaimAmountErr = $mediclaimDate = $mediclaimDateErr = $mediclaimMaturity = $mediclaimMaturityErr = $mediclaimPremium = $mediclaimPremiumErr = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

$will_guarantor = ["", "", ""];
$will_guarantorErr = ["", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $mcDetails = json_decode(select_query($con, "*", "will_mediclaim_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($mcDetails)) {
        $mediclaimName = $mcDetails[0]->name;
        $mediPolicyNumber = $mcDetails[0]->policy_number;
        $mediclaimAmount = $mcDetails[0]->insured_amount;
        $mediclaimDate = $mcDetails[0]->issue_date;
        $mediclaimMaturity = $mcDetails[0]->maturity_date;
        $mediclaimPremium = $mcDetails[0]->premium;

        $mcBeneficiaryDetails = json_decode(select_query($con, "*", "will_mc_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));
        $willGuarantorDetails = json_decode(select_query($con, "*", "will_guarantor_master", "enabled='1' AND willid=" . $willDetails[0]->id, "", "", ""));

        if (!empty($mcBeneficiaryDetails)) {
            for ($i = 0; $i < count($mcBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $mcBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $mcBeneficiaryDetails[$i]->percentage;
            }
        }

        if (!empty($willGuarantorDetails)) {
            for ($i = 0; $i < count($willGuarantorDetails); $i++) {
                $will_guarantor[$i] = $willGuarantorDetails[$i]->guarantorId;
            }
        }
    }
}

if (isset($_POST['mcSub'])) {

    $errorFlag = false;

    // MediCalim Details
    $mediclaimName = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mediclaim_name'])));
    $mediPolicyNumber = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mediclaim_policy'])));
    $mediclaimAmount = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mediclaim_amount'])));
    $mediclaimDate = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mediclaim_date'])));
    $mediclaimMaturity = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mediclaim_maturity'])));
    $mediclaimPremium = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mediclaim_premium'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];
    $guarantors = isset($_POST['will_guarantor']) ? $_POST['will_guarantor'] : [];

    $mediclaimNameErr = $mediclaimName != '' ? '' : 'Please insert mediclaim name';
    $mediPolicyNumberErr = $mediPolicyNumber != '' ? '' : 'Please insert mediclaim policy number';
    $mediclaimAmountErr = $mediclaimAmount != '' ? '' : 'Please insert mediclaim amount';
    $mediclaimDateErr = $mediclaimDate != '' ? '' : 'Please insert mediclaim date';
    $mediclaimMaturityErr = $mediclaimMaturity != '' ? '' : 'Please insert mediclaim maturity';
    $mediclaimPremiumErr = $mediclaimPremium != '' ? '' : 'Please insert mediclaim premium';

    if ($mediclaimNameErr != '' || $mediPolicyNumberErr != '' || $mediclaimAmountErr != '' || $mediclaimDateErr != '' || $mediclaimMaturityErr != '' || $mediclaimPremiumErr != '')
        $errorFlag = true;

    $totalPercentage = 0;
    for ($i = 0; $i < count($beneficiaries); $i++) {
        if (empty($beneficiaries) || $beneficiaries[$i] == '') {
            $will_beneficiaryErr[$i] = 'Please select beneficiary';
            $errorFlag = true;
        } else if ($beneficiaries[$i] != '' && $beneficiary_percentages[$i] == '') {
            $will_beneficiary[$i] = $beneficiaries[$i];
            $will_beneficiary_percentageErr[$i] = 'Please enter percentage';
            $errorFlag = true;
        } else {
            $will_beneficiary[$i] = $beneficiaries[$i];
            $will_beneficiary_percentage[$i] = $beneficiary_percentages[$i];
            $totalPercentage += $will_beneficiary_percentage[$i];
        }
    }

    if ($totalPercentage > 100) {
        $errorFlag = true;
        echo '<script>swal({title: "Total percentage can not be greater than 100",type: "warning",button: "Ok"});</script>';
    }

    $nonLawyerGuarantor = 0;
    if (!empty($guarantors)) {
        for ($i = 0; $i < count($guarantors); $i++) {
            if ($guarantors[$i] == '') {
                $errorFlag = true;
                $will_guarantorErr[$i] = "Please select guarantor";
            } else {
                foreach ($getGuarantor as $g) {
                    if ($g->id == $guarantors[$i] && !$g->islawyer)
                        $nonLawyerGuarantor++;
                }
                $will_guarantor[$i] = $guarantors[$i];
            }
        }

        if ($nonLawyerGuarantor == 0 || $nonLawyerGuarantor == 1) {
            $errorFlag = true;
            echo '<script>swal({title: "Please select atleast 2 non-lawyer guarantors",type: "warning",button: "Ok"});</script>';
        }
    } else {
        $errorFlag = true;
        echo '<script>swal({title: "Please select guarantors",type: "warning",button: "Ok"});</script>';
    }

    if (!$errorFlag && isset($_GET['id'])) {

        $willId = decryption($_GET['id']);

        // MediClaim Data Insertion
        $insertedMedi = "";
        try {

            if (isset($_GET['edit']) && $_GET['edit']) {
                update_query($con, "will_fd_master", "enabled='0'", "willId=" . $willId);
                update_query($con, "will_guarantor_master", "enabled='0'", "willId='$willId'");
            }

            $insertedMedi = json_decode(insert_query($con, array('willId', 'name', 'policy_number', 'insured_amount', 'issue_date', 'maturity_date', 'premium', 'createdBy', 'updatedBy'), array($willId, $mediclaimName, $mediPolicyNumber, $mediclaimAmount, $mediclaimDate, $mediclaimMaturity, $mediclaimPremium, $_SESSION['uid'], $_SESSION['uid']), "will_mediclaim_master"));
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting mediclaim details.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedMedi != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit']) {
                update_query($con, "will_mc_beneficiary_master", "enabled='0'", "willId=" . $willId);
                update_query($con, "will_guarantor_master", "enabled='0'", "willId=" . $willId);
            }

            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_mc_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            for ($i = 0; $i < count($will_guarantor); $i++) {
                try {
                    if (!empty($will_guarantor[$i])) {
                        $insertedGuarantor[$i] = json_decode(insert_query($con, array('willId', 'guarantorId', 'createdBy', 'updatedBy'), array($willId, $will_guarantor[$i], $_SESSION['uid'], $_SESSION['uid']), "will_guarantor_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting gurantors' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                echo '<script>swal({title: "Will saved successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "will.php";});</script>';
            }
        }
    }
}
?>
<!--Container Main start-->
<div class="m-3">
    <div class="row">
        <h4 class="text-uppercase"><?php echo $page; ?></h4>
    </div>
</div>
<div class="container-fluid pt-3 mb-4">
    <form action="" method="POST">

        <div class="card">
            <div class="card-header bg-white" id="headingHouseProp">
                <h5 class="mb-0">
                    MediClaim
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="will_mediclaim_name" placeholder="Enter mediclaim name" <?php echo isset($mediclaimName) && $mediclaimName != '' ? 'value="' . $mediclaimName . '"' : ''; ?>>
                            <?php echo $mediclaimNameErr != '' ? '<span class="text-danger">' . $mediclaimNameErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Policy Number</label>
                            <input type="text" class="form-control" name="will_mediclaim_policy" placeholder="Enter policy number" <?php echo isset($mediPolicyNumber) && $mediPolicyNumber != '' ? 'value="' . $mediPolicyNumber . '"' : ''; ?>>
                            <?php echo $mediPolicyNumberErr != '' ? '<span class="text-danger">' . $mediPolicyNumberErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Amount Insured</label>
                            <input type="text" class="form-control" name="will_mediclaim_amount" placeholder="Enter mediclaim amount" <?php echo isset($mediclaimAmount) && $mediclaimAmount != '' ? 'value="' . $mediclaimAmount . '"' : ''; ?>>
                            <?php echo $mediclaimAmountErr != '' ? '<span class="text-danger">' . $mediclaimAmountErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" class="form-control" name="will_mediclaim_date" <?php echo isset($mediclaimDate) && $mediclaimDate != '' ? 'value="' . $mediclaimDate . '"' : ''; ?>>
                            <?php echo $mediclaimDateErr != '' ? '<span class="text-danger">' . $mediclaimDateErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Maturity Date</label>
                            <input type="date" class="form-control" name="will_mediclaim_maturity" <?php echo isset($mediclaimMaturity) && $mediclaimMaturity != '' ? 'value="' . $mediclaimMaturity . '"' : ''; ?>>
                            <?php echo $mediclaimMaturityErr != '' ? '<span class="text-danger">' . $mediclaimMaturityErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Premium</label>
                            <input type="text" class="form-control" name="will_mediclaim_premium" <?php echo isset($mediclaimPremium) && $mediclaimPremium != '' ? 'value="' . $mediclaimPremium . '"' : ''; ?>>
                            <?php echo $mediclaimPremiumErr != '' ? '<span class="text-danger">' . $mediclaimPremiumErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <!-- Beneficiary Details -->
                <div class="row">
                    <p class="px-0 w-100"><b>Beneficiary Details</b></p>

                    <?php
                    for ($i = 0; $i < count($will_beneficiary); $i++) { ?>
                        <div class="col-sm-12 col-md-6 col-lg-3 px-0">
                            <div class="form-group">
                                <label>Beneficiary <?php echo $i + 1; ?></label>
                                <select class="form-control" name="will_beneficiary[<?php echo $i; ?>]">
                                    <option selected disabled>Select Beneficiary</option>

                                    <?php
                                    foreach ($getBeneficiary as $beneficiary) { ?>
                                        <option value="<?php echo $beneficiary->id; ?>" <?php echo $will_beneficiary[$i] == $beneficiary->id ? 'selected' : ''; ?>><?php echo $beneficiary->name; ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <?php echo $will_beneficiaryErr[$i] != '' ? '<span class="text-danger">' . $will_beneficiaryErr[$i] . '</span>' : ''; ?>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <label>Percentage</label>
                            <div class="form-group input-group">
                                <input type="number" class="form-control" placeholder="Enter percentage" name="will_beneficiary_percentage[<?php echo $i; ?>]" aria-label="Enter percentage" aria-describedby="basic-addon2" <?php echo isset($will_beneficiary_percentage[$i]) && $will_beneficiary_percentage[$i] != '' ? 'value="' . $will_beneficiary_percentage[$i] . '"' : ''; ?>>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">%</span>
                                </div>
                            </div>
                            <?php echo $will_beneficiary_percentageErr[$i] != '' ? '<span class="text-danger">' . $will_beneficiary_percentageErr[$i] . '</span>' : ''; ?>
                        </div>
                    <?php }
                    ?>
                </div>

                <!-- Gaurantor Details -->
                <div class="row">
                    <p class="px-0 w-100"><b>Gaurantor Details</b></p>

                    <?php
                    for ($i = 0; $i < count($will_guarantor); $i++) { ?>
                        <div class="col-sm-12 col-md-6 col-lg-3 pl-0">
                            <div class="form-group">
                                <label>Gaurantor <?php $i + 1; ?></label>
                                <select class="form-control" name="will_guarantor[]">
                                    <option selected disabled>Select Gaurantor</option>

                                    <?php
                                    foreach ($getGuarantor as $guarantor) { ?>
                                        <option <?php echo isset($will_guarantor[$i]) && $will_guarantor[$i] == $guarantor->id ? 'selected' : ''; ?> value="<?php echo $guarantor->id; ?>">
                                            <?php echo $guarantor->name;
                                            echo $guarantor->islawyer ? '(Lawyer)' : ''; ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <?php echo $will_guarantorErr[$i] != '' ? '<span class="text-danger">' . $will_guarantorErr[$i] . '</span>' : ''; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <button type="submit" name="mcSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>