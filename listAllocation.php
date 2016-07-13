<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$Approvals = Approval::find_by_status("Approved", $empid);
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
                        <th style="width:10%">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Approvals as $Approval) { ?>
                        <tr>
                            <td><?php echo $Approval->apr_id; ?></td>
                            <td><?php echo $Approval->title; ?></td>
                            <td><?php $dropdown = ItemDetails::brandDropdown($Approval->apr_id, 1); echo $dropdown;  ?>
                            </td>

                            <td><?php
                                $Allocated = AllocationDetails::allocated($Approval->apr_id);
                                echo date('d-m-Y', strtotime($Approval->date));
                                ?></td>

                            <?php if ($Allocated === 'Sent for Approval' || $Allocated === 'Allocate') { ?>
                                <td><a href="Allocation.php?apr_id=<?php echo $Approval->apr_id; ?>"><button type="button" class="btn btn-xs btn-warning" >Allocate</button></a></td>

    <?php } else { ?>

                                <td><a href="Allocation.php?apr_id=<?php echo $Approval->apr_id; ?>">
                                        <button type="button" class="btn btn-xs <?php
                                                if ($Allocated === 'Allocated') {
                                                    echo 'btn btn-success';
                                                } else {
                                                    echo 'btn-warning';
                                                }
                                                ?>"><?php
                                                    if ($Allocated === 'Allocated') {
                                                        echo 'Allocated';
                                                    } else {
                                                        echo 'Allocate';
                                                    }
                                                    ?></button>
                                    </a>
                                </td>
                        <?php } ?>
                        </tr>
<?php }//End Of For Loop   ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</div>
<!-- /.row -->
<script>
    $(function () {
        $(".dropdown").hover(
                function () {
                    $('.dropdown-menu', this).stop(true, true).fadeIn("fast");
                    $(this).toggleClass('open');
                    $('b', this).toggleClass("caret caret-up");
                },
                function () {
                    $('.dropdown-menu', this).stop(true, true).fadeOut("fast");
                    $(this).toggleClass('open');
                    $('b', this).toggleClass("caret caret-up");
                });
    });
</script>
<script src="js/IEbuttonPatch.js"></script>
<?php require_once(dirname(__FILE__) . "/layouts/footer.php"); ?>