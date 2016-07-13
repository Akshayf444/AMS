<?php

session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$finalErrorList = '';
//collect all errors
$Errors = array();
$newRow = array();
$_SESSION['temperoryBudget'] = 0;

$n = 1;
$empid = $_SESSION['employee'];
//find Employee name
$empName = Employee::find_by_empid($empid);

$division = GPM::find_division1($empName->gpm_empid);

//Selecting all assigned Brands for current employee
$brands = Brand::find_by_division($division);

$brandlist = Employee_Brand::find_by_empid($empid);
$brands2 = explode(",", $brandlist);


/* * * Quaterwise Final Budget ** */
/* * * Quarter 4 ** */
if (date("M", time()) == 'Jan' || date("M", time()) == 'Feb' || date("M", time()) == 'Mar') {
    $truncate = TempBudget::truncate($empid);
    $finalBudget = 0;

    foreach ($brands as $singleBrand) {
        $Budget = BrandBudget::find_by_brand_id($singleBrand->brand_id);
        $quarter = 'BETWEEN 1 AND 3';
        $Expense = ItemDetails::find_brandwise_expense($singleBrand->brand_id, $quarter);
        if (!empty($Budget)) {
            $Quater4Expense = $Budget->qtr4 - $Expense;
        } else {
            $Quater4Expense = 0 - $Expense;
        }
        $tempBudget = new TempBudget();
        $tempBudget->brand = $singleBrand->brand_id;
        $tempBudget->budget = $Quater4Expense;
        $tempBudget->empid = $empid;
        $tempBudget->create();
    }

    foreach ($brands2 as $brand) {
        $Budget = TempBudget::find_by_brand_name($brand, $empid);
        if (!empty($Budget)) {
            $finalBudget +=$Budget->budget;
        } else {
            $finalBudget = 0;
        }
    }
}

/* * * Quarter 1 ** */
if (date("M", time()) == 'Apr' || date("M", time()) == 'May' || date("M", time()) == 'Jun') {
    $finalBudget = 0;
    $truncate = TempBudget::truncate();
    foreach ($brands as $singleBrand) {
        $Budget = BrandBudget::find_by_brand_id($singleBrand->brand_id);

        $quarter = 'BETWEEN 4 AND 6';
        $Expense = ItemDetails::find_brandwise_expense($singleBrand->brand_id, $quarter);
        if (!empty($Budget)) {
            $Quater1Expense = $Budget->qtr1 - $Expense;
        } else {
            $Quater1Expense = 0 - $Expense;
        }
        $tempBudget = new TempBudget();
        $tempBudget->brand = $singleBrand->brand_id;
        $tempBudget->budget = $Quater1Expense;
        $tempBudget->empid = $empid;
        $tempBudget->create();
    }

    foreach ($brands2 as $brand) {
        $Budget = TempBudget::find_by_brand_name($brand, $empid);
        if (!empty($Budget)) {
            $finalBudget +=$Budget->budget;
        } else {
            $finalBudget = 0;
        }
    }
}

/* * * Quarter 2 ** */
if (date("M", time()) == 'Jul' || date("M", time()) == 'Aug' || date("M", time()) == 'Sep') {
    $finalBudget = 0;
    $truncate = TempBudget::truncate();
    foreach ($brands as $singleBrand) {
        $Budget = BrandBudget::find_by_brand_id($singleBrand->brand_id);

        $quarter = 'BETWEEN 7 AND 9';
        $Expense = ItemDetails::find_brandwise_expense($singleBrand->brand_id, $quarter);
        if (!empty($Budget)) {
            $Quater2Expense = $Budget->qtr2 - $Expense;
        } else {
            $Quater2Expense = 0 - $Expense;
        }
        $tempBudget = new TempBudget();
        $tempBudget->brand = $singleBrand->brand_id;
        $tempBudget->budget = $Quater2Expense;
        $tempBudget->empid = $empid;
        $tempBudget->create();
    }

    foreach ($brands2 as $brand) {
        $Budget = TempBudget::find_by_brand_name($brand, $empid);
        if (!empty($Budget)) {
            $finalBudget +=$Budget->budget;
        } else {
            $finalBudget = 0;
        }
    }
}

/* * * Quarter 3 ** */
if (date("M", time()) == 'Oct' || date("M", time()) == 'Nov' || date("M", time()) == 'Dec') {
    $finalBudget = 0;
    $truncate = TempBudget::truncate();

    foreach ($brands as $singleBrand) {
        $Budget = BrandBudget::find_by_brand_id($singleBrand->brand_id);

        $quarter = 'BETWEEN 10 AND 12';
        $Expense = ItemDetails::find_brandwise_expense($singleBrand->brand_id, $quarter);
        if (!empty($Budget)) {
            $Quater3Expense = $Budget->qtr3 - $Expense;
        } else {
            $Quater3Expense = 0 - $Expense;
        }
        $tempBudget = new TempBudget();
        $tempBudget->brand = $singleBrand->brand_id;
        $tempBudget->budget = $Quater3Expense;
        $tempBudget->empid = $empid;
        $tempBudget->create();
    }

    foreach ($brands2 as $brand) {
        $Budget = TempBudget::find_by_brand_name($brand, $empid);
        if (!empty($Budget)) {
            $finalBudget +=$Budget->budget;
        } else {
            $finalBudget = 0;
        }
    }
}