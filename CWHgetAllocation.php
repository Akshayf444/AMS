<?php
require_once(dirname(__FILE__) . "/includes/initialize.php");

if (isset($_POST['search_term']) && isset($_POST['approval'])) {
    $apr_id = $_POST['search_term'];
    $Items = ItemDetails::find_by_apr_id($apr_id);
    if (!empty($Items)) {
        ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th >Key No</th>
                    <th >Brand</th>
                    <th >Description</th>
                    <th >Quantity</th>
                    <th >Action</th>

                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($Items as $Item) {
                    $PRdetail = PrDetails::find_by_item_id($Item->item_id);
                    ?>
                    <tr>
                        <td><?php
                            if (!empty($PRdetail)) {
                                echo $PRdetail->key_no;
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php
                            $dropdown = ItemDetails::brandDropdown($Item->brand_id, 0);
                            echo $dropdown;
                            ?>
                        </td>
                        <td><?php echo $Item->description; ?></td>
                        <td><?php echo $Item->quantity; ?></td>
                        <td><a href="CWHprintAllocation.php?item_id=<?php echo $Item->item_id; ?>"><button type="button" class="btn btn-xs btn-primary">View</button></td>               
                    </tr>
        <?php } ?>
            </tbody>
        </table>

        <script src="js/bootstrapDropdown.js"></script>
        <?php
    } else {
        echo "Details Not Found.";
    }
}
if (isset($_POST['search_term']) && isset($_POST['KeyNo'])) {
    $keyNo = $_POST['search_term'];
    $PRdetail = PrDetails::find_by_key_no($keyNo);
    if (!empty($PRdetail)) {
        $ItemDetails = ItemDetails::find_by_item_id($PRdetail->item_id);
        ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th >Key No</th>
                    <th >Brand</th>
                    <th >Description</th>
                    <th >Quantity</th>
                    <th >Action</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $PRdetail->key_no; ?></td>
                    <td><?php
                $dropdown = ItemDetails::brandDropdown($ItemDetails->brand_id, 0);
                echo $dropdown;
                ?>
                    </td>
                    <td><?php echo $ItemDetails->description; ?></td>

                    <td><?php echo $ItemDetails->quantity; ?></td>
                    <td><a href="CWHprintAllocation.php?item_id=<?php echo $ItemDetails->item_id; ?>"><button type="button" class="btn btn-xs btn-primary">View</button></td>          		
                </tr>
            </tbody>
        </table>
        <script src="js/bootstrapDropdown.js"></script>
        <?php
    } else {
        echo "Details Not Found.";
    }
}//End Of $_POST['keyNo']

if (isset($_POST['search_term']) && isset($_POST['PoNo'])) {
    $PoNo = $_POST['search_term'];

    $POdetails = PoDetails::find_by_po_no($PoNo);
    if (!empty($POdetails)) {
        $ItemDetails = ItemDetails::find_by_item_id($POdetails->item_id);
        ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th >Key No</th>
                    <th >Brand</th>
                    <th >Description</th>
                    <th >Quantity</th>
                    <th >Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php $PRdetail = PrDetails::find_by_item_id($ItemDetails->item_id);
        echo $PRdetail->key_no;
        ?></td>
                    <td><?php
                $dropdown = ItemDetails::brandDropdown($ItemDetails->brand_id, 0);
                echo $dropdown;
                ?>
                    </td>
                    <td><?php echo $ItemDetails->description; ?></td>

                    <td><?php echo $ItemDetails->quantity; ?></td>
                    <td><a href="CWHprintAllocation.php?item_id=<?php echo $ItemDetails->item_id; ?>"><button type="button" class="btn btn-xs btn-primary">View</button></td>               
                </tr>
            </tbody>
        </table>
        <script src="js/bootstrapDropdown.js"></script>
        <?php
    } else {
        echo "Details Not Found.";
    }
}//End Of 
?>