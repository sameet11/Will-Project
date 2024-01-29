<?php
$page = "Add Will - Fixed Deposit";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$fdBankName = $fdBankNameErr = $fdr = $fdrErr = $fdDepositDate = $fdDepositDateErr = $fdrDueDate = $fdrDueDateErr = $fdAmount = $fdAmountErr = $fdSignature = $fdSignatureErr = $fdSignatureFileErr = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $fdDetails = json_decode(select_query($con, "*", "will_fd_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($fdDetails)) {
        $fdBankName = $fdDetails[0]->bank_name;
        $fdr = $fdDetails[0]->fdr_number;
        $fdDepositDate = $fdDetails[0]->deposit_date;
        $fdrDueDate = $fdDetails[0]->due_date;
        $fdAmount = $fdDetails[0]->amount;
        $fdSignature = $fdDetails[0]->signature;

        $fdBeneficiaryDetails = json_decode(select_query($con, "*", "will_fd_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));
        $fdFileDetails = json_decode(select_query($con, "*", "will_fd_file_master", "enabled='1' AND willId=" . $willId, "", "", ""));

        if (!empty($fdBeneficiaryDetails)) {
            for ($i = 0; $i < count($fdBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $fdBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $fdBeneficiaryDetails[$i]->percentage;
            }
        }
    }
}

if (isset($_POST['fdSub'])) {

    $errorFlag = false;

    // FD Details
    $fdBankName = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_fd_bank_name'])));
    $fdr = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_fdr'])));
    $fdDepositDate = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_fd_dod'])));
    $fdrDueDate = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_fd_due_date'])));
    $fdAmount = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_fd_amount'])));
    $fdSignature = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_fd_signature'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];

    $fdBankNameErr = $fdBankName != '' ? '' : 'Please insert bank name';
    $fdrErr = $fdr != '' ? '' : 'Please insert FDR';
    $fdrDueDateErr = $fdrDueDate != '' ? '' : 'Please insert FD due date';
    $fdDepositDateErr = $fdDepositDate != '' ? '' : 'Please insert FD deposit date';
    $fdAmountErr = $fdAmount != '' ? '' : 'Please insert FD amount';
    $fdSignatureErr = $fdSignature != '' ? '' : 'Please insert FD signature';

    if ($fdBankNameErr != '' || $fdrErr != '' || $fdrDueDateErr != '' || $fdDepositDateErr != '' || $fdAmountErr != '' || $fdSignatureErr != '')
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

        // FD Details Insertion
        $insertedFD = "";
        try {

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_fd_master", "enabled='0'", "willId=" . $willId);

            $insertedFD = json_decode(insert_query($con, array('willId', 'bank_name', 'fdr_number', 'deposit_date', 'due_date', 'amount', 'signature', 'createdBy', 'updatedBy'), array($willId, $fdBankName, $fdr, $fdDepositDate, $fdrDueDate, $fdAmount, $fdSignature, $_SESSION['uid'], $_SESSION['uid']), "will_fd_master"));
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting FD.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedFD != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_fd_beneficiary_master", "enabled='0'", "willId=" . $willId);
                
            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_fd_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                if (!empty($_FILES) && isset($_FILES['will_fd_signature_file']['name']) && $_FILES['will_fd_signature_file']['name'] != '') {
                    $signature = $_FILES['will_fd_signature_file'];

                    if ($signature != "") {

                        update_query($con, "will_fd_file_master", "enabled='0'", "willId=" . $willId);

                        $target_dir = "assets/images/uploads/fd-signatures/";
                        foreach ($_FILES as $fileKey => $fileValue) {

                            $target_file = $target_dir . $_SESSION['uid'] . '_' . $willId . '_' . $insertedFD . '_' . time() . "." . pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
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
                                if ($fileKey == 'will_fd_signature_file')
                                    $signatureErr = $errMsg;

                                // if everything is ok, try to upload file
                            } else {
                                if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $target_file)) {
                                    $disabledFiles = json_decode(update_query($con, "will_fd_file_master", "enabled='0'", "willId=" . $willId . " AND fdId=$insertedFD"));

                                    $insertedFile = json_decode(insert_query($con, array('willId', 'fdId', 'filePath', 'createdBy', 'updatedBy'), array($willId, $insertedFD, $target_file, $_SESSION['uid'], $_SESSION['uid']), "will_fd_file_master"));

                                    echo '<script>swal({title: "FD details saved with files successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_shares.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
                                } else {
                                    echo '<script>swal({title: "Something went wrong while uploading images",type: "warning",button: "Ok"});</script>';
                                }
                            }
                        }
                    } else {
                        echo '<script>swal({title: "Something went wrong while fetching file data",type: "warning",button: "Ok"});</script>';
                    }
                } else {
                    echo '<script>swal({title: "FD details saved without files successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_shares.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
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
                    Fixed Deposit
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="will_fd_bank_name" placeholder="Enter bank name" <?php echo isset($fdBankName) && $fdBankName != '' ? 'value="' . $fdBankName . '"' : ''; ?>>
                            <?php echo $fdBankNameErr != '' ? '<span class="text-danger">' . $fdBankNameErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>FDR Number</label>
                            <input type="text" class="form-control" name="will_fdr" placeholder="Enter FDR number" <?php echo isset($fdr) && $fdr != '' ? 'value="' . $fdr . '"' : ''; ?>>
                            <?php echo $fdrErr != '' ? '<span class="text-danger">' . $fdrErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Date of Deposit</label>
                            <input type="date" class="form-control" name="will_fd_dod" placeholder="Enter date of deposit" <?php echo isset($fdDepositDate) && $fdDepositDate != '' ? 'value="' . $fdDepositDate . '"' : ''; ?>>
                            <?php echo $fdDepositDateErr != '' ? '<span class="text-danger">' . $fdDepositDateErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="date" class="form-control" name="will_fd_due_date" placeholder="Enter due date" <?php echo isset($fdrDueDate) && $fdrDueDate != '' ? 'value="' . $fdrDueDate . '"' : ''; ?>>
                            <?php echo $fdrDueDateErr != '' ? '<span class="text-danger">' . $fdrDueDateErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <label>Amount</label>
                        <div class="form-group input-group">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">&#x20B9;</span>
                            </div>
                            <input type="text" class="form-control" name="will_fd_amount" placeholder="Enter amount" <?php echo isset($fdAmount) && $fdAmount != '' ? 'value="' . $fdAmount . '"' : ''; ?>>
                            <?php echo $fdAmountErr != '' ? '<span class="text-danger">' . $fdAmountErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Specimen signature</label>
                            <input type="text" class="form-control" name="will_fd_signature" placeholder="Enter signature" <?php echo isset($fdSignature) && $fdSignature != '' ? 'value="' . $fdSignature . '"' : ''; ?>>
                            <?php echo $fdSignatureErr != '' ? '<span class="text-danger">' . $fdSignatureErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label>Upload Signature</label>
                            <input type="file" class="form-control-file" name="will_fd_signature_file">
                            <?php echo $fdSignatureFileErr != '' ? '<span class="text-danger">' . $fdSignatureFileErr . '</span>' : ''; ?>
                        </div>
                    </div>
                    <?php if (!empty($fdFileDetails) && !empty($fdFileDetails[0])) { ?>
                        <div class="col-sm-12 col-md-3">
                            <img class="img-fluid border rounded border-dark" src="<?php echo $fdFileDetails[0]->filePath ?>" alt="User signature | Will Portal" width="200" height="100">
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
                            <a href="add_shares.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['edit']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
                        <?php } ?>
                        <button type="submit" name="fdSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>