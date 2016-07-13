<?php session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $empid=$_SESSION['PRE'];
 $empName =Employee::find_by_empid($empid);

     if(isset($_GET['apr_id'])){
       	$apr_id =  base64_decode($_GET['apr_id']);
        $SecurityCheck = Approval::SecurityCheck2($apr_id);
        if (empty($SecurityCheck)) {
            redirect_to("AccessDenied.php");
        }

        $Approval = Approval::find_by_apr_id($apr_id);
       // $PODate = PoDetails::find_po_date($apr_id);
        //$GRnDate = GRN::find_grn_date($apr_id);
        $PrDate =PrDetails::find_pr_date($apr_id);
    }else{
    	redirect_to("PRElistApproval.php");
    }
 require_once(dirname(__FILE__)."/layouts/PRElayouts/header.php");?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Show Details
        </h1>
        <ol class="breadcrumb">
	        <li class="active">
	            <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
	        </li>
	        <li class="active">
	            <i class="fa fa-desktop"></i> Show Details
		    </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 ">
    	<div class="jumbotron">
            <?php foreach ($Approval as $value) { ?>

              <p><strong>Approval Id :</strong> <?php echo $value->apr_id; ?></p>
              
              <p><strong>Requisition by :</strong> <?php $PMTname = Employee::find_by_empid($value->empid);
                                    echo $PMTname ->name;  ?></p>
              
              <p><strong>Requisition Date :</strong> <?php echo date('d-m-Y', strtotime($value->date)); ?></p>
              
              <p><strong>Division :</strong> <?php  	
						 			$empName = Employee::find_by_empid($value->empid);
									$division  = GPM::find_division1($empName->gpm_empid);
									$divisionName = Division::find_by_div_id($division); 
									echo $divisionName->div_name;
									?></p>

              <?php  }?>

		</div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 ">
    	<div class="col-lg-2 col-sm-3">
		    <div class="round">	
		    	<br/>
		    	Approval<br/>
		    	<?php foreach ($Approval as $value) {
		    		echo date('d-m-Y', strtotime($value->date));
		    		
		    	}?>
		    </div>
		</div>
		    <div style="float:left;padding-top:35px" class="col-lg-1">
		        <span class="glyphicon glyphicon-arrow-right">
		    </div>

		<div class="col-lg-2 col-sm-3">
		    <div class="round">	
		    	<br/>
		    	PR<br/>
		    	<?php echo date('d-m-Y', strtotime($PrDate)); ?>
		    	
		    </div>
		</div>
		    <div style="float:left;padding-top:35px" class="col-lg-1">
		    <span class="glyphicon glyphicon-arrow-right">
		    </div>

		<div class="col-lg-2 col-sm-3">
		    <div class="round ">	
		    	<br/>
		    	  PO<br/>
		    	<?php //echo date('d-m-Y', strtotime($PODate)); ?>
		    	
		    </div>
		</div>    
		    <div style="float:left;padding-top:35px" class="col-lg-1">
		    <span class="glyphicon glyphicon-arrow-right">
		    </div>
		<div class="col-lg-2 col-sm-3">
		    <div class="round ">	
		    	<br/>
		    	GRN<br/>
		    	<?php // echo date('d-m-Y', strtotime($GRnDate)); ?>
		    </div>
	    </div>
	</div>
</div>  
<div class="row" style="margin-top:1em;margin-bottom:1em">
	<div class="col-lg-4"></div>
	<div class="col-lg-4">
		<button type="button" class="btn btn-default btn-block"><?php foreach ($Approval as $value) {
			$date1 = new DateTime($value->date);
			$date2 = new DateTime($PrDate);
			$interval = $date1->diff($date2);
			}?>Total Time To Deliver : <?php echo $interval->m." months, ".$interval->d." days ";?></button>
	</div>
	<div class="col-lg-4"></div>
</div>  	
<?php require_once(dirname(__FILE__)."/layouts/PRElayouts/footer.php");?>