<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:login.php");
} else {
    include_once '../includes/initialize.php';
    $empid = $_SESSION['admin'];
    $empName = Admin::find_by_emailid($empid);
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

        <title>Admin</title>
        <script src="../js/jquery-1.11.0.js" type="text/javascript"></script>
        <link href="../css/bootstrap.css" rel="stylesheet">
        <link href="../css/bootstrap-multiselect.css" rel="stylesheet">   
        <link href="../css/main.css" rel="stylesheet">
        <link href="../css/sb-admin.css" rel="stylesheet">
        <link href="../css/plugins/morris.css" rel="stylesheet">
        <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="../js/highcharts.js"></script>
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
                            echo " " . $empName->name ;
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
                        <li class="active">
                            <a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                        </li>


                        <li>
                            <a href="assignDivision.php"><i class="fa fa-fw fa-dashboard"></i> Assign Division</a>
                        </li>
                        <li>
                            <a href="addDivision.php"><i class="fa fa-fw fa-dashboard"></i> Add Division</a>
                        </li>
                        <li>
                            <a href="AddMM.php"><i class="fa fa-fw fa-dashboard"></i> Add Marketing Manager</a>
                        </li>
                            
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>

            <div id="page-wrapper">

                <div class="container-fluid">