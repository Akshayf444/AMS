<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$Items = ItemDetails::listApproved($empid, "Approved");

require_once(dirname(__FILE__) . "/layouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Delivery Report
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-shopping-cart"></i> Delivery Report
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="deliveryReport">
                <thead>
                    <tr>
                        <th>Key No</th>
                        <th>Description</th>
                        <th>Ordered Quantity</th>
                        <th>Received Till Date</th>
                        <th>Receipt Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($Items as $Item) {
                        $GRN_items = GRN::find_by_item_id($Item->item_id);
                        $receivedTillDate = 0 ;
                        foreach ($GRN_items as $GRN_item) {
                            ?>
                            <tr>
                                <td><?php
                                    $PRdetail = PrDetails::find_by_item_id($GRN_item->item_id);
                                    $ItemDetails = ItemDetails::find_by_item_id($GRN_item->item_id);

                                    if (!empty($PRdetail)) {
                                        echo $PRdetail->key_no;
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>

                                <td><?php echo $ItemDetails->description; ?></td>
                                <td><?php echo $ItemDetails->quantity; ?></td>
                                <td><?php
                                    $receivedTillDate += $GRN_item->quantity_received;
                                    echo $receivedTillDate;

                                    if ($receivedTillDate == $ItemDetails->quantity) {
                                        echo '<span class="label label-success">Delivered</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('d-m-Y', strtotime($GRN_item->date)); ?></td>
                            </tr>
                        <?php
                        }/*                         * ************* End Of inner loop************ */
                    }/*                     * ********* End Of outer loop ************ */
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="js/bootstrapDropdown.js"></script>
<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="js/dataTables.bootstrap.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
    $('#deliveryReport').dataTable();
} );
</script>
<?php require_once(dirname(__FILE__) . "/layouts/footer.php"); ?>