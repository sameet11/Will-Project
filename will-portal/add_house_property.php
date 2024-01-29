<?php
$page = "Add Will - House Property";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$property = $propertyErr = $propertyRegistrationNo = $propertyRegistrationNoErr =
    $propertyShareCertificateNo = $propertyShareCertificateNoErr = $propertyCardNo = $propertyCardNoErr =
    $propertyShareCertificateErr = $propertyCardErr = $propertyDetails = $propertyFileDetails = "";

$will_beneficiary = ["", "", "", ""];
$will_beneficiaryErr = ["", "", "", ""];

$will_beneficiary_percentage = ["", "", "", ""];
$will_beneficiary_percentageErr = ["", "", "", ""];

if (isset($_GET['id'])) {
    $willId = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $willId, "", "", ""));
    $propertyDetails = json_decode(select_query($con, "*", "will_property_master", "willId=" . $willId . " AND enabled='1'", "", "1", ""));

    if (!empty($propertyDetails)) {
        $property = $propertyDetails[0]->property_details;
        $propertyRegistrationNo = $propertyDetails[0]->registration_number;
        $propertyShareCertificateNo = $propertyDetails[0]->share_certificate_no;
        $propertyCardNo = $propertyDetails[0]->property_card;

        $propertyBeneficiaryDetails = json_decode(select_query($con, "*", "will_property_beneficiary_master", "enabled='1' AND willId=" . $willId, "", "", ""));
        $propertyFileDetails = json_decode(select_query($con, "*", "will_property_file_master", "enabled='1' AND willId=" . $willId, "", "", ""));

        if (!empty($propertyBeneficiaryDetails)) {
            for ($i = 0; $i < count($propertyBeneficiaryDetails); $i++) {
                $will_beneficiary[$i] = $propertyBeneficiaryDetails[$i]->beneficiaryId;
                $will_beneficiary_percentage[$i] = $propertyBeneficiaryDetails[$i]->percentage;
            }
        }
    }
}

if (isset($_POST['propSub'])) {

    $errorFlag = false;

    // House Property Details
    $property = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_house_property'])));
    $propertyRegistrationNo = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_house_property_registration_no'])));
    $propertyShareCertificateNo = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_house_property_share_certi_no'])));
    $propertyCardNo = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_house_property_card'])));

    $beneficiaries = isset($_POST['will_beneficiary']) ? $_POST['will_beneficiary'] : [];
    $beneficiary_percentages = isset($_POST['will_beneficiary_percentage']) ? $_POST['will_beneficiary_percentage'] : [];

    $propertyErr = $property != '' ? '' : 'Please insert property details';
    $propertyRegistrationNoErr = $propertyRegistrationNo != '' ? '' : 'Please insert property registration no';
    $propertyShareCertificateNoErr = $propertyShareCertificateNo != '' ? '' : 'Please insert property share certificate no';
    $propertyCardNoErr = $propertyCardNo != '' ? '' : 'Please insert property card no';

    if ($propertyErr != '' || $propertyRegistrationNoErr != '' || $propertyShareCertificateNoErr != '' || $propertyCardNoErr != '')
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

        // House Property Data Insertion
        $insertedHouseProp = "";
        try {
            $oldProp = "";
            if (isset($_GET['edit']) && $_GET['edit']) {
                $oldProp = json_decode(select_query($con, "id", "will_property_master", "willId=" . $willId, "", "", ""));
                update_query($con, "will_property_master", "enabled='0'", "willId=" . $willId);
            }

            $insertedHouseProp = json_decode(insert_query($con, array('willId', 'property_details', 'registration_number', 'share_certificate_no', 'property_card', 'createdBy', 'updatedBy'), array($willId, $property, $propertyRegistrationNo, $propertyShareCertificateNo, $propertyCardNo, $_SESSION['uid'], $_SESSION['uid']), "will_property_master"));

            if (isset($_GET['edit']) && $_GET['edit'])
                foreach ($oldProp as $prop) {
                    update_query($con, "will_property_file_master", "propertyId=$insertedHouseProp", "propertyId=$prop->id");
                }
        } catch (Exception $e) {
            $errorFlag = true;
            echo '<script>swal({title: "Error while inserting property.<br>' . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
        }

        if ($willId != '' && $insertedHouseProp != '' && $errorFlag == '') {
            $insertedBeneficiary = [];

            if (isset($_GET['edit']) && $_GET['edit'])
                update_query($con, "will_property_beneficiary_master", "enabled='0'", "willId=$willId");

            for ($i = 0; $i < count($will_beneficiary); $i++) {
                try {
                    if (!empty($will_beneficiary[$i])) {
                        $insertedBeneficiary[$i] = json_decode(insert_query($con, array('willId', 'beneficiaryId', 'percentage', 'createdBy', 'updatedBy'), array($willId, $will_beneficiary[$i], $will_beneficiary_percentage[$i], $_SESSION['uid'], $_SESSION['uid']), "will_property_beneficiary_master"));
                    }
                } catch (Exception $e) {
                    $errorFlag = true;
                    echo '<script>swal({title: "Error while inserting beneficiary' . ($i + 1) . "<br>" . $e->getMessage() . '",type: "warning",button: "Ok"});</script>';
                }
            }

            if (!$errorFlag) {
                if (!empty($_FILES) && ($_FILES['will_house_property_share_certi_file']['name'] != '' || $_FILES['will_house_property_card_file']['name'] != '')) {

                    update_query($con, "will_property_file_master", "enabled='0'", "willId=" . $willId);

                    if (isset($_FILES['will_house_property_share_certi_file']['name']))
                        $propertyShareCertificate = $_FILES['will_house_property_share_certi_file'];
                    if (isset($_FILES['will_house_property_card_file']['name']))
                        $propertyCard = $_FILES['will_house_property_card_file'];

                    if ($propertyShareCertificate != "" || $propertyCard != "") {

                        $target_dir = "assets/images/uploads/properties/";
                        foreach ($_FILES as $fileKey => $fileValue) {

                            $fileType = null;
                            if ($fileKey == 'will_house_property_share_certi_file')
                                $fileType = 0;
                            if ($fileKey == 'will_house_property_card_file')
                                $fileType = 1;

                            $target_file = $target_dir . $_SESSION['uid'] . '_' . $willId . '_' . $fileType . '_' . time() . "." . pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
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
                                if ($fileKey == 'will_house_property_share_certi_file')
                                    $propertyShareCertificateErr = $errMsg;
                                if ($fileKey == 'will_house_property_card_file')
                                    $propertyCardErr = $errMsg;

                                // if everything is ok, try to upload file
                            } else {
                                if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $target_file)) {
                                    $disabledFiles = json_decode(update_query($con, "will_property_file_master", "enabled='0'", "file_type='" . $fileType . "' AND willId=" . $willId . " AND propertyId=$insertedHouseProp"));

                                    $insertedFile = json_decode(insert_query($con, array('willId', 'propertyId', 'file_type', 'file_path', 'createdBy', 'updatedBy'), array($willId, $insertedHouseProp, $fileType, $target_file, $_SESSION['uid'], $_SESSION['uid']), "will_property_file_master"));

                                    echo '<script>swal({title: "Property saved with files successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_bank.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
                                } else {
                                    echo '<script>swal({title: "Something went wrong while uploading images",type: "warning",button: "Ok"});</script>';
                                }
                            }
                        }
                    } else {
                        echo '<script>swal({title: "Something went wrong while fetching file data",type: "warning",button: "Ok"});</script>';
                    }
                } else {
                    echo '<script>swal({title: "Property saved without any file successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_bank.php?id=' . $_GET['id'] . '&edit=' . isset($_GET['edit']) . '";});</script>';
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
                    House Property
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Property Details</label>
                            <input type="text" class="form-control" name="will_house_property" placeholder="Enter property details" <?php echo isset($property) && $property != '' ? 'value="' . $property . '"' : ''; ?>>
                            <?php echo $propertyErr != '' ? '<span class="text-danger">' . $propertyErr . '</span>' : ''; ?>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Registration Number</label>
                            <input type="text" class="form-control" name="will_house_property_registration_no" placeholder="Enter registration number" <?php echo isset($propertyRegistrationNo) && $propertyRegistrationNo != '' ? 'value="' . $propertyRegistrationNo . '"' : ''; ?>>
                            <?php echo $propertyRegistrationNoErr != '' ? '<span class="text-danger">' . $propertyRegistrationNoErr . '</span>' : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="row my-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Share Certificate Number</label>
                            <input type="text" class="form-control" name="will_house_property_share_certi_no" placeholder="Enter share certificate number" <?php echo isset($propertyShareCertificateNo) && $propertyShareCertificateNo != '' ? 'value="' . $propertyShareCertificateNo . '"' : ''; ?>>
                            <?php echo $propertyShareCertificateNoErr != '' ? '<span class="text-danger">' . $propertyShareCertificateNoErr . '</span>' : ''; ?>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label>Upload Share Certificate No</label>
                            <input type="file" class="form-control-file" name="will_house_property_share_certi_file">
                            <?php echo $propertyShareCertificateErr != '' ? '<span class="text-danger">' . $propertyShareCertificateErr . '</span>' : ''; ?>
                        </div>
                    </div>

                    <?php if (!empty($propertyFileDetails) && !empty($propertyFileDetails[0]) && $propertyFileDetails[0]->file_type == 0) { ?>
                        <div class="col-sm-12 col-md-3">
                            <img class="img-fluid border rounded border-dark" src="<?php echo $propertyFileDetails[0]->file_path ?>" alt="Property Share Certificate | Will Portal" width="200" height="100">
                        </div>
                    <?php } ?>
                </div>

                <div class="row my-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Property Card</label>
                            <input type="text" class="form-control" name="will_house_property_card" placeholder="Enter property card number" <?php echo isset($propertyCardNo) && $propertyCardNo != '' ? 'value="' . $propertyCardNo . '"' : ''; ?>>
                            <?php echo $propertyCardNoErr != '' ? '<span class="text-danger">' . $propertyCardNoErr . '</span>' : ''; ?>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label>Upload Property Card</label>
                            <input type="file" class="form-control-file" name="will_house_property_card_file">
                            <?php echo $propertyCardErr != '' ? '<span class="text-danger">' . $propertyCardErr . '</span>' : ''; ?>
                        </div>
                    </div>
                    <?php if (!empty($propertyFileDetails) && !empty($propertyFileDetails[1]) && $propertyFileDetails[1]->file_type == 1) { ?>
                        <div class="col-sm-12 col-md-3">
                            <img class="img-fluid border rounded border-dark" src="<?php echo $propertyFileDetails[1]->file_path ?>" alt="Property Share Certificate | Will Portal" width="200" height="100">
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
                            <a href="add_bank.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['edit']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
                        <?php } ?>
                        <button type="submit" name="propSub" class="btn btn-outline-primary btn-sm">Submit Will</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>