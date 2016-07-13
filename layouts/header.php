<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>PMT</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/bootstrap-multiselect.css" rel="stylesheet">   
        <link href="css/main.css" rel="stylesheet">
        <link href="css/sb-admin.css" rel="stylesheet">
        <link href="css/plugins/morris.css" rel="stylesheet">
        <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="js/jquery-1.11.0.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrap-multiselect.js"></script>
        <script src="js/highcharts.js"></script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="js/html5.js"></script>
          <script src="js/respond.js"></script>
        <![endif]-->




    </head>
    <body d="ApprovalSheet">
        <div id="wrapper">
            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="header">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header" >
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><i class="fa fa-home fa-fw"></i>Home</a>
                </div>
                <!-- Top Menu Items -->
                <ul class="nav navbar-right top-nav">
                    <li class="dropdown">
                        <a href="logout.php" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php
                            $empid = $_SESSION['employee'];
                            $empName = Employee::find_by_empid($empid);
                            if ($empName->complete_profile == 0) {

                                echo "<script>window.location = 'GPMcompleteProfile.php';</script> ";
                            }
                            echo " " . $empName->name
                            ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="changePassword.php" ><i class="fa fa-fw fa-gear"></i> Change Password</a>
                            </li>
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
                            <a href="listApproval.php"><i class="fa fa-fw fa-dashboard"></i> Approval</a>
                        </li>
                        <li>
                            <a href="listAllocation.php"><i class="fa fa-fw fa-table"></i> Allocation</a>
                        </li>

                        <li>
                            <a href="brandWiseBudgetDetails.php"><i class="fa fa-fw fa-suitcase"></i> Budget </a>
                        </li>

                        <li>
                            <a href="depotList.php"><i class="fa fa-fw fa-bar-chart-o"></i> Depot List</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-files-o"></i> Reports <i class="fa fa-fw fa-caret-down"></i></a>
                            <ul id="demo" class="collapse">
                                <li>
                                    <a href="brandwiseExpense.php">Brandwise Expense</a>
                                </li>
                                <li>
                                    <a href="viewItemWiseExpense.php">Categorywise Expense</a>
                                </li>
                                <li>
                                    <a href="deliveryReport.php">Delivery Report</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="allocationStatusList.php"><i class="fa fa-fw fa-table"></i> Allocation Status</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-table"></i> Expense</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-table"></i> Agency</a>
                        </li>


                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>

            <div id="page-wrapper">
                <div class="container-fluid">