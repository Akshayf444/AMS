<?php
session_start();
if (!isset($_SESSION['CWH'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$grandTotal = 0;
$CopyFromItemid = '';
//For Calculating total quantity 
$grandTotal2 = 0;

if (isset($_POST['search_term2'])) {
    $item_id = $_POST['search_term2'];
    $Manpowers = AllocationDetails::find_by_item_id2($item_id);

    if (!empty($Manpowers)) {
        foreach ($Manpowers as $Manpower) {
            $grandTotal2+=$Manpower->total_quantity;
        }
    } else {
        $grandTotal2 = 0;
    }

    $totalAmount = ItemDetails::find_by_item_id($item_id);
    if (!empty($totalAmount)) {
        $quantity = $totalAmount->quantity;
    } else {
        $quantity = 0;
    }
    ?>
    <div class="col-lg-3" style="background:#fff;float:right;position:fixed;top:10%;right:0;z-index:1;outline:1px solid #ddd" id="budget">
        <table class="table " id="items">
            <tr>
                <td style="border-top:none;width:50%"><strong>Ordered Quantity</strong></td>
                <td style="border-top:none"><input type="text" class="form-control" id="order" value="<?php echo $quantity; ?>" readonly></td> 
            </tr>
            <tr>
                <td style="border-top:none;width:50%"><strong>Total</strong></td>
                <td style="border-top:none"><input type="text" class="form-control" id="total1" value="<?php echo $grandTotal2; ?>" readonly></td> 
            </tr>
            <tr>
                <td style="width:50%"><strong>Balance At CWH</strong></td>
                <td><span id="balance"><?php echo $quantity - $grandTotal2; ?></span></td> 
            </tr>
        </table>
    </div>

<?php
}

/* * ** List Of Item Details *** */
if (isset($_POST['search_term'])) {
    $item_id = $_POST['search_term'];
    $Manpowers = AllocationDetails::find_by_item_id2($item_id);
    if (!empty($Manpowers)) {
        ?>
        <div class="table-responsive col-lg-9 " >
            <table class="table table-bordered table-hover table-striped" id="items">
                <thead>
                    <tr>
                        <th>Region</th>
                        <th>Depot</th>
                        <th>Approved Manpower</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody
                                    <?php foreach ($Manpowers as $Manpower) { ?>

                        <tr class="targetfields" >
                            <td>
                                <div class="form-group">
                                    <label><?php
                                    $regionName = Region::find_by_region_id($Manpower->region_id);
                                    $depotName = Depot::find_by_depot_code($regionName->depot_id);
                                    if (isset($depotName->depot_name)) {
                                        echo $depotName->depot_name;
                                    } else {
                                        echo 'NA';
                                    }
                                    ?></label> 
                                    <input type="hidden" name="depot_id[]" value="<?php
                                    if (isset($depotName->depot_id)) {
                                        echo $depotName->depot_id;
                                    } else {
                                        echo 'NA';
                                    }
                                    ?>">
                                </div>
                            </td>

                            <td>
                                <div class="form-group">
                                    <label><?php echo $regionName->region_name; ?></label>
                                    <input type="hidden" name="region_id[]" value="<?php echo $regionName->region_id; ?>">
                                </div>
                            </td>


                            <td><?php echo $Manpower->no_of_persons;
            $grandTotal+=$Manpower->no_of_persons; ?></td>

                            <td><?php echo $Manpower->total_quantity;
            $grandTotal2+=$Manpower->total_quantity; ?> 
                                <input type="hidden" name="alloc_id[]" value="<?php echo $Manpower->alloc_id; ?>">
                            </td>
                        </tr>

        <?php } ?>
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><?php echo $grandTotal; ?></td>
                        <td><input type="text" class="form-control" id="total" value="<?php echo $grandTotal2; ?>" readonly></td> 
                    </tr>

                </tbody>
            </table>
        </div>
    <?php
    }
}
?>