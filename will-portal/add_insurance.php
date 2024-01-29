<?php
$page = "Add Will - Insurance";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$insuranceName = $insuranceNameErr = $policyNumber = $policyNumberErr = $insuranceAmount = $insuranceAmountErr = $insuranceDate = $insuranceDateErr = $insuranceMaturity = $insuranceMaturityErr = $insurancePremium = $insurancePremiumErr = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $insuranceDetails = json_decode(select_query($con, "*", "will_insurance_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($insuranceDetails)) {
        $insuranceName = $insuranceDetails[0]->name;
        $policyNumber = $insuranceDetails[0]->policy_number;
        $insuranceAmount = $insuranceDetails[0]->insured_amount;
        $insuranceDate = $insuranceDetails[0]->issue_date;
        $insuranceMaturity = $insuranceDetails[0]->maturity_date;
        $insurancePremium = $insuranceDetails[0]->premium;

        $insuranceBeneficiaryDetails = json_decode(select_query($con, "*", "will_insurance_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));

        if (!empty($insuranceBeneficiaryDetails)) {
            for ($i = 0; $i < count($insuranceBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $insuranceBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $insuranceBeneficiaryDetails[$i]->percentage;
            }
        }
    }
}

if (isset($_POST['insuranceSub'])) {

    $errorFlag = false;

    // Insurance Details
    $insuranceName = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_insurance_name'])));
    $policyNumber = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_insurance_policy'])));
    $insuranceAmount = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_insurance_amount'])));
    $insuranceDate = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_insurance_date'])));
    $insuranceMaturity = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_insurance_maturity'])));
    $insurancePremium = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_insurance_premium'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];

    $insuranceNameErr = $insuranceName != '' ? '' : 'Please insert insurance name';
    $policyNumberErr = $policyNumber != '' ? '' : 'Please insert policy number';
    $insuranceAmountErr = $insuranceAmount != '' ? '' : 'Please insert insurance amount';
    $insuranceDateErr = $insuranceDate != '' ? '' : 'Please insert insurance date';
    $insuranceMaturityErr = $insuranceMaturity != '' ? '' : 'Please insert insurance maturity date';
    $insurancePremiumErr = $insurancePremium != '' ? '' : 'Please insert insurance Premium';

    if ($insuranceNameErr != '' || $policyNumberErr != '' || $insuranceAmountErr != '' || $insuranceDateErr != '' || $insuranceMaturityErr != '' || $insurancePremiumErr != '')
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

    if (!$errorFlag && isset($_GET['id'])) {

        $willId = decryption($_GET['id']);

        // Insurance Data Insertion
        $insertedInsurance = "";
        try {

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_insurance_master", "enabled='0'", "willId=" . $willId);

            $insertedInsurance = json_decode(insert_query($con, array('willid', 'name', 'policy_number', 'insured_amount', 'issue_date', 'maturity_date', 'premium', 'createdBy', 'updatedBy'), array($willId, $insuranceName, $policyNumber, $insuranceAmount, $insuranceDate, $insuranceMaturity, $insurancePremium, $_SESSION['uid'], $_SESSION['uid']), "will_insurance_master"));
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting insurance.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedInsurance != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_insurance_beneficiary_master", "enabled='0'", "willId=" . $willId);

            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_insurance_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                echo '<script>swal({title: "Insurance details saved successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_mediclaim.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
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
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="card">
            <div class="card-header bg-white" id="headingHouseProp">
                <h5 class="mb-0">
                    Insurance
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="will_insurance_name" placeholder="Enter insurance name" <?php echo isset($insuranceName) && $insuranceName != '' ? 'value="' . $insuranceName . '"' : ''; ?>>
                            <?php echo $insuranceNameErr != '' ? '<span class="text-danger">' . $insuranceNameErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Policy Number</label>
                            <input type="text" class="form-control" name="will_insurance_policy" placeholder="Enter policy number" <?php echo isset($policyNumber) && $policyNumber != '' ? 'value="' . $policyNumber . '"' : ''; ?>>
                            <?php echo $policyNumberErr != '' ? '<span class="text-danger">' . $policyNumberErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Amount Insured</label>
                            <input type="text" class="form-control" name="will_insurance_amount" placeholder="Enter insurance amount" <?php echo isset($insuranceAmount) && $insuranceAmount != '' ? 'value="' . $insuranceAmount . '"' : ''; ?>>
                            <?php echo $insuranceAmountErr != '' ? '<span class="text-danger">' . $insuranceAmountErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" class="form-control" name="will_insurance_date" <?php echo isset($insuranceDate) && $insuranceDate != '' ? 'value="' . $insuranceDate . '"' : ''; ?>>
                            <?php echo $insuranceDateErr != '' ? '<span class="text-danger">' . $insuranceDateErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Maturity Date</label>
                            <input type="date" class="form-control" name="will_insurance_maturity" <?php echo isset($insuranceMaturity) && $insuranceMaturity != '' ? 'value="' . $insuranceMaturity . '"' : ''; ?>>
                            <?php echo $insuranceMaturityErr != '' ? '<span class="text-danger">' . $insuranceMaturityErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Premium</label>
                            <input type="text" class="form-control" name="will_insurance_premium" <?php echo isset($insurancePremium) && $insurancePremium != '' ? 'value="' . $insurancePremium . '"' : ''; ?>>
                            <?php echo $insurancePremiumErr != '' ? '<span class="text-danger">' . $insurancePremiumErr . '</span>' : ''; ?>
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

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <?php if (isset($_GET['id'])) { ?>
                            <a href="add_mediclaim.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['edit']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
                        <?php } ?>
                        <button type="submit" name="insuranceSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>