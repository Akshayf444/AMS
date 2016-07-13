<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$brandlist = Employee_Brand::find_by_empid($empid);
if (!empty($brandlist)) {
    $brands = explode(",", $brandlist);
} else {
    $brands = array();
}

if (isset($_POST['search_term'])) {
    $quarter = $_POST['search_term'];
    ?>
    <?php
    foreach ($brands as $brand) {
        $finalExpense = 0 ;
        $categories = array('Print', 'Gift', 'E-Input', 'Publisher', 'Promo Services', 'Miscellaneous');
        ?>
        <tr>
            <td><?php $brandName = Brand::find_by_brand_id2($brand); echo $brandName->brand_name;?></td>
            <?php foreach ($categories as $category) { ?>

                <td>
                    <?php
                    $Expense = ItemDetails::find_brand_categorywise_expense($brand, $quarter, $category);
                    echo $Expense;
                    $finalExpense += $Expense; 
                    ?>
                </td>
            <?php }//End Of Category Loo[p] ?>
                <th><?php echo $finalExpense;  ?></th>
        </tr>
        <?php
    }//End Of Brand Loo[p] 
}// End Of Post ?>