<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CWH</title>
    <script src="js/jquery-1.11.0.js"></script>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-multiselect.css" rel="stylesheet">   
    <link href="css/main.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="js/highcharts.js"></script>
    <script src="js/IEbuttonPatch.js"></script>
    <style>
        @media print{
            thead {
                display: table-header-group;
                vertical-align: middle;
                border-color: inherit;
            }
        }

    </style>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5.js"></script>
      <script src="js/respond.js"></script>
    <![endif]-->
</head>

<body id="ApprovalSheet">

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
                       $empid=$_SESSION['CWH'];
                       $empName = Employee::find_by_empid($empid);
                        echo " ".$empName->name?> <b class="caret"></b></a>
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
                    <!--
                    <li class="active">
                        <a href="CWHindex.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="CWHlistApproval.php"><i class="fa fa-fw fa-list"></i> Approval</a>
                    </li>
                    -->
                    <li class="<?php if ( isset($pageTitle) && $pageTitle == 'Search Allocation') {
                        echo 'active';
                    }?>">
                        <a href="CWHsearchAllocation.php"><i class="fa fa-fw fa-search"></i> Allocation</a>
                    </li>

                    <li class="<?php if (isset($pageTitle) && $pageTitle == 'Search GRN') {
                        echo 'active';
                    }?>">
                        <a href="CWHsearchGRN.php"><i class="fa fa-fw fa-search"></i> GRN </a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">