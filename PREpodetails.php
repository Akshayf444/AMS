<?php
session_start();
if (!isset($_SESSION['PRE'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$EncryptUrl = new Encryption();

$Errors = array();
if (isset($_GET['apr_id'])) {
    $apr_id = $EncryptUrl->decode($_GET['apr_id']);
    $SecurityCheck = Approval::SecurityCheck2($apr_id);
    if (empty($SecurityCheck)) {
        redirect_to("AccessDenied.php");
    }
    $Items = ItemDetails::find_by_apr_id($apr_id);
    $Approval = Approval::find_by_apr_id2($apr_id);
} else {
    redirect_to("PRElistApproval.php");
}

if (isset($_POST['save'])) {

    $Approval = Approval::find_by_apr_id2($_POST['apr_id1']);

    $length = count($_POST['po_date']);
    for ($i = 0; $i < $length; $i++) {
        $newPODetails = new PoDetails();
        $newPODetails->item_id = $_POST['item_id'][$i];

        if (!empty($_POST['po_date'][$i])) {
            if (preg_match("/^(\d{1,2})\-(\d{1,2})\-(\d{4})$/", $_POST['po_date'][$i])) {
                $dtA = new DateTime(strftime("%d-%m-%Y ", time()));
                $dtB = new DateTime($_POST['po_date'][$i]);

                $newPODetails->po_date = date("Y-m-d", strtotime($_POST['po_date'][$i]));
            }
        } else {
            array_push($Errors, "Please Enter Po Date");
        }

        if (!empty($_POST['po_no'][$i])) {
            $newPODetails->pr_no = trim($_POST['po_no'][$i]);
        } else {
            array_push($Errors, "Please Enter Po No");
        }

        if (!empty($_POST['cost'][$i])) {
            $newPODetails->line_no = trim($_POST['cost'][$i]);
        } else {
            array_push($Errors, "Please Enter Po Cost");
        }

        if (empty($Errors)) {
            $found_po = PoDetails::find_by_item_id($newPODetails->item_id);
            if (!empty($found_po)) {
                //Update details
                $newPODetails->date = strftime("%Y-%m-%d ", time());
                $newPODetails->po_id = $found_po->po_id;
                $newPODetails->update($found_po->po_id);
            } else {
                //Add details
                $newPODetails->date = strftime("%Y-%m-%d ", time());
                $newPODetails->po_id = $newPODetails->autoGenerate_id();
                $newPODetails->create();
            }
        } else {
            array_push($Errors, "Error Occured.");
        }
    }//End Of For loop

    $POdetails = PoDetails::proceed($Approval->apr_id);
    if (empty($Errors) && $POdetails == true) {
        $column_name = "receive";
        $status = "received";
        $UpdateOrderStatus = new Approval();
        //foreach ($Approvals as $value) {
        //$UpdateOrderStatus->update_PRE_status($Approval->apr_id,'False','process_for_po');
        $UpdateOrderStatus->update_PRE_status($Approval->apr_id, $status, $column_name);
        //}
    } else {
        $finalErrorList = array_unique($Errors);
    }
    redirect_to("PRElistApproval.php");
}


require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12">
        <h1 class="page-header">
            PO Details
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> PO Details
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">
        <form action="PREpodetails.php?apr_id=<?php echo $_GET['apr_id']; ?>" method="post">
            <table class="table table-bordered table-hover table-striped" id="items">
                <thead>
                    <tr>
                        <th style="width:10%">Key No</th>
                        <th style="width:20%">Brand</th>
                       	<th>Description</th>
                        <th style="width:7%">Quantity</th> 
                        <th style="width:12%">PO Date</th>
                        <th style="width:15%">PR No</th>
                        <th style="width:10%">Line No</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Items as $Item) { ?>
                        <tr>
                            <td><?php
                        $findPoDetails = PoDetails::find_by_item_id($Item->item_id);
                        $keyNo = PrDetails::find_by_item_id($Item->item_id);
                        echo $keyNo->key_no;
                        ?></td>
                            <td><?php
                            $dropdown = ItemDetails::brandDropdown($Item->brand_id, 0);
                            echo $dropdown;
                            ?>
                            </td>
                            <td><?php echo $Item->description; ?></td>
                            <td><?php echo $Item->quantity; ?></td>
                            <td><input class="form-control" name="po_date[]"  type="text" onchange = "isDate(this.value)" placeholder="dd-mm-yyyy" 
                                       value="<?php
                                       if (!empty($findPoDetails)) {
                                           echo date('d-m-Y', strtotime($findPoDetails->po_date));
                                       } else {
                                           echo date('d-m-Y');
                                       }
                                       ?>">

                                <input type ="hidden" name="item_id[]" value="<?php echo $Item->item_id; ?>">
                            </td>

                            <td><input class="form-control" name="po_no[]"  type="text" value="<?php
                            if (!empty($findPoDetails)) {
                                echo $findPoDetails->pr_no;
                            }
                                       ?>">
                            </td>

                            <td><input class="form-control" name="cost[]"  type="text" value="<?php
                                   if (!empty($findPoDetails)) {
                                       echo $findPoDetails->line_no;
                                   }
                                   ?>" >
                            </td>
                        </tr>
<?php } ?>
                    <tr>
                        <td colspan="7">
                            <input type="hidden" name="apr_id1" value="<?php echo $Approval->apr_id; ?>">				
                            <input type="submit" name="save" value="Save" class="btn btn-info" >
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<script src="js/bootstrapDropdown.js"></script>
<script src="js/dateValidator.js"></script>

<?php require_once(dirname(__FILE__) . "/layouts/PRElayouts/footer.php"); ?>