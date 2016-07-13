<?php
session_start();
if (isset($_SESSION['mm'])) {
    include_once './includes/initialize.php';
    $empid = $_SESSION['mm'];
    $empName = MM::find_by_empid($empid);
} elseif (isset($_SESSION['sm'])) {
    include_once './includes/initialize.php';
    $empid = $_SESSION['sm'];
    $empName = SM::find_by_empid($empid);
} else {
    header("Location:login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php
            if (isset($_SESSION['mm'])) {
                echo "MM";
            } elseif (isset($_SESSION['sm'])) {
                echo "SM";
            }
            ?>
        </title>
        <script src="js/jquery-1.11.0.js"></script>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/bootstrap-multiselect.css" rel="stylesheet">   
        <link href="css/main.css" rel="stylesheet">
        <link href="css/sb-admin.css" rel="stylesheet">
        <link href="css/plugins/morris.css" rel="stylesheet">
        <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="js/highcharts.js"></script>
    </head>
    <body>
        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><i class="fa fa-home fa-fw"></i>Home</a>
                </div>
                <!-- Top Menu Items -->
                <ul class="nav navbar-right top-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php
//$division = Division::find_by_div_id($empName->division);
                            echo " " . $empName->name;
                            ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">

                            <li>
                                <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav side-nav">

                        <?php if (isset($_SESSION['mm'])) { ?>

                            <li class="active">
                                <a href="MMindex.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                            </li>
                            <li>
                                <a href="MMlistApproval.php"><i class="fa fa-fw fa-list"></i> Approval</a>
                            </li>

                            <li>
                                <a href="MMAddBudget.php"><i class="fa fa-fw fa-dashboard"></i> Manage Budget</a>
                            </li>

                            <li>
                                <a href="GPMaddBrand2.php"><i class="fa fa-fw fa-dashboard"></i> Allocate Brand</a>
                            </li>
                            <li>
                                <a href="MMbrandList.php"><i class="fa fa-fw fa-dashboard"></i> Manage Brand</a>
                            </li>
                            <li>
                                <a href="MMlistPmt.php"><i class="fa fa-fw fa-dashboard"></i> PMT</a>
                            </li>
                            <li>
                                <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-files-o"></i> Reports <i class="fa fa-fw fa-caret-down"></i></a>
                                <ul id="demo" class="collapse">
                                    <li>
                                        <a href="MMbrandwiseExpense.php">Brandwise Expense</a>
                                    </li>
                                    <li>
                                        <a href="MMviewItemWiseExpense.php">Categorywise Expense</a>
                                    </li>
                                    <li>
                                        <a href="MMdeliveryReport.php">Delivery Report</a>
                                    </li>
                                    <li>
                                        <a href="MMpmtwiseExpense.php">PMTwise Expense</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="MMlistGPM.php"><i class="fa fa-fw fa-dashboard"></i> Manage GPM</a>
                            </li>


                        <?php } elseif (isset($_SESSION['sm'])) { ?>

                            <li >
                                <a href="SMindex.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>

            <div id="page-wrapper">
                <div class="container-fluid">