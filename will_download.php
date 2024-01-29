<?php
$page = "Will Download";
include_once("./adders/header.php");

if (!isset($_SESSION['logged_in']) || empty($_SESSION['logged_in']))
    header("Location: login.php");

$userDetails = json_decode(select_query($con, "*", "user_master", "enabled='1' AND id=" . $_SESSION['uid'], "", "", ""))[0];

$willId = $willData = $guarantorId = '';

if (isset($_GET['id']))
    $willId = decryption($_GET['id']);

if (isset($_GET['gid']))
    $guarantorId = decryption($_GET['gid']);

if (!empty($willId) && $willId != '') {
    $willData = json_decode(select_query($con, "wm.*,um.first_name,um.last_name", "will_master wm LEFT JOIN user_master um ON wm.createdBy=um.id", "wm.enabled='1' AND wm.id=$willId ", "", "", ""))[0];

    if (!isset($_GET['gid']) && !$willData->approved)
        echo '<script>
                swal({title: "Will not approved yet",type: "warning",button: "Ok"}).then(function() {window.location.href = "index.php";});
             </script>';

    $isAuthorized = false;

    $checkIsAuthorizedGuarantor = json_decode(select_query($con, "*", "will_guarantor_master wgm LEFT JOIN guarantor_master gm ON wgm.guarantorId=gm.id", "wgm.enabled='1' AND gm.enabled='1' AND wgm.willId='$willId' AND gm.email='$userDetails->email_id'", "", "", ""));

    if (count($checkIsAuthorizedGuarantor) > 0)
        $isAuthorized = true;

    $checkIsAuthorizedBeneficiary = json_decode(select_query($con, "wpbm.id", "will_property_beneficiary_master wpbm LEFT JOIN beneficiary_master bm ON wpbm.beneficiaryId=bm.id", "wpbm.enabled='1' AND bm.enabled='1' AND wpbm.willId='$willId' AND bm.email='$userDetails->email_id'", "", "", ""));

    if (count($checkIsAuthorizedBeneficiary) > 0)
        $isAuthorized = true;
}

if ($isAuthorized && (isset($_GET['gid']) || $willData->approved)) {

    $willPropertyDetails = json_decode(select_query($con, "*", "will_property_master", "enabled='1' AND willId='$willId'", "", "", ""));
    $willBankDetails = json_decode(select_query($con, "*", "will_bank_master", "enabled='1' AND willId='$willId'", "", "", ""));
    $willfdDetails = json_decode(select_query($con, "*", "will_fd_master", "enabled='1' AND willId='$willId'", "", "", ""));
    $willshareDetails = json_decode(select_query($con, "*", "will_share_master", "enabled='1' AND willId='$willId'", "", "", ""));
    $willmfDetails = json_decode(select_query($con, "*", "will_mf_master", "enabled='1' AND willId='$willId'", "", "", ""));
    $willinsuranceDetails = json_decode(select_query($con, "*", "will_insurance_master", "enabled='1' AND willId='$willId'", "", "", ""));
    $willmediclaimDetails = json_decode(select_query($con, "*", "will_mediclaim_master", "enabled='1' AND willId='$willId'", "", "", ""));
?>

    <div class="container will-border border-warning rounded">
        <div class="row my-3">
            <h3 class="mx-auto">
                <i class="fas fa-scroll"></i> Will Portal
            </h3>
        </div>

        <div class="row">
            <h4 class="mx-auto">
                Title: <?php echo $willData->title; ?>
            </h4>
        </div>

        <div class="row">
            <p class="text-justify mx-auto">
                <?php echo $willData->description; ?>
            </p>
        </div>

        <div class="container">

            <?php
            if (!empty($willPropertyDetails)) {
                $propertyBeneficiaryDetails = json_decode(select_query($con, "*", "will_property_beneficiary_master wpbm LEFT JOIN beneficiary_master bm ON wpbm.beneficiaryId = bm.id", "wpbm.enabled='1' AND bm.enabled='1' AND wpbm.willId='$willId'", "", "", ""));
                $propertyFileDetails = json_decode(select_query($con, "*", "will_property_file_master", "enabled='1' AND propertyId='" . $willPropertyDetails[0]->id . "' AND willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            House Property
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <b>Property: </b><?php echo $willPropertyDetails[0]->property_details; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Registration No: </b><?php echo $willPropertyDetails[0]->registration_number; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Share Certificate No: </b><?php echo $willPropertyDetails[0]->share_certificate_no; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Property Card: </b><?php echo $willPropertyDetails[0]->property_card; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col">
                                                <p>
                                                    <?php echo $propertyFileDetails[0]->file_type == 0 ? '<b>Share Certificate</b>' : '<b>Property Card</b>'; ?>
                                                </p>
                                                <img src="<?php echo $propertyFileDetails[0]->file_path; ?>" alt="" height="200">
                                            </div>
                                            <div class="col">
                                                <p>
                                                    <?php echo $propertyFileDetails[0]->file_type == 1 ? '<b>Share Certificate</b>' : '<b>Property Card</b>'; ?>
                                                </p>
                                                <img src="<?php echo $propertyFileDetails[0]->file_path; ?>" alt="" height="200">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($propertyBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if (!empty($willBankDetails)) {
                $bankBeneficiaryDetails = json_decode(select_query($con, "*", "will_bank_beneficiary_master wbbm LEFT JOIN beneficiary_master bm ON wbbm.beneficiaryId = bm.id", "wbbm.enabled='1' AND bm.enabled='1' AND wbbm.willId='$willId'", "", "", ""));
                $bankFileDetails = json_decode(select_query($con, "*", "will_bank_file_master", "enabled='1' AND bankId='" . $willBankDetails[0]->id . "' AND willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            Bank Account Details
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group w-100 ">
                                    <li class="list-group-item">
                                        <b>Bank Name: </b><?php echo $willBankDetails[0]->bank_name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Branch: </b><?php echo $willBankDetails[0]->branch; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Account Number: </b><?php echo $willBankDetails[0]->account_number; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Account Type: </b><?php echo $willBankDetails[0]->account_type == 1 ? 'Savings' : 'Current'; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Nominee Name: </b><?php echo $willBankDetails[0]->nominee_name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Signature: </b><?php echo $willBankDetails[0]->signature; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <p class="w-100">
                                            <b>Specimen Signature</b>
                                        </p>
                                        <img src="<?php echo $bankFileDetails[0]->filePath; ?>" alt="" height="200">
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($bankBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if (!empty($willfdDetails)) {
                $fdBeneficiaryDetails = json_decode(select_query($con, "*", "will_fd_beneficiary_master wbbm LEFT JOIN beneficiary_master bm ON wbbm.beneficiaryId = bm.id", "wbbm.enabled='1' AND bm.enabled='1' AND wbbm.willId='$willId'", "", "", ""));
                $fdFileDetails = json_decode(select_query($con, "*", "will_fd_file_master", "enabled='1' AND fdId='" . $willfdDetails[0]->id . "' AND willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            Fixed Deposit Details
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group w-100 ">
                                    <li class="list-group-item">
                                        <b>Bank Name: </b><?php echo $willfdDetails[0]->bank_name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>FDR No: </b><?php echo $willfdDetails[0]->fdr_number; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Deposit Dat: </b><?php echo $willfdDetails[0]->deposit_date; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Due Date: </b><?php echo $willfdDetails[0]->due_date; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Amount: </b><?php echo $willfdDetails[0]->amount; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Signature: </b><?php echo $willfdDetails[0]->signature; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <p class="w-100">
                                            <b>Specimen Signature</b>
                                        </p>
                                        <img src="<?php echo $fdFileDetails[0]->filePath; ?>" alt="" height="200">
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($fdBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if (!empty($willshareDetails)) {
                $shareBeneficiaryDetails = json_decode(select_query($con, "*", "will_share_beneficiary_master wbbm LEFT JOIN beneficiary_master bm ON wbbm.beneficiaryId = bm.id", "wbbm.enabled='1' AND bm.enabled='1' AND wbbm.willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            Share Details
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group w-100 ">
                                    <li class="list-group-item">
                                        <b>Company: </b><?php echo $willshareDetails[0]->company; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>No of Quantity: </b><?php echo $willshareDetails[0]->share_quantity; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Demat No: </b><?php echo $willshareDetails[0]->demat_no; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Nominee Name: </b><?php echo $willshareDetails[0]->nominee_name; ?>
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($shareBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if (!empty($willmfDetails)) {
                $mfBeneficiaryDetails = json_decode(select_query($con, "*", "will_mf_beneficiary_master wbbm LEFT JOIN beneficiary_master bm ON wbbm.beneficiaryId = bm.id", "wbbm.enabled='1' AND bm.enabled='1' AND wbbm.willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            Mutual Funds
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group w-100 ">
                                    <li class="list-group-item">
                                        <b>Folio Number: </b><?php echo $willmfDetails[0]->folio_number; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Fund Name: </b><?php echo $willmfDetails[0]->fund_name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Nominee Name: </b><?php echo $willmfDetails[0]->nominee_name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Invested Amount: </b><?php echo $willmfDetails[0]->invested_amount; ?>
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($mfBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if (!empty($willinsuranceDetails)) {
                $insuranceBeneficiaryDetails = json_decode(select_query($con, "*", "will_insurance_beneficiary_master wbbm LEFT JOIN beneficiary_master bm ON wbbm.beneficiaryId = bm.id", "wbbm.enabled='1' AND bm.enabled='1' AND wbbm.willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            Insurance
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group w-100 ">
                                    <li class="list-group-item">
                                        <b>Name: </b><?php echo $willinsuranceDetails[0]->name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Policy Number: </b><?php echo $willinsuranceDetails[0]->policy_number; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Insured Amount: </b><?php echo $willinsuranceDetails[0]->insured_amount; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Issue Date: </b><?php echo $willinsuranceDetails[0]->issue_date; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Maturity Date: </b><?php echo $willinsuranceDetails[0]->maturity_date; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Premium: </b><?php echo $willinsuranceDetails[0]->premium; ?>
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($insuranceBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if (!empty($willmediclaimDetails)) {
                $mediclaimBeneficiaryDetails = json_decode(select_query($con, "*", "will_mc_beneficiary_master wbbm LEFT JOIN beneficiary_master bm ON wbbm.beneficiaryId = bm.id", "wbbm.enabled='1' AND bm.enabled='1' AND wbbm.willId='$willId'", "", "", ""));
            ?>
                <div class="row my-2">
                    <div class="card adjust-width">
                        <div class="card-header bg-primary text-white">
                            MediClaim
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <ul class="list-group w-100 ">
                                    <li class="list-group-item">
                                        <b>Name: </b><?php echo $willmediclaimDetails[0]->name; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Policy Number: </b><?php echo $willmediclaimDetails[0]->policy_number; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Insured Amount: </b><?php echo $willmediclaimDetails[0]->insured_amount; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Issue Date: </b><?php echo $willmediclaimDetails[0]->issue_date; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Maturity Date: </b><?php echo $willmediclaimDetails[0]->maturity_date; ?>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Premium: </b><?php echo $willmediclaimDetails[0]->premium; ?>
                                    </li>
                                </ul>
                            </div>

                            <div class="row table-responsive">
                                <p class="m-3"><b>Beneficiary Details</b></p>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beneficiary Name</th>
                                            <th scope="col">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($mediclaimBeneficiaryDetails as $beneficiary) {
                                            $bene
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo $beneficiary->name; ?></td>
                                                <td><?php echo $beneficiary->percentage; ?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="row justify-content-center">
            <p class="mb-0">
                <small class="text-secondary">
                    This will has been created by <?php echo $willData->first_name . " " . $willData->last_name; ?> on <?php echo $willData->createdOn; ?>. Last Modified: <?php echo $willData->updatedOn; ?>
                </small>
            </p>
        </div>
    </div>

<?php
} else {
    echo '<script>
            swal({title: "Access Denied",type: "warning",button: "Ok"}).then(function() {window.location.href = "index.php";});
         </script>';
}
include_once("./adders/footer.php");
?>
<?php
if (!isset($_GET['gid'])) {
    echo '<script>
            window.print();
        </script>';
}
