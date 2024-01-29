<?php
$page = "Add Will - Shares";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$shareCompany = $shareCompanyErr = $shareCount = $shareCountErr = $dematAccount = $dematAccountErr = $shareNominee = $shareNomineeErr = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $shareDetails = json_decode(select_query($con, "*", "will_share_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($shareDetails)) {
        $shareCompany = $shareDetails[0]->company;
        $shareCount = $shareDetails[0]->share_quantity;
        $dematAccount = $shareDetails[0]->demat_no;
        $shareNominee = $shareDetails[0]->nominee_name;

        $shareBeneficiaryDetails = json_decode(select_query($con, "*", "will_share_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));

        if (!empty($shareBeneficiaryDetails)) {
            for ($i = 0; $i < count($shareBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $shareBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $shareBeneficiaryDetails[$i]->percentage;
            }
        }
    }
}

if (isset($_POST['shareSub'])) {

    $errorFlag = false;

    // Shares Details
    $shareCompany = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_share_company'])));
    $shareCount = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_share_count'])));
    $dematAccount = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_demat_no'])));
    $shareNominee = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_share_nominee'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];

    $shareCompanyErr = $shareCompany != '' ? '' : 'Please insert company';
    $shareCountErr = $shareCount != '' ? '' : 'Please insert count';
    $dematAccountErr = $dematAccount != '' ? '' : 'Please insert demat account number';
    $shareNomineeErr = $shareNominee != '' ? '' : 'Please insert nominee name';

    if ($shareCompanyErr != '' || $shareCountErr != '' || $dematAccountErr != '' || $shareNomineeErr != '')
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

        // Share Data Insertion
        $insertedShare = "";
        try {

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_share_master", "enabled='0'", "willId=" . $willId);

            $insertedShare = json_decode(insert_query($con, array('willId', 'company', 'share_quantity', 'demat_no', 'nominee_name', 'createdBy', 'updatedBy'), array($willId, $shareCompany, $shareCount, $dematAccount, $shareNominee, $_SESSION['uid'], $_SESSION['uid']), "will_share_master"));
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting shares data.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedShare != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_share_beneficiary_master", "enabled='0'", "willId=" . $willId);

            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_share_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                echo '<script>swal({title: "Shares details saved successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_mutual_fund.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
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
                    Shares
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" class="form-control" name="will_share_company" placeholder="Enter company" <?php echo isset($shareCompany) && $shareCompany != '' ? 'value="' . $shareCompany . '"' : ''; ?>>
                            <?php echo $shareCompanyErr != '' ? '<span class="text-danger">' . $shareCompanyErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>No of Shares</label>
                            <input type="text" class="form-control" name="will_share_count" placeholder="Enter no. of shares" <?php echo isset($shareCount) && $shareCount != '' ? 'value="' . $shareCount . '"' : ''; ?>>
                            <?php echo $shareCountErr != '' ? '<span class="text-danger">' . $shareCountErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Demat A/c number</label>
                            <input type="text" class="form-control" name="will_demat_no" placeholder="Enter demat account number" <?php echo isset($dematAccount) && $dematAccount != '' ? 'value="' . $dematAccount . '"' : ''; ?>>
                            <?php echo $dematAccountErr != '' ? '<span class="text-danger">' . $dematAccountErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Nominee</label>
                            <input type="text" class="form-control" name="will_share_nominee" placeholder="Enter nominee name" <?php echo isset($shareNominee) && $shareNominee != '' ? 'value="' . $shareNominee . '"' : ''; ?>>
                            <?php echo $shareNomineeErr != '' ? '<span class="text-danger">' . $shareNomineeErr . '</span>' : ''; ?>
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
                            <a href="add_mutual_fund.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['edit']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
                        <?php } ?>
                        <button type="submit" name="shareSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>