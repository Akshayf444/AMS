<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>GPM</title>
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
                    <a class="navbar-brand" href="gpmindex.php"><i class="fa fa-home fa-fw"></i>Home</a>
                </div>
                <!-- Top Menu Items -->
                <ul class="nav navbar-right top-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php
                            $empid = $_SESSION['gpm'];
                            $empName = GPM::find_by_empid($empid);
                            $division = Division::find_by_div_id($empName->division);
                            if ($empName->complete_profile == 0) {
    
                                echo "<script>window.location = 'GPMcompleteProfile.php';</script> ";
}
                            echo " " . $empName->name." (".$division->div_name .")";
                            ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="GPMchangePassword.php" ><i class="fa fa-fw fa-gear"></i> Change Password</a>
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
                            <a href="GPMindex.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="GPMlistApproval.php"><i class="fa fa-fw fa-list"></i> Approval</a>
                        </li>
                        <?php
                        $empid = $_SESSION['gpm'];
                        $empName = GPM::find_by_empid($empid);
                        $Brands = Brand::find_by_division($empName->division);

                        foreach ($Brands as $brand) {
                            $value = BrandBudget::find_by_brand_id($brand->brand_id);
                        }
                        if (empty($value)) {
                            ?>
                            <li>
                                <a href="GPMAddBudget.php"><i class="fa fa-fw fa-dashboard"></i> Add Budget</a>
                            </li>
                            <?php } else { ?>
                            <li>
                                <a href="GPMAddBudget.php"><i class="fa fa-fw fa-dashboard"></i> Manage Budget</a>
                            </li>
                            <?php } ?>

                        <li>
                            <a href="GPMbrandList.php"><i class="fa fa-fw fa-dashboard"></i> Manage Brand</a>
                        </li>
                        <li>
                            <a href="GPMlistPmt.php"><i class="fa fa-fw fa-dashboard"></i> PMT</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-files-o"></i> Reports <i class="fa fa-fw fa-caret-down"></i></a>
                            <ul id="demo" class="collapse">
                                <li>
                                    <a href="GPMbrandwiseExpense.php">Brandwise Expense</a>
                                </li>
                                <li>
                                    <a href="GPMviewItemWiseExpense.php">Categorywise Expense</a>
                                </li>
                                <li>
                                    <a href="GPMdeliveryReport.php">Delivery Report</a>
                                </li>
                                <li>
                                    <a href="GPMpmtwiseExpense.php">PMTwise Expense</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="GPMallocationList.php"><i class="fa fa-fw fa-dashboard"></i> Allocation</a>
                        </li>
                        <li>
                            <a href="GPMupdateManpower.php"><i class="fa fa-fw fa-dashboard"></i> Manpower</a>
                        </li>
                        
            
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>

            <div id="page-wrapper">

                <div class="container-fluid">