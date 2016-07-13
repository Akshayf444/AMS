<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['employee'];
    $empName =Employee::find_by_empid($empid);
    $division  = GPM::find_division1($empName->gpm_empid);
    $brands =Brand::find_by_division($division);

//import header file From layouts
require_once(dirname(__FILE__)."/layouts/header.php");
?>
<div class="row">
        <div class="col-lg-12">
            
            <h1 class="page-header">Expense</h1>
            
                <ol class="breadcrumb">
                    <li class="active">
                        <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-hand-o-down"></i> Expense
                    </li>
               </ol>
        </div>
</div>
<div class="row">
	
</div>
<?php require_once(dirname(__FILE__)."/layouts/footer.php");?>