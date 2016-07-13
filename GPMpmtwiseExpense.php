<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$Employees = Employee::find_by_gpm($empid);
$brands = Brand::find_by_division($empName->division);

//find_quarter_and_categorywise_items

require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            PMTwise Expense
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-desktop"></i> PMTwise Expense
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <select class="form-control quarter" onchange="Search()">
                <option value="">Select Quarter</option>
                <option value="null">All</option>     
                <option value="BETWEEN 4 AND 6">Quarter 1</option>                            
                <option value="BETWEEN 7 AND 9">Quarter 2</option>
                <option value="BETWEEN 10 AND 12">Quarter 3</option>
                <option value="BETWEEN 1 AND 3">Quarter 4</option>
            </select>
        </div>
    </div>
    <div class="col-lg-3 " id="loader" style="display:none">
        <img src="images/loader3.gif">
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>PMT Name</th>
                        <th>Print</th>
                        <th>Gift</th>
                        <th>E-Input</th>
                        <th>Publisher</th>
                        <th>Promo Services</th>
                        <th>Miscellaneous</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="results">
                    <?php
                    foreach ($Employees as $Employee) {
                        $finalExpense = 0;
                        $categories = array('Print', 'Gift', 'E-Input', 'Publisher', 'Promo Services', 'Miscellaneous');
                        //$AssignedBrand =Employee_Brand::find_by_empid2($PMT->empid);
                        ?>
                        <tr>
                            <td><?php echo $Employee->name; ?></td>
                                <?php foreach ($categories as $category) { ?>

                                <td><?php
                                $quarter = 'null';
                                $Expense = ItemDetails::find_brand_categorywise_expense_PMT($Employee->empid, $quarter, $category);
                                echo $Expense;
                                $finalExpense += $Expense;
                                ?>
                                </td>
    <?php }//End Of Category Loo[p]  ?>
                            <th><?php echo $finalExpense; ?></th>
                        </tr>
<?php }//End Of Brand Loo[p]  ?>


                </tbody>
            </table>
        </div>
    </div>
    <script>
        function Search() {
            $("#loader").show();
            $(".result").css("background", " url('images/loader.gif') no-repeat scroll center center ");
            var search_term = $(".quarter").val();

            $.post('GPMgetPMTwiseExpense.php', {search_term: search_term}, function (data) {

                $('.results').html(data);
                $("#loader").hide();
            });
        }
    </script>
<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>