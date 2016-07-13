<?php session_start(); if(!isset($_SESSION['CWH'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");  
 $grandTotal = 0;
 $pageTitle = 'Print Allocation';

    if (isset($_GET['item_id'])) {
    	$item_id =$_GET['item_id'];
    	$Allocations = AllocationDetails::find_by_item_id2($item_id);
        $keyNo =PrDetails::find_by_item_id($item_id);
        $ItemDescription =ItemDetails::find_by_item_id($item_id);
        $ReceivedQuantity = GRN::find_received_quantity($item_id);

    }else{
    	$Allocations =array();
    }

require_once(dirname(__FILE__)."/layouts/CWHlayouts/header.php");?>
<div class="row" id="breadcrumb">
    <div class="col-lg-12">
        <h1 class="page-header">Print Allocation</h1>
            <ol class="breadcrumb">
                <li class="active">
                        <i class="fa fa-hand-o-down"></i>Print Allocation
                </li>
            </ol>
    </div>
</div>
<div class="row" style="margin-bottom:2em;" id="button">
    <div class="col-lg-1 pull-center">
        <button type="submit" class="btn btn-default" id="printpagebutton" onclick="printpage()"><i class="fa fa-print"></i> Print </button>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
            <div id="pageHeader">
                <h2>Item Details</h2>
                <h4>Key No : <?php if(!empty($keyNo)){ echo $keyNo->key_no; }else{ echo '-'; } ?></h4>
                <h4>Description : <?php echo $ItemDescription->description; ?></h4>
                <h4>Received Quantity : <?php if(!empty($ReceivedQuantity)){
                                            echo $ReceivedQuantity;
                }else{
                    echo 0;
                } ?>
                </h4>
            </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div class="table-responsive" style="font-size:11px">
        	<table class="table table-bordered ">
            <thead>
                <tr>
                	<th>Depot</th>
                    <th>Region</th>
                    <th>Total Quantity</th>
                </tr>
            </thead>
            <tbody>
            <?php  if(!empty($Allocations)){ 
            		foreach ($Allocations as $Allocation) { ?>
            	<tr class=" page-break">
            		<td><?php  $DepotName = Depot::find_by_depot_id($Allocation->depot_id);
            			if(!empty($DepotName)){
            				echo $DepotName->depot_name;
            			}else{
            				echo 'NA';
            			}

            		?></td>
            		<td><?php $RegionName = Region::find_by_region_id($Allocation->region_id); 
            				echo $RegionName->region_name;
            		?>
            		</td>

            		<td><?php echo $Allocation->total_quantity; $grandTotal+=$Allocation->total_quantity; ?></td>
            	</tr>

       		<?php  } ?>
                <tr>
                    <td colspan ="2"><strong> Total</strong></td>
                    <td><?php echo $grandTotal;?></td>
                </tr>
                <tr>
                    <td colspan ="2"><strong>Balance At CWH</strong></td>
                    <td><?php if(!empty($ReceivedQuantity)){echo $ReceivedQuantity->quantity_received - $grandTotal;}else{  }?></td>
                </tr>
            <?php }else{
                    echo '<tr><td colspan="3"><h3>Allocation Details Not Found.</h3></td></tr>';
            }?>
        </tbody>
       	</table>
        </div>
    </div>
</div>
<?php require_once(dirname(__FILE__)."/layouts/footer.php");?>
<script type="text/javascript">
    function printpage() {
        $("#breadcrumb").hide();
        $("#button").hide();
        $("#ApprovalSheet").css("margin-top","0px");
        window.print();
        $("#breadcrumb").show();
        $("#button").show();
        $("#ApprovalSheet").css("margin-top","50px");
    }
</script>

<?php require_once(dirname(__FILE__)."/layouts/CWHlayouts/footer.php");?>