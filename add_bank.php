<?php
$page = "Add Will - Bank Account";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$bankName = $bankNameErr = $branchName = $branchNameErr = $accountNo = $accountNoErr = $accountType = $accountTypeErr = $nominee = $nomineeErr = $signature = $signatureErr = $bankSignatureErr = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $bankDetails = json_decode(select_query($con, "*", "will_bank_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($bankDetails)) {
        $bankName = $bankDetails[0]->bank_name;
        $branchName = $bankDetails[0]->branch;
        $accountNo = $bankDetails[0]->account_number;
        $accountType = $bankDetails[0]->account_type;
        $nominee = $bankDetails[0]->nominee_name;
        $signature = $bankDetails[0]->signature;

        $bankBeneficiaryDetails = json_decode(select_query($con, "*", "will_bank_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));
        $bankFileDetails = json_decode(select_query($con, "*", "will_bank_file_master", "enabled='1' AND willId=" . $willId, "", "", ""));

        if (!empty($bankBeneficiaryDetails)) {
            for ($i = 0; $i < count($bankBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $bankBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $bankBeneficiaryDetails[$i]->percentage;
            }
        }
    }
}

if (isset($_POST['bankSub'])) {

    $errorFlag = false;

    // Bank Account Details
    $bankName = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_bank_name'])));
    $branchName = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_branch_name'])));
    $accountNo = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_account_no'])));
    $accountType = isset($_POST['will_account_type']) ? mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_account_type']))) : '';
    $nominee = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_account_nominee'])));
    $signature = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_signature'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];

    $bankNameErr = $bankName != '' ? '' : 'Please insert bank name';
    $branchNameErr = $branchName != '' ? '' : 'Please insert branch name';
    $accountNoErr = $accountNo != '' ? '' : 'Please insert account number';
    $accountTypeErr = $accountType != '' ? '' : 'Please insert account type';
    $nomineeErr = $nominee != '' ? '' : 'Please insert nominee';
    $signatureErr = $signature != '' ? '' : 'Please insert signature';

    if ($branchNameErr != '' || $branchNameErr != '' || $accountNoErr != '' || $accountTypeErr != '' || $nomineeErr != '' || $signatureErr != '')
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

        // TODO: Bank Account Data Insertion
        $insertedBankData = "";
        try {
            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_bank_master", "enabled='0'", "willId=" . $willId);

            $insertedBankData = json_decode(insert_query($con, array('willId', 'bank_name', 'branch', 'account_number', 'account_type', 'nominee_name', 'signature', 'createdBy', 'updatedBy'), array($willId, $bankName, $branchName, $accountNo, $accountType, $nominee, $signature, $_SESSION['uid'], $_SESSION['uid']), "will_bank_master"));
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting bank.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedBankData != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_bank_beneficiary_master", "enabled='0'", "willId=$willId");

            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_bank_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                if (!empty($_FILES) && $_FILES['will_signature_file']['name'] != '') {

                    update_query($con, "will_bank_file_master", "enabled='0'", "willId=" . $willId);

                    $bankSignature = $_FILES['will_signature_file'];

                    if ($bankSignature != "") {

                        $target_dir = "assets/images/uploads/bank-signatures/";
                        foreach ($_FILES as $fileKey => $fileValue) {

                            $target_file = $target_dir . $_SESSION['uid'] . '_' . $willId . '_' . $insertedBankData . '_' . time() . "." . pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
                            $uploadOk = 1;
                            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                            $errMsg = "";
                            // Check if image file is a actual image or fake image
                            $check = getimagesize($_FILES[$fileKey]["tmp_name"]);
                            if ($check !== false) {
                                $uploadOk = 1;
                            } else {
                                $errMsg = "Please select an image";
                                $uploadOk = 0;
                            }

                            // Check file size
                            if ($_FILES[$fileKey]["size"] > 500000) {
                                $errMsg = "Sorry, your file is too large. Max 5MB is allowed";
                                $uploadOk = 0;
                            }

                            // Allow certain file formats
                            $allowedType = array("jpg", "png", "jpeg");

                            if (!in_array($imageFileType, $allowedType)) {
                                $errMsg = "Sorry, only JPG, JPEG, PNG files are allowed.";
                                $uploadOk = 0;
                            }

                            // Check if $uploadOk is set to 0 by an error

                            if ($uploadOk == 0) {
                                if ($fileKey == 'will_signature_file')
                                    $bankSignatureErr = $errMsg;

                                // if everything is ok, try to upload file
                            } else {
                                if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $target_file)) {
                                    $disabledFiles = json_decode(update_query($con, "will_bank_file_master", "enabled='0'", "willId=" . $willId . " AND bankId=$insertedBankData"));

                                    $insertedFile = json_decode(insert_query($con, array('willId', 'bankId', 'filePath', 'createdBy', 'updatedBy'), array($willId, $insertedBankData, $target_file, $_SESSION['uid'], $_SESSION['uid']), "will_bank_file_master"));

                                    echo '<script>swal({title: "Bank account details saved with files successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_fixed_deposit.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
                                } else {
                                    echo '<script>swal({title: "Something went wrong while uploading images",type: "warning",button: "Ok"});</script>';
                                }
                            }
                        }
                    } else {
                        echo '<script>swal({title: "Something went wrong while fetching file data",type: "warning",button: "Ok"});</script>';
                    }
                } else {
                    echo '<script>swal({title: "Bank account details saved without files successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_fixed_deposit.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
                }
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
                    Bank Account
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="will_bank_name" placeholder="Enter bank name" <?php echo isset($bankName) && $bankName != '' ? 'value="' . $bankName . '"' : ''; ?>>
                            <?php echo $bankNameErr != '' ? '<span class="text-danger">' . $bankNameErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" class="form-control" name="will_branch_name" placeholder="Enter branch name" <?php echo isset($branchName) && $branchName != '' ? 'value="' . $branchName . '"' : ''; ?>>
                            <?php echo $branchNameErr != '' ? '<span class="text-danger">' . $branchNameErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="will_account_no" placeholder="Enter account number" <?php echo isset($accountNo) && $accountNo != '' ? 'value="' . $accountNo . '"' : ''; ?>>
                            <?php echo $accountNoErr != '' ? '<span class="text-danger">' . $accountNoErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Type of Account</label>
                            <select class="form-control" name="will_account_type">
                                <option selected disabled>Select account type</option>
                                <option value="1" <?php echo isset($accountType) && $accountType != '' && $accountType == '1' ? 'selected' : ''; ?>>Saving</option>
                                <option value="2" <?php echo isset($accountType) && $accountType != '' && $accountType == '2' ? 'selected' : ''; ?>>Current</option>
                            </select>
                            <?php echo $accountTypeErr != '' ? '<span class="text-danger">' . $accountTypeErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Nominee Name</label>
                            <input type="text" class="form-control" name="will_account_nominee" placeholder="Enter nominee name" <?php echo isset($nominee) && $nominee != '' ? 'value="' . $nominee . '"' : ''; ?>>
                            <?php echo $nomineeErr != '' ? '<span class="text-danger">' . $nomineeErr . '</span>' : ''; ?>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Specimen signature</label>
                            <input type="text" class="form-control" name="will_signature" placeholder="Enter signature" <?php echo isset($signature) && $signature != '' ? 'value="' . $signature . '"' : ''; ?>>
                            <?php echo $signatureErr != '' ? '<span class="text-danger">' . $signatureErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label>Upload Signature</label>
                            <input type="file" class="form-control-file" name="will_signature_file">
                        </div>
                    </div>
                    <?php if (!empty($bankFileDetails) && !empty($bankFileDetails[0])) { ?>
                        <div class="col-sm-12 col-md-3">
                            <img class="img-fluid border rounded border-dark" src="<?php echo $bankFileDetails[0]->filePath ?>" alt="User signature | Will Portal" width="200" height="100">
                        </div>
                    <?php } ?>
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
                            <a href="add_fixed_deposit.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['edit']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
                        <?php } ?>
                        <button type="submit" name="bankSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>