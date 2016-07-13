<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$EncryptUrl = new Encryption();

$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$Approvals = Approval::find_by_empid($empid);

if (isset($_POST['submit'])) {
    $Approval = new Approval();
    $apr_id = $_POST['approvalid'];
    if ($_POST['approved'] == 'Approved') {
        $status = "Approved";
    } else {
        $status = "Rejected";
    }
    $Approval->updateStatus($apr_id, $status);

    redirect_to("listApproval.php");
}
require_once(dirname(__FILE__) . "/layouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            List Of Approvals
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> List Of Approvals
            </li>
        </ol>
    </div>
</div>
<div class="row" style="margin-bottom:2em;">
    <div class="col-lg-1 pull-center">
        <a href="AddApproval.php"><button type="button" class="btn btn-default" >Add New</button></a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Approval Id</th>
                        <th>Title Of Approval</th>
                        <th>Brand/Division</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Approvals as $Approval) { ?>
                        <tr>
                            <td><?php echo $Approval->apr_id; ?></td>
                            <?php if ($Approval->status == 'Approved') { ?>
                                <td><?php echo $Approval->title; ?></td>   
                            <?php } else { ?>
                                <td><a href="editApproval.php?apr_id=<?php echo $EncryptUrl->encode($Approval->apr_id); ?>" ><?php echo $Approval->title; ?></a></td>
                            <?php } ?>

                            <td><?php
                                $dropdown = ItemDetails::brandDropdown($Approval->apr_id, 1);
                                echo $dropdown;
                                ?></td>

                            <td><?php echo date('d-m-Y', strtotime($Approval->date)); ?></td>
                            <td><?php
                                $status = Approval::approvalStatus($Approval->apr_id);
                                $finalStatus = join(",", $status);
                                echo $finalStatus;
                                ?>
                            </td>
                            <?php
                            $POdetails = PoDetails::proceed($Approval->apr_id);
                            if ($Approval->receive == "received") {
                                ?>

                            <?php } elseif ($POdetails == true || $Approval->process_for_po == "processed") { ?>

                            <?php } else { ?>

                            <?php } ?>

    <?php if ($Approval->status == 'Approved') { ?>
                                <td><a href="viewApproval.php?apr_id=<?php echo $EncryptUrl->encode($Approval->apr_id); ?>"><button type="button" class="btn btn-xs btn-primary">View</button></td>

    <?php } else { ?>
                                <td><button type="button" class="btn btn-xs btn-info dialog" data-toggle="modal" data-target="#myModal" data-id="<?php echo $Approval->apr_id; ?>" 
                                            >Change Status</button></td>
    <?php } ?>

                        </tr>
<?php } ?>
                </tbody>
            </table>
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title" id="myModalLabel">Change Status</h4>
                        </div>
                        <div class="modal-body">
                            <form action="listApproval.php" method="post">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <tbody>
                                            <tr>
                                                <td>

                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="approved" id="optionsRadios1" value="Approved" checked>Approved
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="approved" id="optionsRadios2" value="Not Approved">Not Approved
                                                        </label>
                                                    </div>
                                                    <input type="hidden" id="apr_id" name="approvalid" value="">
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-primary" >Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
<script src="js/bootstrapDropdown.js"></script>

<script>
    $(document).on("click", ".dialog", function () {
        var myBookId = $(this).data('id');
        $(".modal-body #apr_id").val(myBookId);
    });
</script>          
<script src="js/IEbuttonPatch.js"></script>
<?php require_once(dirname(__FILE__) . "/layouts/footer.php"); ?>