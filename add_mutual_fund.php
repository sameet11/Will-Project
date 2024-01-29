<?php
$page = "Add Will - Mutual Fund";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$mfFolio = $mfFolioErr = $mfFundName = $mfFundNameErr = $mfNominee = $mfNomineeErr = $mfAmount = $mfAmountErr = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $mfDetails = json_decode(select_query($con, "*", "will_mf_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($mfDetails)) {
        $mfFolio = $mfDetails[0]->folio_number;
        $mfFundName = $mfDetails[0]->fund_name;
        $mfNominee = $mfDetails[0]->nominee_name;
        $mfAmount = $mfDetails[0]->invested_amount;

        $mfBeneficiaryDetails = json_decode(select_query($con, "*", "will_mf_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));

        if (!empty($mfBeneficiaryDetails)) {
            for ($i = 0; $i < count($mfBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $mfBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $mfBeneficiaryDetails[$i]->percentage;
            }
        }
    }
}

if (isset($_POST['mfSub'])) {

    $errorFlag = false;

    // MF Details
    $mfFolio = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mf_folio'])));
    $mfFundName = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mf_fund_name'])));
    $mfNominee = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mf_nominee'])));
    $mfAmount = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_mf_amount'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];

    $mfFolioErr = $mfFolio != '' ? '' : 'Please insert folio number';
    $mfFundNameErr = $mfFundName != '' ? '' : 'Please insert fund name';
    $mfNomineeErr = $mfNominee != '' ? '' : 'Please insert nominee';
    $mfAmountErr = $mfAmount != '' ? '' : 'Please insert Amount';

    if ($mfFolioErr != '' || $mfFundNameErr != '' || $mfNomineeErr != '' || $mfAmountErr != '')
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

        // MF Data Insertion
        $insertedMF = "";
        try {

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_mf_master", "enabled='0'", "willId=" . $willId);

            $insertedMF = json_decode(insert_query($con, array('willId', 'folio_number', 'fund_name', 'nominee_name', 'invested_amount', 'createdBy', 'updatedBy'), array($willId, $mfFolio, $mfFundName, $mfNominee, $mfAmount, $_SESSION['uid'], $_SESSION['uid']), "will_mf_master"));
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting mutual fund details.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedMF != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_mf_beneficiary_master", "enabled='0'", "willId=" . $willId);

            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_mf_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                echo '<script>swal({title: "Mutual fund details saved successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_insurance.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
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
                    Mutual Fund
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Folio Number</label>
                            <input type="text" class="form-control" name="will_mf_folio" placeholder="Enter folio number" <?php echo isset($mfFolio) && $mfFolio != '' ? 'value="' . $mfFolio . '"' : ''; ?>>
                            <?php echo $mfFolioErr != '' ? '<span class="text-danger">' . $mfFolioErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Name of fund</label>
                            <input type="text" class="form-control" name="will_mf_fund_name" placeholder="Enter fund name" <?php echo isset($mfFundName) && $mfFundName != '' ? 'value="' . $mfFundName . '"' : ''; ?>>
                            <?php echo $mfFundNameErr != '' ? '<span class="text-danger">' . $mfFundNameErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Nominee</label>
                            <input type="text" class="form-control" name="will_mf_nominee" placeholder="Enter nominee name" <?php echo isset($mfNominee) && $mfNominee != '' ? 'value="' . $mfNominee . '"' : ''; ?>>
                            <?php echo $mfNomineeErr != '' ? '<span class="text-danger">' . $mfNomineeErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Total amount invested</label>
                            <input type="number" class="form-control" name="will_mf_amount" placeholder="Enter total amount" <?php echo isset($mfAmount) && $mfAmount != '' ? 'value="' . $mfAmount . '"' : ''; ?>>
                            <?php echo $mfAmountErr != '' ? '<span class="text-danger">' . $mfAmountErr . '</span>' : ''; ?>
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
                            <a href="add_insurance.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['edit']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
                        <?php } ?>
                        <button type="submit" name="mfSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>