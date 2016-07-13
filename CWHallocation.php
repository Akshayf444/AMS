<?php session_start(); if(!isset($_SESSION['CWH'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");

    $grandTotal = 0; 
    if(isset($_GET['apr_id'])){
        /**** Collect Approval Id ****/
        $apr_id =$_GET['apr_id'];

        /**** Collect List Of Items From Approvals ****/
        $Items2 = ItemDetails::find_by_apr_id($apr_id);
        $Approval = Approval::find_by_apr_id($apr_id);
        $equalItems =ItemDetails::find_equal_items($apr_id);

        $Empid = Approval::find_by_apr_id2($apr_id);
 		$empName = Employee::find_by_empid($Empid->empid);
		$division  = GPM::find_division1($empName->gpm_empid);
		$divisionName = Division::find_by_div_id($division);
		$Manpowers = Manpower::find_by_division($divisionName->div_id);

    }else{
        $Items = array();
        $Approval = array();
        $equalItems =array();
    }

 require_once(dirname(__FILE__)."/layouts/CWHlayouts/header.php");?>
<div class="row" id="Budget">
    <div class="col-lg-3" style="background:#fff;float:right;position:fixed;top:10%;right:0;z-index:1;outline:1px solid #ddd" id="budget">
        <table class="table " id="items">
                    <tr>
                        <td style="border-top:none;width:50%"><strong>Ordered Quantity</strong></td>
                        <td style="border-top:none"><input type="text" class="form-control" id="order" value="0" readonly></td> 
                    </tr>
                    <tr>
                        <td style="border-top:none;width:50%"><strong>Total</strong></td>
                        <td style="border-top:none"><input type="text" class="form-control" id="total1" value="" readonly></td>
                    </tr>
                    <tr>
                        <td style="width:50%"><strong>Balance At CWH</strong></td>
                        <td><span id="balance"></span></td> 
                    </tr>
                </table>
    </div>
</div>
    <div class="row">
        <div class="col-lg-12">
        	<h1 class="page-header">
                <?php //Approval title 
                 foreach ($Approval as $value) {?>
                Allocation for <?php echo $value->title; ?> 
                <?php }?>
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li>
                    <i class="fa fa-dashboard"></i> Allocation
                </li>
            </ol>
         </div>
    </div>
<div class="row">
<div class="col-lg-12 ">
	<div class="table-responsive ">
        <table class="table" style="width:40%">
            <tr>
                <th style="border-top:none"> <label  class=" control-label">Select Item</label></th>
                    <td style="border-top:none">
                            <select class="form-control" name="item_id" onchange="Search()" id="item_value">
                            <option value="">Select Item</option>
                            <?php foreach ($Items2 as $Item1) {?>
                            <option value="<?php echo trim($Item1->item_id); ?>"><?php echo trim($Item1->description); ?></option>
                            <?php }?>
                            </select>
                    </td>
                </tr>
                 <tr>
                    <th style="border-top:none"><label  class=" control-label">Quantity/Person</label></th>
                    <td style="border-top:none">
                        <input type="text" class="form-control" id="quantity" name="qty_per_person" >
                    </td>
                </tr>     
                </table>
            </div>
            <div class="form1">
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


                            <td><?php echo $Manpower->no_of_persons; $grandTotal+=$Manpower->no_of_persons;?></td>
                            <td><input class="form-control subtotal"  name="total[]" type="text" value="0" ></td>
                       </tr>

                    <?php }?>
                        <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><?php echo $grandTotal; $grandTotal = 0; ?></td>
                        <td><input type="text" class="form-control" id="total" value="" readonly></td> 
                        </tr>

                    </tbody>
                </table>
                </div>
            </div> 
            <div class="col-lg-2" id="loader" style="display:none"><img src="images/loader.gif"></div>
        </div>
    </div>
</div>
<script>
function Search() {
        $("#save").show();
        $("#loader").show();
        var search_term=$("#item_value").val();
        var search_term2 = $("#item_value").val();
        $.post('CWHgetTotalAmount.php',{search_term:search_term},function(data){
            $("#quantity").val(data);
        });

        $.post('CWHgetItemDetails.php',{search_term:search_term},function(data){
            var data1 = data;
            if(data1 == null || data1 == ""){
                alert('Item Details Not Found');
            }else{
                $(".form1").html(data1);
                $.post('CWHgetTotalAmount.php',{search_term2:search_term2},function(data){
                    $("#displayHeading").html(data);
                    //alert(data);
                });
            }
        });
        $("#loader").hide();

    } 

</script>
<?php require_once(dirname(__FILE__)."/layouts/CWHlayouts/footer.php");?>