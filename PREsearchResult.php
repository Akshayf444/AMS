<?php session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $empid=$_SESSION['PRE'];
 $empName =Employee::find_by_empid($empid);
$encryption = new Encryption();
if(isset($_POST['apr_id'])) {
	$Approvals=Approval::find_by_apr_id($_POST['apr_id']);
        $apr_id = $encryption->encode($_POST['apr_id']);
}else{
	redirect_to("PRElistApproval.php");
}

 require_once(dirname(__FILE__)."/layouts/PRElayouts/header.php");?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Search Result
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> Search Result
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
        	<?php if(!empty($Approvals)){?>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Approval Id</th>
                        <th>Title Of Approval</th>
                        <th>Brand</th>
                        <th>Division</th>
                        <th>Requision By</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    <?php    foreach ($Approvals as $Approval) { ?>
                        <tr>

                            <td><?php echo $Approval->apr_id; ?></td>
                            <td><?php echo $Approval->title; ?></td>
                            <td><?php $dropdown = ItemDetails::brandDropdown($Approval->apr_id, 1);
                                    echo $dropdown;
                                    ?>
                            </td>

                            <td><?php $Empid = Approval::find_by_apr_id2($Approval->apr_id);
                                $empName = Employee::find_by_empid($Empid->empid);
                                $division  = GPM::find_division1($empName->gpm_empid);
                                $divisionName = Division::find_by_div_id($division);  
                                echo $divisionName->div_name; ?>
                            </td>

                            <td><?php $PMT=Employee::find_by_empid($Approval->empid); echo $PMT->name; ?></td>
                            <td><?php echo $Approval->date; ?></td>
                            
                            <td><?php $status =Approval::approvalStatus($Approval->apr_id); 
                                    $finalStatus =join(",",$status);
                                    echo $finalStatus;
                            ?></td>

                            <?php $POdetails=PoDetails::proceed($Approval->apr_id); 
                            if ($Approval->receive == "received") { ?>
                            
                            <td>
                                <a href="PREpodetails.php?apr_id=<?php echo $apr_id; ?>">
                                    <button type="button" class="btn btn-xs btn-info PREapproval" style="width:100px">PO Details
                                    </button>
                                </a>
                            </td>
                                
                            <?php }elseif ($POdetails == true || $Approval->process_for_po == "processed" ){ ?>
                            
                            <td>
                                <a href="PREpodetails.php?apr_id=<?php echo $apr_id; ?>">
                                <button type="button" class="btn btn-xs btn-info PREapproval dialog"  style="width:100px" >PO Details
                                </button></a>
                            </td>

                            <?php } else{ ?>
                            
                            <td>

                                <a href="PREprocessForPR.php?apr_id=<?php echo $apr_id; ?>">
                                    <button type="button" class="btn btn-xs btn-success PREapproval" style="width:100px">Process For PR
                                    </button>
                                </a>
                            </td>

                            <?php }/****** End Of If *****/ ?>
                        </tr>

                    <?php }/***** End Of loop ***/  
                	}else{
                		echo '<b>Approval  Details Not Found</b>';
                	}?>
                    </tbody>
                    </table>

                </div>       
            </div>
        </div>
    </div> 
 <script>
    $(function(){
    $(".dropdown").hover(            
            function() {
                $('.dropdown-menu', this).stop( true, true ).fadeIn("fast");
                $(this).toggleClass('open');
                $('b', this).toggleClass("caret caret-up");                
            },
            function() {
                $('.dropdown-menu', this).stop( true, true ).fadeOut("fast");
                $(this).toggleClass('open');
                $('b', this).toggleClass("caret caret-up");                
            });
    });
</script>

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