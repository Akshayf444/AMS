<?php
session_start();
require_once(dirname(__FILE__) . "/includes/initialize.php");
if (isset($_SESSION['gpm'])) {    
    
    $empid = $_SESSION['gpm'];
    $empName = GPM::find_by_empid($empid);
    $brands = Brand::find_by_division($empName->division);
}
if (isset($_SESSION['mm'])) {
    $empid = $_SESSION['mm'];
    $empName = MM::find_by_empid($empid);
    $divisions = explode(",", $empName->division);
    $brands = array();
    foreach ($divisions as $division) {
         $BrandList =Brand::find_by_division($division);
         foreach ($BrandList as $value) {
             array_push($brands, $value);
         }
    }

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
            <td><?php echo $brand->brand_name; ?></td>
                <?php foreach ($categories as $category) { ?>

                <td><?php
                $Expense = ItemDetails::find_brand_categorywise_expense($brand->brand_id, $quarter, $category);
                echo $Expense; $finalExpense += $Expense; 
                ?>
                </td>
        <?php }//End Of Category Loo[p] ?>
                 <th><?php echo $finalExpense;  ?></th>
        </tr>
    <?php }//End Of Brand Loo[p] 
}// End Of Post 
?>
