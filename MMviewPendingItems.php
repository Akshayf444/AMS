<?php
require_once(dirname(__FILE__) . "/MMheader.php");

$Employees = Employee::find_by_mm($empid);
$finalPending = array();

foreach ($Employees as $Employee) {
    $Approved = ItemDetails::listApproved($Employee->empid, "Pending");
    foreach ($Approved as $value) {
        array_push($finalPending, $value);
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Pending Items
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Pending Items
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
                    if (!empty($finalPending)) {

                        foreach ($finalPending as $Approval) {
                            ?>
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
                                    $brands = ItemDetails::brandDropdown($Approval->brand_id, 0);
                                    echo $brands;
                                    ?>
                                </td>

                                <td>Pending</td>
                            </tr>
                        <?php
                        }
                    }//End Of If....
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="js/bootstrapDropdown.js"></script>
<?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>