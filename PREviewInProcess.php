<?php
session_start();
if (!isset($_SESSION['PRE'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['PRE'];
$inProcess = GRN::PRElistItemsInProcess('inProcess');

require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Items In Process
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <i class="fa fa-recycle"></i> Items In Process
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
                    <?php
                    if (!empty($inProcess)) {

                        foreach ($inProcess as $Item) {
                            ?>
                            <tr>
                                <td><?php
                                    $PRdetail = PrDetails::find_by_item_id($Item);
                                    $Approval = ItemDetails::find_by_item_id($Item);
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
                        <?php
                        }//End Of For Loop
                    }//End Of if
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="js/bootstrapDropdown.js"></script>
<?php require_once(dirname(__FILE__) . "/layouts/PRElayouts/footer.php"); ?>