<?php
$page = "Beneficiary";
include_once("./adders/header.php");
$documentsUploaded = json_decode((select_query($con, "fileType", "user_file_master", "userId=" . $_SESSION['uid'] . " AND fileType IN ('0','1','2','3') AND enabled='1'", "", "", "")));

if (count($documentsUploaded) != 4)
	header("Location: profile.php");

$nameErr = $phoneErr = $emailErr = $addressErr = $name = $email = $phone = $address = '';

if (!empty($_GET)) {
	$id = decryption($_GET['id']);
	$action = $_GET['action'];

	if ($action == 'Dt') {
		$result = delete_query($con, "beneficiary_master", "id=" . $id);

		if ($result)
			echo '<script>swal({title: "Beneficiary deleted successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "beneficiary.php";});</script>';
		else
			echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"}).then(function() {window.location.href = "beneficiary.php";});</script>';
	} else if ($action == 'Ed') {
		$beneficiaryDetails = json_decode(select_query($con, "*", "beneficiary_master", "id=" . $id, "", "", ""));
		$name = $beneficiaryDetails[0]->name;
		$email = $beneficiaryDetails[0]->email;
		$phone = $beneficiaryDetails[0]->phone;
		$address = $beneficiaryDetails[0]->address;
	} else {
		header("Location: beneficiary.php");
	}
}

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "createdBy=" . $_SESSION['uid'], "", "", ""));

if (isset($_POST['prodSub'])) {
	$name = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['beneficiary_name'])));
	$phone = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['beneficiary_phone'])));
	$email = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['beneficiary_email'])));
	$address = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['beneficiary_address'])));

	if (!preg_match('/^[a-zA-Z]+[a-zA-Z ]+$/', $name))
		$nameErr = "Only alphabets and spaces are allowed";

	if (!preg_match('/^[0-9]{10}+$/', $phone))
		$phoneErr = "Only numbers of 10 digits are allowed";

	if (!preg_match('/^(\w*\s*[\-\,\/\.\(\)\&]*)+$/i', $address))
		$addressErr = "Enter valid address";

	if ($name == '')
		$nameErr = "Beneficiary name is required";

	if ($phone == '')
		$phoneErr = "Beneficiary phone number is required";

	if ($email == '')
		$emailErr = "Beneficiary email address is required";

	if ($address == '')
		$addressErr = "Beneficiary address is required";

	if ($nameErr == '' && $phoneErr == '' && $emailErr == '' && $addressErr == '') {
		if (!empty($_GET['id'])) {
			$id = decryption($_GET['id']);
			$addGurantor = json_decode(update_query($con, "beneficiary_master", "name='$name', phone='$phone', email='$email', address='$address'", "id=$id"));

			if ($addGurantor) {
				echo '<script>swal({title: "Beneficiary updated successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "beneficiary.php";});</script>';
			} else {
				echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"});</script>';
			}
		} else {
			$addGurantor = json_decode(insert_query($con, array('name', 'phone', 'email', 'address', 'createdBy'), array($name, $phone, $email, $address, $_SESSION['uid']), "beneficiary_master"));
			
			if ($addGurantor) {
				echo '<script>swal({title: "Beneficiary added successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "beneficiary.php";});</script>';
			} else {
				echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"});</script>';
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

<div class="container-fluid pt-3">
	<div class="row my-3 ml-1">
		<button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addBeneficiaryModalCenter">Add New Beneficiary</button>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="addBeneficiaryModalCenter" tabindex="-1" role="dialog" aria-labelledby="addBeneficiaryModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header primary-head">
					<h5 class="modal-title" id="addBeneficiaryModalLongTitle">Add New Beneficiary</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="" method="POST">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="">Beneficiary Name</label>
									<input type="text" name="beneficiary_name" class="form-control form-control-sm" <?php echo isset($name) && $name != '' ? 'value="' . $name . '"' : ''; ?>>
									<?php echo $nameErr != '' ? '<span class="text-danger">' . $nameErr . '</span>' : ''; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Beneficiary Phone Number</label>
									<input type="number" name="beneficiary_phone" class="form-control form-control-sm" <?php echo isset($phone) && $phone != '' ? 'value="' . $phone . '"' : ''; ?>>
									<?php echo $phoneErr != '' ? '<span class="text-danger">' . $phoneErr . '</span>' : ''; ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Beneficiary Email Address</label>
									<input type="email" name="beneficiary_email" class="form-control form-control-sm" <?php echo isset($email) && $email != '' ? 'value="' . $email . '"' : ''; ?>>
									<?php echo $emailErr != '' ? '<span class="text-danger">' . $emailErr . '</span>' : ''; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="">Address</label>
									<textarea class="form-control" name="beneficiary_address" rows="3"><?php echo isset($address) && $address != '' ? $address : ''; ?></textarea>
									<?php echo $addressErr != '' ? '<span class="text-danger">' . $addressErr . '</span>' : ''; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" name="prodSub" class="btn btn-outline-primary btn-sm">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<table id="beneficiarysTable" class="display nowrap w-100 table-responsive-sm ">
			<thead class="thead">
				<th>#</th>
				<th>Name</th>
				<th>Phone</th>
				<th>Email</th>
				<th>Address</th>
				<th>Action</th>
			</thead>

			<tbody>
				<?php
				if (!empty($getBeneficiary)) {
					$cnt = 1;
					foreach ($getBeneficiary as $beneficiary) { ?>
						<tr>
							<td><?php echo $cnt++; ?></td>
							<td><?php echo $beneficiary->name; ?></td>
							<td><?php echo $beneficiary->phone; ?></td>
							<td><?php echo $beneficiary->email; ?></td>
							<td><?php echo $beneficiary->address; ?></td>
							<td>
								<a href="./beneficiary.php?id=<?php echo encryption($beneficiary->id); ?>&action=Ed">
									<i class="fas fa-edit"></i>
								</a>
								<a href="./beneficiary.php?id=<?php echo encryption($beneficiary->id); ?>&action=Dt">
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
</div>
<?php
include_once('./adders/footer.php');
?>