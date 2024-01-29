<?php
$page = "Profile";
include_once("./adders/header.php");

$first_name = $first_nameErr = $last_name = $last_nameErr =
	$email = $emailErr = $phone = $phoneErr = $address = $addressErr =
	$aadharFront = $aadharFrontErr = $aadharBackErr = $aadharBack = $pan = $panErr = $drivingLicense = $drivingLicenseErr = "";

$userDetails = json_decode(select_query($con, "*", "user_master", "enabled='1' AND id=" . $_SESSION['uid'], "", "", ""));
$userFileDetails = json_decode(select_query($con, "*", "user_file_master", "enabled='1' AND userId=" . $_SESSION['uid'], "", "", ""));

if (!empty($userDetails) && count($userDetails) > 0) {
	$first_name = $userDetails[0]->first_name;
	$last_name = $userDetails[0]->last_name;
	$email = $userDetails[0]->email_id;
	$phone = $userDetails[0]->phone_number;
	$address = $userDetails[0]->address;
} else {
	echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"}).then(function() {window.location.href = "index.php";});</script>';
}

if (!empty($userFileDetails))
	foreach ($userFileDetails as $file) {
		if ($file->fileType == 0)
			$aadharFront = $file->filePath;
		if ($file->fileType == 1)
			$aadharBack = $file->filePath;
		if ($file->fileType == 2)
			$pan = $file->filePath;
		if ($file->fileType == 3)
			$drivingLicense = $file->filePath;
	}

if (isset($_POST['subForm'])) {

	$first_name = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['first_name'])));
	$last_name = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['last_name'])));
	$phone = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['phone'])));
	$email = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['email'])));
	$address = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['address'])));

	if (!preg_match('/^[a-zA-Z]+[a-zA-Z ]+$/', $first_name))
		$first_nameErr = "Only alphabets and spaces are allowed";

	if (!preg_match('/^[a-zA-Z]+[a-zA-Z ]+$/', $last_name))
		$last_nameErr = "Only alphabets and spaces are allowed";

	if (!preg_match('/^[0-9]{10}+$/', $phone))
		$phoneErr = "Only numbers of 10 digits are allowed";

	if (!preg_match('/^(\w*\s*[\-\,\/\.\(\)\&]*)+$/i', $address))
		$addressErr = "Enter valid address";

	if ($first_name == '')
		$first_nameErr = "First name is required";

	if ($last_name == '')
		$last_nameErr = "Last name is required";

	if ($phone == '')
		$phoneErr = "Phone number is required";

	if ($email == '')
		$emailErr = "Email address is required";

	if ($address == '')
		$addressErr = "Address is required";

	if ($first_nameErr == '' && $last_nameErr == '' && $phoneErr == '' && $emailErr == '' && $addressErr == '') {

		$result = json_decode(update_query($con, "user_master", "first_name='$first_name', last_name='$last_name', phone_number='$phone', email_id='$email', address='$address'", "id=" . $_SESSION['uid']));

		if ($result) {
			$userDetails = json_decode(select_query($con, "*", "user_master", "enabled='1' AND id=" . $_SESSION['uid'], "", "", ""));

			if(!empty($_FILES)) {
				echo '<script>swal({title: "Profile updated. Please wait for files to upload",type: "success",button: "Ok"});</script>';
			} else {
				echo '<script>swal({title: "Profile updated successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "profile.php";});</script>';
			}
		}

		if (isset($_FILES['aadhar_front']['name']))
			$aadharFront = $_FILES['aadhar_front'];
		if (isset($_FILES['aadhar_back']['name']))
			$aadharBack = $_FILES['aadhar_back'];
		if (isset($_FILES['pan_card']['name']))
			$pan = $_FILES['pan_card'];
		if (isset($_FILES['driving_license']['name']))
			$drivingLicense = $_FILES['driving_license'];

		if ($aadharFront != "" || $aadharBack != "" || $pan != "" || $drivingLicense != "") {

			$target_dir = "assets/images/uploads/";
			foreach ($_FILES as $fileKey => $fileValue) {

				$fileType = null;
				if ($fileKey == 'aadhar_front')
					$fileType = 0;
				if ($fileKey == 'aadhar_back')
					$fileType = 1;
				if ($fileKey == 'pan_card')
					$fileType = 2;
				if ($fileKey == 'driving_license')
					$fileType = 3;

				$target_file = $target_dir . $_SESSION['uid'] . $fileType . time() . "." . pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
				$uploadOk = 1;
				$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

				$errMsg = "";
				// Check if image file is a actual image or fake image
				if (isset($_POST["submit"])) {
					$check = getimagesize($_FILES[$fileKey]["tmp_name"]);
					if ($check !== false) {
						$uploadOk = 1;
					} else {
						$errMsg = "Please select an image";
						$uploadOk = 0;
					}
				}

				// Check file size
				if ($_FILES[$fileKey]["size"] > 500000) {
					$errMsg = "Sorry, your file is too large.";
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
					if ($fileKey == 'aadhar_front')
						$aadharFrontErr = $errMsg;
					if ($fileKey == 'aadhar_back')
						$aadharBackErr = $errMsg;
					if ($fileKey == 'pan_card')
						$panErr = $errMsg;
					if ($fileKey == 'driving_license')
						$drivingLicenseErr = $errMsg;

					// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $target_file)) {
						$disabledFiles = json_decode(update_query($con, "user_file_master", "enabled='0'", "fileType='" . $fileType . "' AND userId=" . $_SESSION['uid']));
						$insertedFile = json_decode(insert_query($con, array('userId', 'fileType', 'filePath', 'createdBy', 'updatedBy'), array($_SESSION['uid'], $fileType, $target_file, $_SESSION['uid'], $_SESSION['uid']), "user_file_master"));

						$userFileDetails = json_decode(select_query($con, "*", "user_file_master", "enabled='1' AND userId=" . $_SESSION['uid'], "", "", ""));

						echo '<script>swal({title: "Profile updated successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "profile.php";});</script>';
					} else {
						echo '<script>swal({title: "Something went wrong while uploading images",type: "warning",button: "Ok"});</script>';
					}
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

<div class="container pt-3">
	<div class="card">
		<div class="card-body">
			<form class="form-group" action="" method="post" enctype="multipart/form-data">
				<div class="row justify-content-center">
					<div class="col-md-4">
						<div class="form-group">
							<label>First Name</label>
							<input type="text" class="form-control" placeholder="Enter first name" name="first_name" <?php echo isset($first_name) && $first_name != '' ? 'value="' . $first_name . '"' : ''; ?>>
							<?php echo $first_nameErr != '' ? '<span class="text-danger">' . $first_nameErr . '</span>' : ''; ?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label>Last Name</label>
							<input type="text" class="form-control" placeholder="Enter last name" name="last_name" <?php echo isset($last_name) && $last_name != '' ? 'value="' . $last_name . '"' : ''; ?>>
							<?php echo $last_nameErr != '' ? '<span class="text-danger">' . $last_nameErr . '</span>' : ''; ?>
						</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-md-4">
						<div class="form-group">
							<label>Email</label>
							<input type="text" class="form-control" placeholder="Enter email" name="email" <?php echo isset($email) && $email != '' ? 'value="' . $email . '"' : ''; ?>>
							<?php echo $emailErr != '' ? '<span class="text-danger">' . $emailErr . '</span>' : ''; ?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label>Phone Number</label>
							<input type="text" class="form-control" placeholder="Enter phone number" name="phone" <?php echo isset($phone) && $phone != '' ? 'value="' . $phone . '"' : ''; ?>>
							<?php echo $phoneErr != '' ? '<span class="text-danger">' . $phoneErr . '</span>' : ''; ?>
						</div>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-md-8">
						<div class="form-group">
							<label>Address</label>
							<input type="text" class="form-control" placeholder="Enter address" name="address" <?php echo isset($address) && $address != '' ? 'value="' . $address . '"' : ''; ?>>
							<?php echo $addressErr != '' ? '<span class="text-danger">' . $addressErr . '</span>' : ''; ?>
						</div>
					</div>
				</div>

				<div class="row justify-content-center my-2">
					<div class="col-md-8">
						<div id="accordion">
							<div class="card">
								<div class="card-header bg-white" id="headingAadhar">
									<h5 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseAadhar" aria-expanded="true" aria-controls="collapseAadhar">
										Aadhar Details
									</h5>
								</div>

								<div id="collapseAadhar" class="collapse" aria-labelledby="headingAadhar" data-parent="#accordion">
									<div class="card-body">
										<div class="row">
											<div class="col">
												<h6>Aadhar Front</h6>
												<?php if ($aadharFront != "") { ?>
													<img class="img-fluid" src="<?php echo $aadharFront ?>" alt="User Aadhar Front | Will Portal" width="300" height="150">
												<?php } else { ?>
													<i class="far fa-file-image aadhar-placeholder"></i>
													<div class="form-group">
														<label>Upload Aadhar Back</label>
														<input type="file" class="form-control-file" name="aadhar_front">
													</div>
												<?php } ?>
												<?php echo $aadharFrontErr != '' ? '<span class="text-danger">' . $aadharFrontErr . '</span>' : ''; ?>
											</div>
											<div class="col">
												<h6>Aadhar Back</h6>
												<?php if ($aadharBack != "") { ?>
													<img class="img-fluid" src="<?php echo $aadharBack; ?>" alt="User Aadhar Back | Will Portal" width="300" height="150">
												<?php } else { ?>
													<i class="far fa-file-image aadhar-placeholder"></i>
													<div class="form-group">
														<label>Upload Aadhar Back</label>
														<input type="file" class="form-control-file" name="aadhar_back">
													</div>
												<?php } ?>
												<?php echo $aadharBackErr != '' ? '<span class="text-danger">' . $aadharBackErr . '</span>' : ''; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header bg-white" id="headingPan">
									<h5 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapsePan" aria-expanded="false" aria-controls="collapsePan">
										PAN Card Detaiils
									</h5>
								</div>
								<div id="collapsePan" class="collapse" aria-labelledby="headingPan" data-parent="#accordion">
									<div class="card-body">
										<h6>Pan Card</h6>
										<?php if ($pan != "") { ?>
											<img class="img-fluid" src="<?php echo $pan; ?>" alt="User Aadhar Fron | Will Portal" width="300" height="150">
										<?php } else { ?>
											<i class="far fa-file-image aadhar-placeholder"></i>
											<div class="form-group">
												<label>Upload PAN Card</label>
												<input type="file" class="form-control-file" name="pan_card">
											</div>
										<?php } ?>
										<?php echo $panErr != '' ? '<span class="text-danger">' . $panErr . '</span>' : ''; ?>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header bg-white" id="headingDrivingLicense">
									<h5 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseDrivinLicense" aria-expanded="false" aria-controls="collapseDrivinLicense">
										Driving License Details
									</h5>
								</div>
								<div id="collapseDrivinLicense" class="collapse" aria-labelledby="headingDrivingLicense" data-parent="#accordion">
									<div class="card-body">
										<h6>Driving License</h6>
										<?php if ($pan != "") { ?>
											<img class="img-fluid" src="<?php echo $drivingLicense; ?>" alt="User Aadhar Fron | Will Portal" width="300" height="150">
										<?php } else { ?>
											<i class="far fa-file-image aadhar-placeholder"></i>
											<div class="form-group">
												<label>Upload Driving License</label>
												<input type="file" class="form-control-file" name="driving_license">
											</div>
										<?php } ?>
										<?php echo $drivingLicenseErr != '' ? '<span class="text-danger">' . $drivingLicenseErr . '</span>' : ''; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row justify-content-center my-2">
					<div class="col-md-8">
						<button type="submit" name="subForm" class="btn btn-outline-primary btn-sm">Update</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
include_once('./adders/footer.php');
?>