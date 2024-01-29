<?php
$page = "Will";
include_once("./adders/header.php");

if (!empty($_GET)) {
	$id = decryption($_GET['id']);
	$action = $_GET['action'];

	if ($action == 'Dt') {
		$result = delete_query($con, "will_master", "id=" . $id);

		if ($result)
			echo '<script>swal({title: "Will deleted successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "will.php";});</script>';
		else
			echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"}).then(function() {window.location.href = "will.php";});</script>';
	} else {
		header("Location: will.php");
	}
}

$getwill = json_decode(select_query($con, "*", "will_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));
?>
<!--Container Main start-->
<div class="m-3">
	<div class="row">
		<h4 class="text-uppercase"><?php echo $page; ?></h4>
	</div>
</div>

<div class="container-fluid pt-3">
	<div class="row my-3 ml-1">
		<a href="add_will.php?edit=false" class="btn btn-outline-primary btn-sm">Add New Will</a>
	</div>

	<div class="container-fluid">
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
							<td><?php echo $will->approved ? 'Approved' : 'Pending (' . $approvedCount .' of '. count($getWillGuarantor) .' approved)'; ?></td>
							<td>
								<a href="./add_will.php?id=<?php echo encryption($will->id); ?>&edit=true">
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
</div>
<?php
include_once('./adders/footer.php');
?>