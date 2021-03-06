<?php require_once(dirname(__FILE__)."/MMheader.php");
    $Employees = Employee::find_by_mm($empid);
    $finalCategory = array('Print','Gift','E-Input','Publisher','Promo Services','Miscellaneous');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Categorywise Expense
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i>  Categorywise Expense
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
                            <th>Category</th>
	                    <th>Quarter 1</th>
	                    <th>Quarter 2</th>
	                    <th>Quarter 3</th>
	                    <th>Quarter 4</th>
	                    <th>Total</th>
	                </tr>
                </thead>
                <tbody>
               	<?php if(!empty($finalCategory)){ 
               			foreach ($finalCategory as $Category) {

               		?>
                	<tr>
                		<td><?php echo $Category; ?></td>
                		<td><?php $quarter='BETWEEN 4 AND 6'; 
                		$Qtr1Amount = ItemDetails::MMfind_quarterwise_expense($empid,$Category,$quarter);
                		echo $Qtr1Amount;
                		?></td>

                		<td><?php $quarter='BETWEEN 7 AND 9'; 
                		$Qtr2Amount = ItemDetails::MMfind_quarterwise_expense($empid,$Category,$quarter);
                		echo $Qtr2Amount;
                		?></td>

                		<td><?php $quarter='BETWEEN 10 AND 12'; 
                		$Qtr3Amount = ItemDetails::MMfind_quarterwise_expense($empid,$Category,$quarter);
                		echo $Qtr3Amount;
                		?></td>

                		<td><?php $quarter='BETWEEN 1 AND 3'; 
                		$Qtr4Amount = ItemDetails::MMfind_quarterwise_expense($empid,$Category,$quarter);
                		echo $Qtr4Amount;
                		?></td>

                		<td><?php  echo $Qtr1Amount + $Qtr2Amount + $Qtr3Amount +$Qtr4Amount; ?></td>
                	</tr>
               	<?php }               				
               	}?>

               	</tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once(dirname(__FILE__)."/MMfooter.php");?>