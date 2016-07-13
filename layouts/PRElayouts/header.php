<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PRE</title>

    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <script src="js/jquery-1.11.0.js"></script>
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
   
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
                <a class="navbar-brand" href="PREindex.php"><i class="fa fa-home fa-fw"></i>Home</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="logout.php" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php 
                     $empid=$_SESSION['PRE'];
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
                    <li class="active">
                        <a href="PREindex.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="PRElistApproval.php"><i class="fa fa-fw fa-dashboard"></i> Approval</a>
                    </li>
                    <!-- <li>
                        <a href="#"><i class="fa fa-fw fa-table"></i> Allocation</a>
                    </li>
                     <li>
                        <a href="#"><i class="fa fa-fw fa-bar-chart-o"></i> Status</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-fw fa-table"></i> Expense</a>
                    </li> -->
                    <li>
                        <a href="PREsearchGRN.php"><i class="fa fa-fw fa-table"></i> GRN</a>
                    </li>
                    <li>
                        <a href="PREupload.php"><i class="fa fa-fw fa-upload"></i> Upload PO Excel </a>
                    </li>
                    <li>
                        <a href="PREuploadPrNo.php"><i class="fa fa-fw fa-upload"></i> Upload PR Excel </a>
                    </li>
                    <li>
                        <a href="PREdeleteKey.php"><i class="fa fa-fw fa-upload"></i> Delete Key no </a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">