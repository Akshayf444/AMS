<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php"); 
    $grandTotal =0;
    $CopyFromItemid ='';
    //For Calculating total quantity 
    $grandTotal2=0; 

    $empid =$_SESSION['employee'];
    $empName =Employee::find_by_empid($empid);

    if (isset($_POST['search_term2'])) {
        $item_id = $_POST['search_term2'];
        $Manpowers=AllocationDetails::find_by_item_id2($item_id);

        if(!empty($Manpowers)){       
            foreach ($Manpowers as $Manpower) {  
                $grandTotal2+=$Manpower->total_quantity;
            }
        }else{
            $grandTotal2 = 0;
        }

            $totalAmount = ItemDetails::find_by_item_id($item_id);
            if(!empty($totalAmount)) {   $quantity = $totalAmount->quantity; }else{ $quantity = 0;}
        ?>
        <div class="col-lg-3" style="background:#fff;float:right;position:fixed;top:10%;right:0;z-index:1;outline:1px solid #ddd" id="budget">
        <table class="table " id="items">
                    <tr>
                        <td style="border-top:none;width:50%"><strong>Ordered Quantity</strong></td>
                        <td style="border-top:none"><input type="text" class="form-control" id="order" value="<?php  echo $quantity; ?>" readonly></td> 
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

      <?php  }

/**** List Of Item Details ****/
    if(isset($_POST['search_term'])){
    $item_id = $_POST['search_term'];
    $CopyFromItemid = $item_id;
    $Manpowers=AllocationDetails::find_by_item_id2($item_id);
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
                    <?php foreach ($Manpowers as $Manpower) {  ?>

                        <tr class="targetfields" >
                            <td>
                            <div class="form-group">
                               <label><?php $regionName =Region::find_by_region_id($Manpower->region_id); 
                                       $depotName = Depot::find_by_depot_code($regionName->depot_id); 
                                            if (isset($depotName->depot_name)) {
                                                echo $depotName->depot_name;
                                            }else{
                                                echo 'NA';
                                            }
                               ?></label> 
                               <input type="hidden" name="depot_id[]" value="<?php if(isset($depotName->depot_id)){
                                                                                     echo $depotName->depot_id; 
                                                                                        }else{
                                                                                           echo 'NA'; 
                                                                                        }?>">
                            </div>
                            </td>

                            <td>
                            <div class="form-group">
                               <label><?php echo $regionName->region_name; ?></label>
                               <input type="hidden" name="region_id[]" value="<?php echo $regionName->region_id; ?>">
                            </div>
                            </td>


                            <td><input class="form-control rate" value="<?php echo $Manpower->no_of_persons; $grandTotal+=$Manpower->no_of_persons;?>"  name="no_of_persons[]" type="text" readonly="readonly"></td>
                            <td><input class="form-control subtotal"  name="total[]" type="text" value="<?php echo $Manpower->total_quantity;  $grandTotal2+=$Manpower->total_quantity;?>" >
                                <input type="hidden" name="alloc_id[]" value="<?php echo $Manpower->alloc_id; ?>">
                            </td>
                       </tr>

                    <?php }?>
                        <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><?php echo $grandTotal; ?></td>
                        <td><input type="text" class="form-control" id="total" value="<?php echo $grandTotal2; ?>" readonly></td> 
                        </tr>
                        <tr>
                            <td colspan="5">
                            <button type="submit" class="btn btn-primary"  id="save" name="save" onClick="return validate();">Save</button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Copy To All</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
<?php }else{
    /**** Find Division For Current Employee ****/
        $division  = GPM::find_division1($empName->gpm_empid);
    /**** Find Manpower By Division ****/
        $Manpowers = Manpower::find_by_division($division);
    ?>
<script>
    alert('Allocation Details Not Found For This Item');
</script>
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
                    <?php foreach ($Manpowers as $Manpower) {  ?>

                        <tr class="targetfields" >
                            <td>
                            <div class="form-group">
                               <label><?php $regionName =Region::find_by_region_id($Manpower->region_id); 
                                       $depotName = Depot::find_by_depot_code($regionName->depot_id); 
                                            if (isset($depotName->depot_name)) {
                                                echo $depotName->depot_name;
                                            }else{
                                                echo 'NA';
                                            }
                               ?></label> 
                               <input type="hidden" name="depot_id[]" value="<?php if(isset($depotName->depot_id)){
                                                                                     echo $depotName->depot_id; 
                                                                                        }else{
                                                                                           echo 'NA'; 
                                                                                        }?>">
                            </div>
                            </td>

                            <td>
                            <div class="form-group">
                               <label><?php echo $regionName->region_name; ?></label>
                               <input type="hidden" name="region_id[]" value="<?php echo $regionName->region_id; ?>">
                            </div>
                            </td>


                            <td><input class="form-control rate" value="<?php echo $Manpower->no_of_persons; $grandTotal+=$Manpower->no_of_persons;?>"  name="no_of_persons[]" type="text" readonly="readonly"></td>
                            <td><input class="form-control subtotal"  name="total[]" type="text" value="" ></td>
                       </tr>

                    <?php }?>
                        <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><?php echo $grandTotal; ?></td>
                        <td><input type="text" class="form-control" id="total" value="" readonly></td> 
                        </tr>
                        <tr>
                            <td colspan="5">
                            <button type="submit" class="btn btn-primary"  id="save" name="save" onClick="return validate();">Save</button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Copy To All</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>

<?php }
}
?>
<script>
        jQuery(".subtotal").keyup(function(event) {
        var total = 0;
        jQuery("#items .targetfields").each(function() {
        var subtotal = parseInt(jQuery(this).find(".subtotal").val());
        
        if(!isNaN(subtotal))
             total+=subtotal;
        });
            var order = jQuery("#order").val();
            var balance = order - total;

            jQuery("#total").val(total.toFixed(2));
            jQuery("#total1").val(total.toFixed(2));
            jQuery("#balance").html(balance.toFixed(2));

    }); 

        jQuery(".rate").keyup(function(event) {
            var total = 0;
            var qty = parseInt(jQuery("#quantity").val())||0;
            jQuery("#items .targetfields").each(function() {
            var rate = parseInt(jQuery(this).find(".rate").val())||0;
            var subtotal = qty * rate;
            jQuery(this).find(".subtotal").val(subtotal);
                if(!isNaN(subtotal))
                    total+=subtotal;
            });
            var order = jQuery("#order").val();
            var balance = order - total;

            jQuery("#total").val(total.toFixed(2));
            jQuery("#total1").val(total.toFixed(2));
            jQuery("#balance").html(balance.toFixed(2));
        });
        
</script>