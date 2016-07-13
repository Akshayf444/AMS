<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$Approvals = Approval::find_by_gpm_empid($empid);

require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            List Of Approvals
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> List Of Approvals
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Requisition By</th>
                        <th>Approval Id</th>
                        <th>Title Of Approval</th>
                        <th>Brand/Division</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="width:10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($Approvals)) {
                        foreach ($Approvals as $Approval) {
                            ?>
                            <tr>
                                <td><?php
                                    $PMT = Employee::find_by_empid($Approval->empid);
                                    echo $PMT->name;
                                    ?>
                                </td>
                                <td><?php echo $Approval->apr_id; ?></td>
                                <td><?php echo $Approval->title; ?></td>
                                <td><?php $dropdown = ItemDetails::brandDropdown($Approval->apr_id, 1);
                            echo $dropdown;
                                    ?></td>

                                <td><?php echo date('d-m-Y', strtotime($Approval->date));
                            $Allocated = AllocationDetails::allocated($Approval->apr_id);
                            ?></td>

        <?php if ($Allocated === 'Sent for Approval' || $Allocated === 'Allocate') { ?>
                                    <td>Partially Allocated</td>
                                    <td><a href="GPMlistApprovalItems.php?apr_id=<?php echo $Approval->apr_id; ?>"><button type="button" class="btn btn-xs btn-warning" >Allocate</button></a></td>

        <?php } else { ?>
                                    <td>Allocated</td>
                                    <td><a href="GPMlistApprovalItems.php?apr_id=<?php echo $Approval->apr_id; ?>">
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
                            <?php
                        }//End Of For Loop
                    }//End Of If
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
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

<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>