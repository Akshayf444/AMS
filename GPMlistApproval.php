<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];

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
                        <th>Date</th>
                        <th>Requisition By</th>
                        <th>Approval Id</th>
                        <th>Title Of Approval</th>
                        <th>Brand</th>

                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($Approvals)) {
                        foreach ($Approvals as $Approval) {
                            ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($Approval->date)); ?></td>
                                <td><?php $PMT = Employee::find_by_empid($Approval->empid);
                    echo $PMT->name; ?></td>
                                <td><?php echo $Approval->apr_id; ?></td>
                                <td><?php echo $Approval->title; ?></td>
                                <td><?php $brands = ItemDetails::brandDropdown($Approval->apr_id, 1); 
                                    echo $brands;?>
                                </td>

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
                                <td>
                                    <form action ="GPMviewApproval.php" method="post">
                                        <button type="submit" class="btn btn-xs btn-primary" name="submit">View</button>
                                        <input type="hidden" name="empid" value="<?php echo $Approval->empid; ?>">
                                        <input type="hidden" name="apr_id" value="<?php echo $Approval->apr_id; ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
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