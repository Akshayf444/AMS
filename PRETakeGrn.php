<?php session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $empid=$_SESSION['PRE'];
 $empName =Employee::find_by_empid($empid);
 $Errors = array();
 $finalErrorList ='';

    if(isset($_GET['apr_id'])){
        $apr_id = $_GET['apr_id'];
        $SecurityCheck = Approval::SecurityCheck2($apr_id);
        if (empty($SecurityCheck)) {
            redirect_to("AccessDenied.php");
        }
        $Items = ItemDetails::find_by_apr_id($apr_id);
        $Approval = Approval::find_by_apr_id($apr_id);
    }

    if(isset($_POST['submit'])){

        //  Creates new object of GRN
        $newGRN = new GRN();
        $length = count($_POST['item_id']);

        for ($i=0; $i < $length; $i++) { 
            $newGRN->grn_id= $newGRN->autoGenerate_id();
            $newGRN->item_id = $_POST['item_id'][$i];
            $newGRN->apr_id = $_POST['apr_id'][$i];
            
            $newGRN->date = strftime("%Y-%m-%d ", time());
            if(!empty($_POST['grn_date'][$i])){
                if (preg_match("/^(\d{1,2})\-(\d{1,2})\-(\d{4})$/", $_POST['grn_date'][$i])) {
                    
                    $dtA = new DateTime(strftime("%d-%m-%Y ", time()));
                    $dtB = new DateTime($_POST['grn_date'][$i]);
                    if ($dtB < $dtA) {
                        array_push($Errors,"Cannot Use Past Date");
                    }else{
                        $newGRN->grn_date = date("Y-m-d",strtotime($_POST['grn_date'][$i]));
                    }
                }else{
                    array_push($Errors, "Please Enter Valid Date Format.");
                }

                $dtA = new DateTime(strftime("%Y-%m-%d ", time()));
                $dtB = new DateTime($_POST['grn_date'][$i]);

            }else{
                array_push($Errors, "Please Enter GRN Date");
            }


            if (!empty($_POST['quantity_received'][$i])) {
                $newGRN->quantity_received = $_POST['quantity_received'][$i];
            }else{
                array_push($Errors, "Please Enter Received Quantity.");
            }
            
            if (!empty($_POST['quantity_remaining'][$i])) {
                 $newGRN->quantity_remaining = $_POST['quantity_remaining'][$i];
            }

            if (empty($Errors)) {
                $newGRN->create();
                $_SESSION['Errors'] = "Success";

            }
           
        }//End of loop

        if (empty($Errors)) {
            $column_name="receive";
            $status = "received" ;
            $UpdateOrderStatus = new Approval();
            foreach ($Approval as $value) {
                $UpdateOrderStatus->update_PRE_status($value->apr_id,'False','order_status');
                $UpdateOrderStatus->update_PRE_status($value->apr_id,'False','process_for_po');
                $UpdateOrderStatus->update_PRE_status($value->apr_id,$status,$column_name);
            }
            redirect_to("PRElistApproval.php");
        }else{
            $finalErrorList = array_unique($Errors);
        }
    }
 require_once(dirname(__FILE__)."/layouts/PRElayouts/header.php");?>
<div class="row">
                    <div class="col-lg-12 col-sm-12 col-md-12">
                        <h1 class="page-header">
                            Take GRN
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                            </li>
                            <li class="active">
                                <i class="fa fa-list"></i> Take GRN
                            </li>
                        </ol>
</div>
<div class="row">
        <?php  if (!empty($Errors)) {
            foreach ($finalErrorList as $value) {
            echo "<ul><li style='color:red;'>".$value."</li></ul>"; 
            } 
        }
        ?>
</div>
<div class="row">
 	<div class="col-lg-12">
    	<div class="table-responsive">
        <form action="PRETakeGrn.php?apr_id=<?php echo $_GET['apr_id']; ?>" method="post">
    		<table class="table table-bordered table-hover table-striped" id="items">
                <thead>
                    <tr>
                    	<th style="width:8%">Key No</th>
                        <th style="width:15%">Brand/Division</th>
                        <th style="width:7%">Item Category</th>
                       	<th>Description Of An Item</th>
                        <th style="width:7%">Quantity</th>
                        <th style="width:7%">Value/Item</th>
                        <th style="width:7%">Amount</th>  
                        <th style="width:10%">GRN Date</th>
                        <th style="width:10%">Quantity Received</th>
                        <th style="width:10%">Quantity Remaining</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php   $classNo = 1; foreach ($Items as $Item) { 
                        $PrDetails =PrDetails::find_by_item_id($Item->item_id);
                        
                        ?>
                        <tr class="targetfields" id="<?php echo $classNo."11"; ?>">
                        	<td><input class="form-control"  type="text" value="<?php echo $PrDetails->key_no; ?>" readonly>
                                <input type="hidden" name="item_id[]" value="<?php echo $Item->item_id; ?>">
                                <input type="hidden" name="apr_id[]" value="<?php echo $Item->apr_id; ?>">
                            </td>
                            <td><?php echo $Item->brand_id; ?></td>
                            <td><?php echo $Item->item_category; ?></td>
                            <td><?php echo $Item->description; ?></td>
                            <td><input class="form-control quantity"  type="text" value="<?php echo $Item->quantity; ?>" readonly></td>
                            <td><?php echo $Item->value; ?></td>
                            <td><?php echo $Item->amount; ?></td>
                            <td><input class="form-control "  type="text" onchange = "isDate(this.value)" name="grn_date[]" placeholder="dd-mm-yyyy"></td>
                            <td><input class="form-control received"  type="text" name="quantity_received[]" ></td>
                            <td><input class="form-control remaining"  type="text" name="quantity_remaining[]" readonly></td>
                       </tr>
                        <?php  $classNo++; } ?>
                    <tr>
	                	<td colspan="10"><button type="submit" class="btn btn-primary" name="submit">Save</button></td>
                    </tr>
                    </tbody>
                </table>
            </form>
    	</div>
    </div>
</div>
<script type="text/javascript">
//Function For Validating Date
  function isDate(value)
  {
    // regular expression to match required date format
    re = /^(\d{1,2})\-(\d{1,2})\-(\d{4})$/;
    var date = value;

    if(value != '') {
      if(regs = date.match(re)) {
        // day value between 1 and 31
        if(regs[1] < 1 || regs[1] > 31) {
          alert("Invalid value for day: " + regs[1]);
          
          return false;
        }
        if (regs[1] < (new Date()).getDate() ) {
            alert("Cannot Use Past Date");
            return false;
        }

        // month value between 1 and 12
        if(regs[2] < 1 || regs[2] > 12) {
          alert("Invalid value for month: " + regs[2]);
 
          return false;
        }

        if (regs[2] < (new Date()).getMonth() ) {
            alert("Month cannot be in the past");
            return false;
        }
      
        if(regs[3] < (new Date()).getFullYear()) {
          alert("Invalid value for year: " + regs[3] +" Cannot use past year");
          
          return false;
        }
      } else {
        alert("Invalid date format ");
        return false;
      }
    } 
}//End Of Function
</script>

<script>
jQuery(function() {

    jQuery("#items ").delegate('.received ','keyup',function() {
    var currentRow = $(this).closest('tr');
    var rowid = currentRow.attr('id');

    var qty = parseFloat($("#" + rowid).find(".quantity").val());
    var received = parseFloat($("#" + rowid).find(".received").val())||0;
    var remaining = qty - received;
    $("#" + rowid).find(".remaining").val(remaining.toFixed(2));

    });
});
</script>

<?php require_once(dirname(__FILE__)."/layouts/PRElayouts/footer.php");?>