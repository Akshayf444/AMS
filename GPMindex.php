<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$Employees = Employee::find_by_gpm($empid);
$itemCount = 0; //For transaction panel
$finalApprovedCount = 0;
$finalinProcessCount = 0;
$finalDeliveredCount = 0;
$finalPendingCount = 0;
$finalItems = array();

foreach ($Employees as $Employee) {
    $Approved = ItemDetails::countApproved($Employee->empid, "Approved");
    $finalApprovedCount +=$Approved;

    $inProcess = GRN::isProcessed($Employee->empid, 'inProcess');
    $finalinProcessCount +=$inProcess;

    $Delivered = GRN::isProcessed($Employee->empid, "Processed");
    $finalDeliveredCount +=$Delivered;

    $Pending = ItemDetails::countApproved($Employee->empid, "Pending");
    $finalPendingCount +=$Pending;

    $Items = ItemDetails::find_by_empid($Employee->empid);
    foreach ($Items as $value) {
        array_push($finalItems, $value);
    }
}

$ItemCategory = array('Print', 'Gift', 'E-Input', 'Publisher', 'Promo Services', 'Miscellaneous');

foreach ($ItemCategory as $Category) {
    $Count = ItemDetails::GPMdrawPieChart($empid, $Category);
    $dataString = "['" . $Category . "'," . $Count . "]";
    $Category2[] = $dataString;
}

$brands2 = Brand::find_by_division($empName->division); //List of brands by division ....
require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>

<script>
    $(function () {
        $('#piechart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 1, //null,
                plotShadow: false
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false,
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
                        <?php echo join(',', $Category2) . ","; ?>
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

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-comments fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $finalApprovedCount; ?></div>
                        <div>Total Approved Items</div>
                    </div>
                </div>
            </div>
            <a href="GPMviewApprovedItems.php">
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
                        <div class="huge"><?php echo $finalinProcessCount; ?></div>
                        <div>Items In Process</div>
                    </div>
                </div>
            </div>
            <a href="GPMviewInProcess.php">
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
                        <div class="huge"><?php echo $finalDeliveredCount; ?></div>
                        <div>No Of Delivered Items</div>
                    </div>
                </div>
            </div>
            <a href="GPMviewProcessedItems.php">
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
                        <div class="huge"><?php echo $finalPendingCount; ?></div>
                        <div>No Of Pending Items</div>
                    </div>
                </div>
            </div>
            <a href="GPMviewPendingItems.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> BrandWise Expense</h3>
            </div>
            <div class="panel-body">
                <div id="container"></div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i> Item Category</h3>
            </div>
            <div class="panel-body">
                <div id="piechart"></div>
                <div class="text-right">
                    <a href="GPMviewItemWiseExpense.php">View Details <i class="fa fa-arrow-circle-right"></i></a>
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
                            <?php
                            foreach ($finalItems as $Item) {
                                $itemCount++;
                                if ($itemCount == 11) {
                                    break;
                                }
                                ?>
                                <tr>
                                    <td><?php
                                        $PRdetail = PrDetails::find_by_item_id($Item->item_id);
                                        if (!empty($PRdetail)) {
                                            echo $PRdetail->key_no;
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td><?php echo $Item->description; ?></td>
                                    <td><?php
                                        $status = GRN::isDelivered($Item->item_id);
                                        if (!empty($status)) {
                                            echo $status;
                                        }
                                        ?>
                                    </td>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    <a href="GPMtransactions.php">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div><!--End Of row-->
<div class="row">

</div>
<!-- /.row -->
<script>
    $(function () {
        $('#container').highcharts({
            chart: {
                type: 'areaspline'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            xAxis: {
                categories: ['Quarter1', 'Quarter2', 'Quarter3', 'Quarter4'],
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: 'Value'
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                valueSuffix: ' '
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        lineWidth: 1,
                        lineColor: '#666666'
                    }
                }
            },
            series: [
<?php
if (!empty($brands2)) {
    foreach ($brands2 as $Brand) {
        $Budget = BrandBudget::find_by_brand_id($Brand->brand_id);
        if (!empty($Budget)) {
            $quarter = array('BETWEEN 4 AND 6', 'BETWEEN 7 AND 9', 'BETWEEN 10 AND 12', 'BETWEEN 1 AND 3');
            foreach ($quarter as $value) {
                $Expense = ItemDetails::find_brandwise_expense($Brand->brand_id, $value);
                $brandSeriesData[] = $Expense;
            }
        }
        ?>
                        {
                            name: '<?php
        if (!empty($Budget)) {
            echo $Budget->brand_name;
        } else {
            echo $Brand->brand_name;
        }
        ?>',
                            data: [<?php
        echo join(",", $brandSeriesData);
        unset($brandSeriesData);
        ?>]
                        },
        <?php
    }//*** End Of For Loop ******/
}/* * * End Of Outer If *** */
?>

            ]
        });
    });
</script>
<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>