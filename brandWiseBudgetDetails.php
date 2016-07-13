<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$division = GPM::find_division1($empName->gpm_empid);
$qtr1Total = 0;
$qtr2Total = 0;
$qtr3Total = 0;
$qtr4Total = 0;

$qtr1Remaining = 0;
$qtr2Remaining = 0;
$qtr3Remaining = 0;
$qtr4Remaining = 0;

//Selecting all Brands for current employee
$brandlist = Employee_Brand::find_by_empid($empid);
$brands2 = explode(",", $brandlist);
require_once(dirname(__FILE__) . "/layouts/header.php");
?>
<div class="row">
    <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">

        <h1 class="page-header">BrandWise Budget Details</h1>

        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> BrandWise Budget Details
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="table-responsive col-lg-12 col-md-12 col-sm-12" >
        <table class="table table-bordered table-hover table-striped" >
            <thead>
                <tr>
                    <td></td>
                    <td colspan="5" style="text-align:center">
                        <strong> Allocated</strong>
                    </td>
                    <td colspan="5" style="text-align:center">
                        <strong> Remaining</strong>
                    </td>
                </tr>
                <tr>
                    <th >Brand</th>
                    <th >Quarter 1 </th>
                    <th >Quarter 2 </th>
                    <th >Quarter 3 </th>
                    <th >Quarter 4 </th>
                    <th >Total</th>
                    <th >Quarter 1 </th>
                    <th >Quarter 2 </th>
                    <th >Quarter 3 </th>
                    <th >Quarter 4 </th>
                    <th >Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($brands2)) {
                    foreach ($brands2 as $Brand) {
                        $Budget = BrandBudget::find_by_brand_id($Brand);

                        if (!empty($Budget)) {
                            ?>
                            <tr>
                                <td><?php echo $Budget->brand_name; ?></td>
                                <td><?php echo $Budget->qtr1;
                                        $qtr1Total +=$Budget->qtr1;
                                    ?></td>
                                <td><?php echo $Budget->qtr2;
                                        $qtr2Total +=$Budget->qtr2;
                                ?>
                                </td>
                                <td><?php echo $Budget->qtr3;
                                        $qtr3Total +=$Budget->qtr3;
                                ?>
                                </td>
                                <td><?php echo $Budget->qtr4;
                                        $qtr4Total +=$Budget->qtr4;
                                    ?>
                                </td>

                                <td><?php echo $Budget->qtr1 + $Budget->qtr2 + $Budget->qtr3 + $Budget->qtr4; ?></td>

                                <td><?php
                                    //echo $Budget->qtr1_remaining; $qtr1Remaining+=$Budget->qtr1_remaining;
                                    $quarter = 'BETWEEN 4 AND 6';
                                    $Expense = ItemDetails::find_brandwise_expense($Brand, $quarter);
                                    $Quater1Expense = $Budget->qtr1 - $Expense;
                                    echo $Quater1Expense;
                                    $qtr1Remaining+=$Budget->qtr1 - $Expense;
                                    ?></td>
                                <td><?php
                                    $quarter = 'BETWEEN 7 AND 9'; //echo $Budget->qtr2_remaining; $qtr2Remaining+=$Budget->qtr2_remaining;
                                    $Expense = ItemDetails::find_brandwise_expense($Brand, $quarter);
                                    $Quater2Expense = $Budget->qtr2 - $Expense;
                                    echo $Quater2Expense;
                                    $qtr2Remaining+=$Budget->qtr2 - $Expense;
                                    ?></td>
                                <td><?php
                                    $quarter = 'BETWEEN 10 AND 12'; //echo $Budget->qtr3_remaining; $qtr3Remaining+=$Budget->qtr3_remaining;
                                    $Expense = ItemDetails::find_brandwise_expense($Brand, $quarter);
                                    $Quater3Expense = $Budget->qtr3 - $Expense;
                                    echo $Quater3Expense;
                                    $qtr3Remaining+=$Budget->qtr3 - $Expense;
                                    ?></td>
                                <td><?php
                                    $quarter = 'BETWEEN 1 AND 3'; //echo $Budget->qtr4_remaining; $qtr4Remaining+=$Budget->qtr4_remaining;
                                    $Expense = ItemDetails::find_brandwise_expense($Brand, $quarter);
                                    $Quater4Expense = $Budget->qtr4 - $Expense;
                                    echo $Quater4Expense;
                                    $qtr4Remaining+=$Budget->qtr4 - $Expense;
                                    ?></td>

                                <td><?php echo $Quater1Expense + $Quater2Expense + $Quater3Expense + $Quater4Expense; ?></td>
                            </tr>
                            <?php
                        } /*                         * * End Of if ** */
                    } /*                     * * End Of Loop ** */
                } /*                 * * End of if ** */
                ?>
                <tr>
                    <td style="border-top:2px solid #ddd;"><strong>Total</strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr1Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr2Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr3Total; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr4Total; ?></strong></td>

                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr1Total + $qtr2Total + $qtr3Total + $qtr4Total; ?></strong></td>

                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr1Remaining; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr2Remaining; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr3Remaining; ?></strong></td>
                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr4Remaining; ?></strong></td>

                    <td style="border-top:2px solid #ddd;"><strong><?php echo $qtr1Remaining + $qtr2Remaining + $qtr3Remaining + $qtr4Remaining; ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="container"></div>

<?php require_once(dirname(__FILE__) . "/layouts/footer.php"); ?>