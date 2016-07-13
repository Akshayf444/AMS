<?php
//session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
require_once(dirname(__FILE__) . "/includes/initialize.php");

if (isset($_POST['search_term']) && isset($_POST['approval'])) {
    $KeyNoFlag = true;
    $Approval = Approval::find_by_apr_id3($_POST['search_term']);
    if (!empty($Approval)) {
        $Items = ItemDetails::find_by_apr_id($_POST['search_term']);
        if (!empty($Items)) {
            ?>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">Key No</th>
                        <th style="width:20%">Brand</th>

                        <th style="width:25%">Description</th>
                        <th style="width:13%">Ordered Quantity</th>
                        <th>Received Till Date</th>
                        <th style="width:13%">Received Quantity</th>
                        <th style="width:15%">GRN Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($Items as $item) {


                        $receivedQuantity = GRN::find_by_item_id2($item->item_id);
                        $receivedTillDate = GRN::find_received_quantity($item->item_id);
                        ?>
                        <tr>
                            <td><?php
                                $PRdetail = PrDetails::find_by_item_id($item->item_id);
                                if (!empty($PRdetail)) {
                                    echo $PRdetail->key_no;
                                } else {
                                    echo '-';
                                    $KeyNoFlag = false;
                                }
                                ?></td>
                            <td><?php
                                $dropdown = ItemDetails::brandDropdown($item->brand_id, 0);
                                echo $dropdown;
                                ?>
                            </td>
                            <td><?php echo $item->description; ?></td>
                            <td><?php echo $item->quantity; ?></td>
                            <td><?php
                                if (!empty($receivedTillDate)) {
                                    echo $receivedTillDate;
                                } else {
                                    echo 0;
                                }
                                ?>
                            </td>

                            <td><input type="hidden" name="item_id[]" value="<?php echo $item->item_id; ?>">
                                <input type="hidden" name="apr_id[]" value="<?php echo $item->apr_id; ?>">
                                <?php if ($item->quantity == $receivedTillDate) { ?>
                                    <input type="hidden" name="quantity_received[]" class="form-control" value="0"  >
                                    This Item Has Been Delivered.
                                <?php } else { ?>
                                    <input type="textbox" name="quantity_received[]" class="form-control" value=""  >
                                <?php } ?>
                            </td>

                            <td><input class="form-control "  type="text" onchange = "isDate(this.value)" name="grn_date[]" 
                                       value="<?php
                                       if (!empty($receivedQuantity)) {
                                           echo date('d-m-Y', strtotime($receivedQuantity->date));
                                       } else {
                                           echo date('d-m-Y');
                                       }
                                       ?>" placeholder="dd-mm-yyyy">
                            </td>

                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="8">	
                            <?php if ($KeyNoFlag == true) { ?>

                                <input type="submit" name="submit" value="Save" class="btn btn-primary" >

                            <?php } ?> 
                        </td>
                    </tr>
                </tbody>
            </table>
            <script src="js/bootstrapDropdown.js"></script>
            <?php
        } else {
            echo "Details Not Found.";
        }
    } else {
        echo "Details Not Found.";
    }
}//End Of Approval

if (isset($_POST['search_term']) && isset($_POST['PR'])) {
    $poNo = $_POST['search_term'];
    $POdetails = PoDetails::find_by_po_no($poNo);

    if (!empty($POdetails)) {
        ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th style="width:10%">Key No</th>
                    <th style="width:20%">Brand</th>

                    <th style="width:25%">Description</th>
                    <th style="width:13%">Ordered Quantity</th>
                    <th>Received Till Date</th>
                    <th style="width:13%">Received Quantity</th>
                    <th style="width:15%">GRN Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($POdetails as $POdetail) {
                    $ItemDetails = ItemDetails::find_by_item_id($POdetail->item_id);
                    $receivedQuantity = GRN::find_by_item_id2($POdetail->item_id);
                    $receivedTillDate = GRN::find_received_quantity($POdetail->item_id);
                    ?>
                    <tr>
                        <td><?php
                            $PRdetail = PrDetails::find_by_item_id($ItemDetails->item_id);
                            echo $PRdetail->key_no;
                            ?></td>

                        <td><?php
                            $dropdown = ItemDetails::brandDropdown($ItemDetails->brand_id, 0);
                            echo $dropdown;
                            ?>
                        </td>
                        <td><?php echo $ItemDetails->description; ?></td>
                        <td><?php echo $ItemDetails->quantity; ?></td>
                        <td><?php
                            if (!empty($receivedTillDate)) {
                                echo $receivedTillDate;
                            } else {
                                echo 0;
                            }
                            ?>
                        </td>
                        <td><input type="hidden" name="item_id[]" value="<?php echo $ItemDetails->item_id; ?>">
                            <input type="hidden" name="apr_id[]" value="<?php echo $ItemDetails->apr_id; ?>">
                            <?php if ($ItemDetails->quantity == $receivedTillDate) { ?>
                                <input type="hidden" name="quantity_received[]" class="form-control" value="0"  >
                                This Item Has Been Delivered.
                            <?php } else { ?>
                                <input type="textbox" name="quantity_received[]" class="form-control" value=""  >
                            <?php } ?>
                        </td>



                        <td><input class="form-control "  type="text" onchange = "isDate(this.value)" 
                                   value="<?php
                                   if (!empty($receivedQuantity)) {
                                       echo date('d-m-Y', strtotime($receivedQuantity->date));
                                   } else {
                                       echo date('d-m-Y');
                                   }
                                   ?>" name="grn_date[]" placeholder="dd-mm-yyyy"></td>

                    </tr>

                <?php } ?>
                <tr>
                    <td colspan="8">                
                        <input type="submit" name="submit" value="Save" class="btn btn-primary" >
                    </td>
                </tr>
            </tbody>
        </table>
        <script src="js/bootstrapDropdown.js"></script>

        <?php
    } else {
        echo "Details Not Found.";
    }
}//End Of PR


if (isset($_POST['search_term']) && isset($_POST['KeyNo'])) {
    $keyNo = $_POST['search_term'];
    $PRdetail = PrDetails::find_by_key_no($keyNo);
    if (!empty($PRdetail)) {
        $ItemDetails = ItemDetails::find_by_item_id($PRdetail->item_id);

        $receivedQuantity = GRN::find_by_item_id2($PRdetail->item_id);
        $receivedTillDate = GRN ::find_received_quantity($PRdetail->item_id);
        ?>
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th style="width:10%">Key No</th>
                    <th style="width:20%">Brand</th>

                    <th style="width:25%">Description</th>
                    <th style="width:13%">Ordered Quantity</th>
                    <th>Received Till Date</th>
                    <th style="width:13%">Received Quantity</th>
                    <th style="width:15%">GRN Date</th>
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
                    <td><?php
                        if (!empty($receivedTillDate)) {
                            echo $receivedTillDate;
                        } else {
                            echo 0;
                        }
                        ?>
                    </td>
                    <td><input type="hidden" name="item_id[]" value="<?php echo $ItemDetails->item_id; ?>">
                        <input type="hidden" name="apr_id[]" value="<?php echo $ItemDetails->apr_id; ?>">
                        <?php if ($ItemDetails->quantity == $receivedTillDate) { ?>
                            <input type="hidden" name="quantity_received[]" class="form-control" value="0"  >
                            This Item Has Been Delivered.
                        <?php } else { ?>
                            <input type="textbox" name="quantity_received[]" class="form-control" value=""  >
                        <?php } ?>
                    </td>

                    <td><input class="form-control "  type="text" onchange = "isDate(this.value)" 
                               value="<?php
                               if (!empty($receivedQuantity)) {
                                   echo date('d-m-Y', strtotime($receivedQuantity->date));
                               } else {
                                   echo date('d-m-Y');
                               }
                               ?>" name="grn_date[]" placeholder="dd-mm-yyyy"></td>

                </tr>
                <tr>
                    <td colspan="8">				
                        <input type="submit" name="submit" value="Save" class="btn btn-primary" >
                    </td>
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