<?php
$page = "Dashboard";
include_once("./adders/header.php");

date_default_timezone_set('Asia/Kolkata');

$documentsUploaded = json_decode((select_query($con, "fileType", "user_file_master", "userId=" . $_SESSION['uid'] . " AND fileType IN ('0','1','2','3') AND enabled='1'", "", "", "")));

if (count($documentsUploaded) != 4)
	header("Location: profile.php");

$userDetails = json_decode(select_query($con, "*", "user_master", "enabled='1' AND id=" . $_SESSION['uid'], "", "", ""))[0];

$guarantersId = json_decode(select_query($con, "id", "guarantor_master", "enabled='1' AND email='$userDetails->email_id'", "", "", ""));

$beneficiariesId = json_decode(select_query($con, "id", "beneficiary_master", "enabled='1' AND email='$userDetails->email_id'", "", "", ""));

$getwill = json_decode(select_query($con, "*", "will_master", "enabled='1' AND approved='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$willsToApprove = $willsAsBeneficiary = [];

if (count($guarantersId) > 0)
	foreach ($guarantersId as $gid) {
		$result = json_decode(select_query($con, "gm.*, wm.title, wm.description, um.first_name, um.last_name", "will_guarantor_master gm LEFT JOIN will_master wm ON gm.willId=wm.id LEFT JOIN user_master um on wm.createdBy=um.id", "gm.enabled='1' AND wm.enabled='1' AND gm.guarantorId=" . $gid->id, "hasApproved='0'", "", ""));

		$willsToApprove = array_merge($willsToApprove, $result);
	}

if (count($beneficiariesId) > 0)
	foreach ($beneficiariesId as $bid) {
		$willsAsBeneficiary = json_decode(select_query($con, "wm.id AS willId, wm.title, wm.description, um.first_name, um.last_name", "`will_master` `wm` 
		LEFT JOIN `will_property_beneficiary_master` `pbm` ON `pbm`.`willId`= `wm`.`id`
		LEFT JOIN `will_share_beneficiary_master` `sbm` ON `sbm`.`willId`= `wm`.`id` 
		LEFT JOIN `will_bank_beneficiary_master` `bbm` ON `bbm`.`willId`= `wm`.`id` 
		LEFT JOIN `will_fd_beneficiary_master` `fbm` ON `fbm`.`willId`= `wm`.`id` 
		LEFT JOIN `will_insurance_beneficiary_master` `ibm` ON `ibm`.`willId`= `wm`.`id` 
		LEFT JOIN `will_mc_beneficiary_master` `mbm` ON `mbm`.`willId`= `wm`.`id`
		LEFT JOIN `will_mf_beneficiary_master` `mfbm` ON `mfbm`.`willId`= `wm`.`id` 
		LEFT JOIN `user_master` `um` ON `um`.`enabled`='1' AND `um`.`id` = `wm`.`createdBy`", "wm.approved='1' AND (pbm.beneficiaryId=" . $bid->id . " OR sbm.beneficiaryId=" . $bid->id . " OR bbm.beneficiaryId=" . $bid->id . " OR fbm.beneficiaryId=" . $bid->id . " OR ibm.beneficiaryId=" . $bid->id . " OR mbm.beneficiaryId=" . $bid->id . " OR mfbm.beneficiaryId=" . $bid->id . ")", "", "", "willId"));
	}

?>
<!--Container Main start-->
<div class="m-3">
	<div class="row">
		<h4 class="text-uppercase"><?php echo $page; ?></h4>
	</div>
</div>

<div class="container-fluid pt-3">
	<nav>
		<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
			<a class="nav-item nav-link active" id="nav-wills-tab" data-toggle="tab" href="#nav-wills" role="tab" aria-controls="nav-wills" aria-selected="true">Your Wills</a>
			<a class="nav-item nav-link " id="nav-beneficiary-tab" data-toggle="tab" href="#nav-beneficiary" role="tab" aria-controls="nav-beneficiary" aria-selected="false">You as Beneficiary</a>
			<a class="nav-item nav-link" id="nav-guarantor-tab" data-toggle="tab" href="#nav-guarantor" role="tab" aria-controls="nav-guarantor" aria-selected="false">You as Guarantor</a>
		</div>
	</nav>
	<div class="tab-content" id="nav-tabContent">

		<!-- YOUR WILLS -->
		<div class="tab-pane fade text-dark show active" id="nav-wills" role="tabpanel" aria-labelledby="nav-wills-tab">
			<table id="willsTable" class="display nowrap w-100 table-responsive-sm ">
				<thead class="thead">
					<th>#</th>
					<th>Title</th>
					<th>Approved/Rejected</th>
					<th>Action</th>
				</thead>

				<tbody>
					<?php
					if (!empty($getwill)) {
						$cnt = 1;
						foreach ($getwill as $will) {
							$getWillGuarantor = json_decode(select_query($con, "*", "will_guarantor_master", "enabled='1' AND willId=" . $will->id, "", "", ""));

							$approvedCount = 0;
							foreach ($getWillGuarantor as $guarantor) {
								if ($guarantor->hasApproved)
									$approvedCount++;
							}
					?>
							<tr>
								<td><?php echo $cnt++; ?></td>
								<td><?php echo $will->title; ?></td>
								<td><?php echo $will->approved ? 'Approved' : 'Pending (' . $approvedCount . ' of ' . count($getWillGuarantor) . ' approved)'; ?></td>
								<td>
									<a href="./add_will.php?id=<?php echo encryption($will->id); ?>">
										<i class="fas fa-edit"></i>
									</a>
									<a href="./will.php?id=<?php echo encryption($will->id); ?>&action=Dt">
										<i class="fas fa-trash-alt"></i>
									</a>
								</td>
							</tr>
					<?php }
					}
					?>
				</tbody>
			</table>
		</div>

		<!-- AS A BENEFICIARY -->
		<div class="tab-pane fade text-dark" id="nav-beneficiary" role="tabpanel" aria-labelledby="nav-beneficiary-tab">
			<table id="beneficiarysTable" class="table table-responsive-sm">
				<thead class="thead">
					<th>#</th>
					<th>Title</th>
					<th>Created By</th>
					<th>Action</th>
				</thead>

				<tbody>
					<?php
					if (!empty($willsAsBeneficiary)) {
						$cnt = 1;
						foreach ($willsAsBeneficiary as $will) { ?>
							<tr>
								<td><?php echo $cnt++; ?></td>
								<td><?php echo $will->title; ?></td>
								<td><?php echo $will->first_name . " " . $will->last_name; ?></td>
								<td class="text-center">
									<a target="_blank" href="./will_download.php?id=<?php echo encryption($will->willId); ?>">
										<h4><i class="fas fa-file-download"></i></h4>
									</a>
								</td>
							</tr>
					<?php }
					}
					?>
				</tbody>
			</table>
		</div>

		<!-- AS A GUARANTORS -->
		<div class="tab-pane fade text-dark" id="nav-guarantor" role="tabpanel" aria-labelledby="nav-guarantor-tab">
			<table id="guarantorsTable" class="table table-responsive-sm">
				<thead class="thead">
					<th>#</th>
					<th>Title</th>
					<th>Created By</th>
					<th>Action</th>
				</thead>

				<tbody>
					<?php
					if (!empty($willsToApprove)) {
						$cnt = 1;
						foreach ($willsToApprove as $will) { ?>
							<tr>
								<td><?php echo $cnt++; ?></td>
								<td><?php echo $will->title; ?></td>
								<td><?php echo $will->first_name . " " . $will->last_name; ?></td>
								<td class="text-center">
									<a target="_blank" href="./will_download.php?id=<?php echo encryption($will->willId); ?>&gid=<?php echo encryption($will->guarantorId); ?>">
										<i class="fas fa-eye"></i>
									</a>
									<?php if ($will->hasApproved == 0) { ?>
										<a href="./will_action.php?id=<?php echo encryption($will->willId); ?>&gid=<?php echo encryption($will->guarantorId); ?>&action=Ae" class="text-success">
											<i class="fas fa-check-circle"></i>
										</a>
										<a href="./will_action.php?id=<?php echo encryption($will->willId); ?>&gid=<?php echo encryption($will->guarantorId); ?>&action=Rt" class="text-danger">
											<i class="fas fa-times-circle"></i>
										</a>
									<?php } else echo $will->hasApproved == 1 ? ' | Approved' : ($will->hasApproved == 2 ? ' | Rejected' : ''); ?>
								</td>
							</tr>
					<?php }
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
include_once "./adders/footer.php";
?>