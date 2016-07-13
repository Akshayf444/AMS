<?php session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $empid=$_SESSION['PRE'];
 $empName =Employee::find_by_empid($empid);
 $itemCount = 0;

    $Approved = ItemDetails::PREcountApproved("Approved");
    $inProcess = GRN::PREisProcessed('inProcess');
    $Delivered = GRN::PREisProcessed("Processed");
    $Pending = ItemDetails::PREcountApproved("Pending");

    $ItemCategory =array('Print','Gift','E-Input','Publisher','Promo Services','Miscellaneous');

    foreach ($ItemCategory as $Category) {
        $Count =ItemDetails::PREdrawPieChart($Category);
        $dataString = "['".$Category."',".$Count."]" ;
        $Category2[] =$dataString;

    }
    $Items = ItemDetails::find_all();

require_once(dirname(__FILE__)."/layouts/PRElayouts/header.php");?>
<script>

</script>
<script>
$(function () {
    $('#piechart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 1,//null,
            plotShadow: false
        },
        title: {
            text: ''
        },

        credits: {
            enabled:false,
            text: 'Techvertica.com',
            href: 'http://www.techvertica.com'
        },
        
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Item Category',
            data: [
                  <?php echo join(',',$Category2).","; ?>
            ]
        }]
    });
});
</script>
                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Dashboard <small>Statistics Overview</small>
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->

                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-comments fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $Approved; ?></div>
                                        <div>Total Approved Items</div>
                                    </div>
                                </div>
                            </div>
                            <a href="PREviewApprovedItems.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-tasks fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $inProcess; ?></div>
                                        <div>Items In Process</div>
                                    </div>
                                </div>
                            </div>
                            <a href="PREviewInProcess.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-yellow">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-shopping-cart fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $Delivered; ?></div>
                                        <div>No Of Delivered Items</div>
                                    </div>
                                </div>
                            </div>
                            <a href="PREviewProcessedItems.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-red">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-support fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $Pending; ?></div>
                                        <div>No Of Pending Items</div>
                                    </div>
                                </div>
                            </div>
                            <a href="PREviewPendingItems.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.row 

                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Area Chart</h3>
                            </div>
                            <div class="panel-body">
                                 <div id="container"></div>
                            </div>
                        </div>
                    </div>
                </div>
                 /.row -->

                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i> Item Category</h3>
                            </div>
                            <div class="panel-body">
                                <div id="piechart"></div>
                                <div class="text-right">
                                    <a href="PREviewItemWiseExpense.php">View Details <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Transactions Panel</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Key No</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($Items as $Item) { 
                                             $itemCount++;
                                                if ($itemCount == 11) {
                                                    break;
                                                }
                                            ?>
                                            <tr>
                                                <td><?php $PRdetail=PrDetails::find_by_item_id($Item->item_id); 
                                                if(!empty($PRdetail)){
                                                    echo $PRdetail->key_no;
                                                    }else{ 
                                                        echo '-'; 
                                                    } ?>
                                                </td>

                                                <td><?php echo $Item->description; ?></td>
                                                <td><?php $status =GRN::isDelivered($Item->item_id);
                                                        if(!empty($status)){
                                                            echo $status;
                                                        }

                                                 ?>
                                                </td>
                                                
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                               <div class="text-right">
                                    <a href="PREtransactions.php">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>

 <?php require_once(dirname(__FILE__)."/layouts/PRElayouts/footer.php");?>