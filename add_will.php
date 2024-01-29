<?php
$page = "Add Will";
include_once("./adders/header.php");

$getBeneficiary = json_decode(select_query($con, "*", "beneficiary_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$title = $titleErr = $description = $descriptionErr = "";

if (isset($_GET['id'])) {
    $id = decryption($_GET['id']);

    $willDetails = json_decode(select_query($con, "*", "will_master", "id=" . $id, "", "", ""));

    if (!empty($willDetails)) {
        $title = $willDetails[0]->title;
        $description = $willDetails[0]->description;
    }
}

if (isset($_POST['titleSub'])) {

    $errorFlag = false;

    $title = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_title'])));
    $description = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['will_description'])));

    $titleErr = $title != '' ? '' : 'Please insert title';
    $descriptionErr = $description != '' ? '' : 'Please insert description';

    if ($titleErr == '' && $descriptionErr == '') {
        if (isset($_GET['id'])) {
            $id = decryption($_GET['id']);
            $insertedWill = json_decode(update_query($con, "will_master", "title='$title', description='$description', updatedBy=" . $_SESSION['uid'], "id='$id'"));

            if ($insertedWill != '') {
                echo '<script>swal({title: "Will updated successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_house_property.php?id=' . $_GET['id'] . '&edit=true";});</script>';
            } else {
                echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"});</script>';
            }
        } else {
            $insertedWill = json_decode(insert_query($con, array('title', 'description', 'createdBy', 'updatedBy'), array($title, $description, $_SESSION['uid'], $_SESSION['uid']), "will_master"));

            if ($insertedWill != '') {
                echo '<script>swal({title: "Will added successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "add_house_property.php?id=' . encryption($insertedWill) . '&edit=false";});</script>';
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
<div class="container-fluid pt-3 mb-4">
    <form action="" method="POST">
        <div class="row">
            <div class="form-group w-100">
                <label>Title</label>
                <input type="text" class="form-control" placeholder="Enter will title" name="will_title" <?php echo isset($title) && $title != '' ? 'value="' . $title . '"' : ''; ?>>
                <?php echo $titleErr != '' ? '<span class="text-danger">' . $titleErr . '</span>' : ''; ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group w-100">
                <label>Will Description</label>
                <textarea class="form-control" rows="3" name="will_description" placeholder="Enter will description"><?php echo isset($description) && $description != '' ? trim($description) : ''; ?></textarea>
                <?php echo $descriptionErr != '' ? '<span class="text-danger">' . $descriptionErr . '</span>' : ''; ?>
            </div>
        </div>

        <div class="row">
            <?php if (isset($_GET['id'])) { ?>
                <a href="add_house_property.php?id=<?php echo $_GET['id']; ?>&edit=<?php echo isset($_GET['id']); ?>" class="btn btn-outline-primary btn-sm mx-1">Skip</a>
            <?php } ?>

            <button type="submit" name="titleSub" class="btn btn-outline-primary btn-sm mx-1">Submit Will</button>
        </div>
    </form>
</div>
<?php
include_once('./adders/footer.php');
?>