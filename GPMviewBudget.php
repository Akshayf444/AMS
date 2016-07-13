<?php session_start(); if(!isset($_SESSION['gpm'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['gpm'];
    $empName = GPM::find_by_empid($empid);
    $Brands =Brand::find_by_division($empName->division);

    $qtr1Total=0;
    $qtr2Total=0;
    $qtr3Total=0;
    $qtr4Total=0;
    $finalQtr1Total = 0;
    $finalQtr2Total = 0;
    $finalQtr3Total = 0;
    $finalQtr4Total = 0;
require_once(dirname(__FILE__)."/layouts/gpmlayouts/header.php");?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                View Budget
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li class="active">
                    <i class="fa fa-desktop"></i> View Budget
                </li>
           </ol>
    	</div>
    </div>
<div id="errors" class="row">
  	<div class="col-lg-12">
		<?php if( isset($_SESSION['Error'])){
	        echo $_SESSION['Error'];
		    unset($_SESSION['Error']);
		}?>
	</div>
</div>

<div class="row">
    <div class="col-lg-10">
		<div class="table-responsive" style="margin-top:2em">
	        <table class="table table-bordered table-hover table-striped" id="items">
	        	<tr>
	        		<th>Brand Name</th>
	        		<th>Quarter 1</th>
	        		<th>Quarter 2</th>
	        		<th>Quarter 3</th>
	        		<th>Quarter 4</th>
                    <th>Total</th>
	        	</tr>
	        	<?php foreach ($Brands as $brand) {  
                        $value = BrandBudget::find_by_brand_id($brand->brand_id);
                        if(!empty($value)){
                    ?>
	        	<tr>
	        		<td><?php echo $value->brand_name; ?></td>
	        		<td><?php echo $value->qtr1; $qtr1Total +=$value->qtr1; $finalQtr1Total +=$value->qtr1; ?></td>
	        		<td><?php echo $value->qtr2; $qtr2Total +=$value->qtr2; $finalQtr2Total +=$value->qtr2;?></td>
	        		<td><?php echo $value->qtr3; $qtr3Total +=$value->qtr3; $finalQtr3Total +=$value->qtr3;?></td>
	        		<td><?php echo $value->qtr4; $qtr4Total +=$value->qtr4; $finalQtr4Total +=$value->qtr4;?></td>

                    <td><?php echo $qtr1Total + $qtr2Total  + $qtr3Total + $qtr4Total; $qtr4Total = 0; $qtr1Total = 0; $qtr2Total = 0; $qtr3Total = 0;
                    ?></td>
	        	</tr>
	        	<?php	} }?>
	        	<tr>
                    <td style="border-top:2px solid #ddd;"><strong>Total</strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $finalQtr1Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $finalQtr2Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $finalQtr3Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $finalQtr4Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $finalQtr1Total + $finalQtr2Total  + $finalQtr3Total + $finalQtr4Total; ?></strong></td>
	        </table>
    	</div>
    </div>
</div>

<?php require_once(dirname(__FILE__)."/layouts/gpmlayouts/footer.php");?>