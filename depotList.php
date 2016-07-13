<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $empid=$_SESSION['employee'];
 $empName =Employee::find_by_empid($empid);

    /**** Find Division For Current Employee According To His GPM ***/
    $division  = GPM::find_division1($empName->gpm_empid);
    $Manpowers = Manpower::find_by_division($division);

    $depotNames = Depot::find_all(); 
     require_once(dirname(__FILE__)."/layouts/header.php"); ?>
 <div class="row" id="breadcrumb">
        <div class="col-lg-12">
            
            <h1 class="page-header">Depot List</h1>
            
                <ol class="breadcrumb">
                    <li class="active">
                        <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-hand-o-down"></i> Depot List
                    </li>
               </ol>
	</div>
</div>

<div class="row" style="margin-bottom:1em;" id="button">
    <div class="col-lg-1 pull-center">
        <button type="submit" class="btn btn-default" id="printpagebutton" onclick="printpage()"><i class="fa fa-print"></i> Print All</button>
    </div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
	<?php foreach ($depotNames  as $depotName ) {  ?>
		<div class="col-lg-3 " >
			<button type="button" class="button1" style="width:100%" >
			<div class="panel panel-red">
			<div class="panel-heading">
	                                <h3 class="panel-title"> <?php if (isset($depotName->depot_code)){
	                                	echo $depotName->depot_code;
	                                }?></h3>

	                           </div>
	                           <div class="panel-body" style="height:75px">
			<label><?php 
                                            if (isset($depotName->depot_name)) {
                                                echo $depotName->depot_name;
                                            }
                               ?></label>
                               	</div>
                               </div>
                                </button>
		</div>
	<?php }//End of for loop ?>
		</div>
	</div>
</div>
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
$(document).ready(function(){
	$(".button1").click(function(){
	        //var myClass = $(this).attr("class");
	        var myClass = $(this).attr("class");
	        $('.'+ myClass).not(this).hide();  

	        $("#breadcrumb").hide();
	        $("#button").hide();
	        $("#ApprovalSheet").css("margin-top","0px");
	        window.print();

	        $("#breadcrumb").show();
	        $("#button").show();
	        $("#ApprovalSheet").css("margin-top","50px");
	        $(".button1").each(function(){
                            $(this).show();
                    });
	});
});
</script>
<?php require_once(dirname(__FILE__)."/layouts/footer.php");?>