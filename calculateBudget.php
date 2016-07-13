<?php
session_start();
if (!isset($_SESSION['employee'])) {
    echo "<script>window.location='login.php'</script>";
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$found_budget = BrandBudget::find_all();
//var_dump($_POST);
//print_r($_POST['data']);
$data = json_decode($_POST['data'], true);
//print_r($data);

$warning = array();
foreach ($data as $dataArr) {
    $empid = $_SESSION['employee'];
    $finalBudget = 0;
   
    $warnignString = 'Brand Budget Will Be Less Than Zero For Following Brands :';
    $negative_balance = false;

    if (!empty($found_budget)) {

        $total = floatval($dataArr['total']);
        $brand_count = $dataArr['brand_count'];
        $allBrands = preg_split("/,/", $dataArr['selected_brands']);
        $brandlist = Employee_Brand::find_by_empid($empid);
        $brands2 = explode(",", $brandlist);

        $tempraryBudget = array();
        $quarter = "Quarter4";

        foreach ($allBrands as $brands) {
            //echo $brands . " : ";
            $RemainingBudget = TempBudget::find_by_brand_name($brands , $empid);
            if (!empty($RemainingBudget)) {
                $CurrentBudget = $RemainingBudget->budget - $total;
                //echo $CurrentBudget . "<br/>";
                if ($CurrentBudget < 0) {
                    array_push($warning, $brands);
                }
            }
            unset($CurrentBudget);
        }//End Of For Loop*****//


        if (empty($warning)) {
            foreach ($allBrands as $brands) {
                $RemainingBudget = TempBudget::find_by_brand_name($brands ,$empid);
                if (!empty($RemainingBudget)) {
                    $CurrentBudget = $RemainingBudget->budget - $total;
                    $tempBudget = new TempBudget();
                    $tempBudget->brand = $brands;
                    $tempBudget->budget = $CurrentBudget;
                    $tempBudget->empid = $empid;
                    $tempBudget->update($tempBudget->brand);
                }
            }//End Of For Loop*****
            $negative_balance == false;
        }

        
    }
}/****End Of Outer For ***/

finalBudget($warning);

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/


function finalBudget($warning) {

    $finalBudget = 0;
    $empid = $_SESSION['employee'];
    $brandlist = Employee_Brand::find_by_empid($empid);
    $brands2 = explode(",", $brandlist);
    foreach ($brands2 as $brand) {
        $Budget = TempBudget::find_by_brand_name($brand , $empid);
        $finalBudget += $Budget->budget;
    }

    if (empty($warning)) {
        echo '<h4 >Your Budget:<input type="text" id="budget1" value="' . $finalBudget . '" class="form-control" readonly></h4>';
    } else {
        $brandlist1 = array();
        foreach ($warning as $value) {
           $temp = Brand::find_by_brand_id2($value);
           array_push($brandlist1, $temp->brand_name);
        }
        echo "<input type='text' id='budget1' value='-10' style='display:none'><h4>Brand Budget Will Be Less Than Zero For Following Brands :</h4>" . join(",",$brandlist1);
    }
}

?>