<?php
session_start();
if (!isset($_SESSION['PRE'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['PRE'];
$Approved = ItemDetails::PRElistApproved("Pending");

require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Approved Items
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Approved Items
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
                        <th>Key No</th>
                        <th>Description</th>
                        <th>Brand</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                            <?php foreach ($Approved as $Approval) { ?>
                        <tr>
                            <td><?php
                            $PRdetail = PrDetails::find_by_item_id($Approval->item_id);
                            if (!empty($PRdetail)) {
                                echo $PRdetail->key_no;
                            } else {
                                echo '-';
                            }
                                ?>
                            </td>

                            <td><?php echo $Approval->description; ?></td>
                            <td><?php
                                $dropdown = ItemDetails::brandDropdown($Approval->brand_id, 0);
                                echo $dropdown;
                                ?>
                            </td>

                            <td><?php
                            $status = GRN::isDelivered($Approval->item_id);
                            if (!empty($status)) {
                                echo $status;
                            }
                            ?></td>
                        </tr>
<?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="js/bootstrapDropdown.js"></script>

<?php require_once(dirname(__FILE__) . "/layouts/PRElayouts/footer.php"); ?>