<?php
require_once(dirname(__FILE__) . "/includes/initialize.php");
session_start();
if (isset($_SESSION['gpm'])) {
    header("Location:login.php");
    $empid = $_SESSION['gpm'];
    $empName = GPM::find_by_empid($empid);
    $Employees = Employee::find_by_gpm($empid);
    $brands = Brand::find_by_division($empName->division);
}
if (isset($_SESSION['mm'])) {
    $empid = $_SESSION['mm'];
    $Employees = Employee::find_by_mm($empid);

}

if (isset($_POST['search_term'])) {
    $quarter = $_POST['search_term'];
    ?>
    <?php
    foreach ($Employees as $Employee) {
        $finalExpense = 0 ;
        $categories = array('Print', 'Gift', 'E-Input', 'Publisher', 'Promo Services', 'Miscellaneous');
        ?>
        <tr>
            <td><?php echo $Employee->name; ?></td>
                <?php foreach ($categories as $category) { ?>

                <td><?php
                $Expense = ItemDetails::find_brand_categorywise_expense_PMT($Employee->empid, $quarter, $category);
                echo $Expense; $finalExpense += $Expense; 
                ?>
                </td>
        <?php }//End Of Category Loo[p] ?>
                 <th><?php echo $finalExpense;  ?></th>
        </tr>
    <?php }//End Of Brand Loo[p] 
}// End Of Post 
?>
