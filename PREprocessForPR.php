<?php session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['PRE'];
    $empName =Employee::find_by_empid($empid);
    //Array For Collectin Form Errors
    $ItemCount = 0;
    $postCount=null;
    $EncryptUrl = new Encryption();
    $Errors=array();
    if(isset($_GET['apr_id'])){
        $apr_id =  $EncryptUrl->decode($_GET['apr_id']);
        $SecurityCheck = Approval::SecurityCheck2($apr_id);
        
        if (empty($SecurityCheck)) {
            redirect_to("AccessDenied.php");
        }
        $Items = ItemDetails::find_by_apr_id($apr_id);
        $ItemCount =count($Items);

        $Approval = Approval::find_by_apr_id($apr_id);
    }else{
        redirect_to("PREprocessForPR.php");
    }

    if (isset($_POST['submit'])) {
        $length = count($_POST['item_id']);
        for ($i=0; $i < $length; $i++) { 
            $PrDetails = new PrDetails();
            $PrDetails->pr_id= $PrDetails->autoGenerate_id();
        
            if (!empty($_POST['key_no'][$i])) {
                $PrDetails->key_no = trim($_POST['key_no'][$i]);
                ${'keyno'.$i} = $_POST['key_no'][$i];
            }else{
                array_push($Errors, "Please Enter Key No.");
            }

            $PrDetails->pr_date = strftime("%Y-%m-%d ", time());
            $PrDetails->item_id =($_POST['item_id'][$i]);

            foreach ($Approval as $value) {
                $PrDetails->apr_id = $value->apr_id;
            }

            if (empty($Errors)) {
                $PrDetails->create();

            }else{
                $finalErrorList = array_unique($Errors);
            }
        }

        if (empty($Errors)) {
            /**** Check Whether All Items Have key no ****/
            $PRdetails=PrDetails::proceed(base64_decode($_POST['new_apr_id'])); 
            if(empty($Errors) && $PRdetails == true){
                $column_name="process_for_po";
                $status = "processed" ;
                $UpdatePRStatus = new Approval();
                foreach ($Approval as $value) {
                    $UpdatePRStatus->update_PRE_status($value->apr_id,$status,$column_name);
                }
                redirect_to("PRElistApproval.php");
            }else{

            }
        }else{
            array_push($Errors, "Error Occurred.");
        }
    }

require_once(dirname(__FILE__)."/layouts/PRElayouts/header.php");?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Process For PR
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> Process For PR
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="jumbotron">
            <?php foreach ($Approval as $value) { ?>

              <p><strong>Approval Id :</strong> <?php echo $value->apr_id; ?></p>
              
              <p><strong>Requisition by :</strong> <?php $PMTname = Employee::find_by_empid($value->empid);
                                    echo $PMTname ->name;  ?></p>
              
              <p><strong>Requisition Date :</strong> <?php echo $value->date; ?></p>
              
              <p><strong>Division :</strong> <?php   $empName = Employee::find_by_empid($value->empid);
                                    $division  = GPM::find_division1($empName->gpm_empid);
                                    $divisionName = Division::find_by_div_id($division); 
                                    echo $divisionName->div_name; ?></p>

              <?php  }?>
        </div>    
    </div>
</div>
<div class="row">
    <ul id="errors">
    <?php  if (!empty($Errors)) {
        foreach ($finalErrorList as $value) {
            echo "<li style='color:red;'>".$value."</li>"; 
        } 
    }?>
    </ul>
</div>
<!--
<div class="row">
 	<div class="col-lg-12">
    	<div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                        <tr>
                            <th rowspan="2">Enclosed If Any</th>
                            <th>Dummy</th>
                            <th>Art Work</th>
                            <th>Allocation</th>
                        </tr>
                </thead>
                 <tbody>
                        <tr>
                        	<td></td>
                            <td><input type="checkbox" value=""></td>
                            <td><input type="checkbox" value=""></td>
                            <td>Yes</td>
                       	</tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
-->
<div class="row">
 	<div class="col-lg-12">
        <form action="PREprocessForPR.php?apr_id=<?php echo $_GET['apr_id']; ?>" method="post">
    	<div class="table-responsive">
    		<table class="table table-bordered table-hover table-striped" id="items">
                <thead>
                    <tr>
                        <th style="width:20%">Brand/Division</th>
                        <th style="width:12%">Item Category</th>
                        <th>Description Of An Item</th>
                        <th style="width:10%">Quantity</th>
                        <th style="width:7%">Value/Item</th>
                        <th style="width:7%">Amount</th>
                        <th style="width:15%">Key No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $postCount =0; foreach ($Items as $Item) { ?>
                        <tr class="targetfields">
                            <td><?php $dropdown = ItemDetails::brandDropdown($Item->brand_id, 0);
                                    echo $dropdown;
                                    ?>
                            </td>
                            <td><?php echo $Item->item_category; ?></td>
                            <td><?php echo $Item->description; ?></td>
                            <td><?php echo $Item->quantity; ?></td>
                            <td><?php echo $Item->value; ?></td>
                            <td><?php echo $Item->amount; ?></td>
                            <td><input class="form-control"  type="text" name="key_no[]" class="keyno" 
                                value="<?php if(isset(${'keyno'.$postCount})){
                                                    echo ${'keyno'.$postCount};
                                            }?>" required>


                                <input value="<?php echo $Item->item_id; ?>"  type="hidden" name="item_id[]">
                            </td>
                            
                       </tr>
                    <?php  $postCount ++; }?>
                    <tr>
	                	<td colspan="7"><button type="submit" class="btn btn-success" name="submit" onClick ="return validate();">Save</button></td>
                        <input type="hidden" name="new_apr_id" value="<?php echo $_GET['apr_id']; ?>">
                    </tr>
                    </tbody>
                </table>
    	   </div>
        </form> <!--End Of Form-->
    </div>
</div>

<script src="js/bootstrapDropdown.js"></script>

<script>
jQuery(function() {
    jQuery("#items ").delegate('.keyno','keyup',function() {
        alert("dfadf");
        jQuery("#items .targetfields").each(function() {
            var keyno = jQuery(this).find(".keyno").val();

            if (keyno.val().length == 0) {
                $("#errors").empty();
                $("#errors").html("<li>Please Enter Key No.</li>");
            }
        });
    });
});
</script>
<?php require_once(dirname(__FILE__)."/layouts/PRElayouts/footer.php");?>