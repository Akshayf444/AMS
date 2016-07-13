<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];

$qtr1Total = 0;
$qtr2Total = 0;
$qtr3Total = 0;
$qtr4Total = 0;
$finalAllocatedTotal = 0;
$grandTotal = 0;
$keyNoStatus = '-';

$AllQuarterExpense = 0;
$finalRemainingBudget = 0;



if (isset($_POST['submit'])) {
    $apr_id = $_POST['apr_id'];
    $empid = $_POST['empid'];
    /* $SecurityCheck = Approval::SecurityCheck($apr_id,$empid);
      if (empty($SecurityCheck)) {
      redirect_to("AccessDenied.php");
      } */

    $Items = ItemDetails::find_by_apr_id($apr_id);
    $Approval = Approval::find_by_apr_id($apr_id);
    $brandlist = Employee_Brand::find_by_empid($empid);
    $empName = Employee::find_by_empid($empid);

    $brands2 = ItemDetails::uniqueBrands($apr_id);

    if (!empty($brands2)) {
        foreach (array_unique($brands2) as $Brand) {
            $finalExpense = 0;
            $Budget = BrandBudget::find_by_brand_id($Brand);
            $finalAllocatedTotal += ( $Budget->qtr1 + $Budget->qtr2 + $Budget->qtr3 + $Budget->qtr4);

            $quarter = array('BETWEEN 4 AND 6', 'BETWEEN 7 AND 9', 'BETWEEN 10 AND 12', 'BETWEEN 1 AND 3');
            foreach ($quarter as $value) {
                $Expense = ItemDetails::find_brandwise_expense($Brand, $value);
                $finalExpense += $Expense;
            }

            $AllQuarterExpense += ($Budget->qtr1 + $Budget->qtr2 + $Budget->qtr3 + $Budget->qtr4) - $finalExpense;
        }//End of for loop

        $finalRemainingBudget = $AllQuarterExpense;
    }//End of if

    $html = '<h1>Approval Sheet For Promotional Material</h1><table style="border:1px solid #ddd;width:100%" >';
    foreach ($Approval as $value) {

        $division = GPM::find_division1($empName->gpm_empid);
        $divisionName = Division::find_by_div_id($division);

        //Approval details...
        $itemCategory = ItemDetails::find_item_category($value->apr_id);
        $html.='<tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;border-top:1px solid #ddd" colspan="2">Date</td><td style="padding:8px;border-top:1px solid #ddd" colspan="4">' . date('d-m-Y', strtotime($value->date)) . '</td></tr>
                <tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;" colspan="2">Approval Id</td><td style="padding:8px" colspan="4">' . $value->apr_id . '</td></tr>
                <tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;" colspan="2">Approval Title</td><td style="padding:8px" colspan="4">' . $value->title . '</td></tr>
                <tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;" colspan="2">Division Name</td><td style="padding:8px" colspan="4">' . $divisionName->div_name . '</td></tr>
                <tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;" colspan="2">Vendor/Artist/Agency Name</td><td style="padding:8px" colspan="4">' . $value->vendor . '</td></tr>
                <tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;border-bottom:1px solid #ddd" colspan="2">Item Category</td><td style="padding:8px;border-bottom:1px solid #ddd" colspan="4">' . $itemCategory . '</td></tr>';
    }

    //Item Details...
    $html.='<tr style="border:1px solid #ddd;"><td colspan="5" style="padding:8px" ></td></tr>
                <tr style="border:1px solid #ddd;">
                    <td style="padding:8px;font-weight:bold;border:1px solid #ddd;width:5%">Key No</td>
                    <td style="padding:8px;font-weight:bold;border:1px solid #ddd">Brand Name</td>
                    <td style="padding:8px;font-weight:bold;border:1px solid #ddd">Description Of Item</td>
                    <td style="padding:8px;font-weight:bold;border:1px solid #ddd">Estimated Cost</td>
                    <td style="padding:8px;font-weight:bold;border:1px solid #ddd">Quantity</td>
                    <td style="padding:8px;font-weight:bold;border:1px solid #ddd">Total</td>
            </tr>';

    foreach ($Items as $item) {
        $keyNo = PrDetails::find_by_item_id($item->item_id);
        if (!empty($keyNo)) {
            $keyNoStatus = $keyNo->key_no;
        }
        $grandTotal +=(int) $item->amount;
        $brandId = explode(",", $item->brand_id);
        $finalBrandList = array();
        foreach ($brandId as $id) {
            $brandlist = Brand::find_by_brand_id2($id);
            array_push($finalBrandList, $brandlist->brand_name);
        }
        $grandTotal +=(int) $item->amount;
        $html.='<tr style="border:1px solid #ddd;">
                <td style="padding:8px;border:1px solid #ddd;width:10%">' . $keyNoStatus . '</td>
                <td style="padding:8px;border:1px solid #ddd;width:15%">' . implode(",", $finalBrandList) . '</td>
                <td style="padding:8px;border:1px solid #ddd;width:45%">' . $item->description . '</td>
                <td style="padding:8px;border:1px solid #ddd;width:10%">' . $item->value . '</td>
                <td style="padding:8px;border:1px solid #ddd;width:10%">' . $item->quantity . '</td>
                <td style="padding:8px;border:1px solid #ddd;width:10%">' . $item->amount . '</td>
            </tr>';
    }
    $html.='<tr style="border:1px solid #ddd;">
                <td colspan="5" style="padding:8px;font-weight:bold">Total Amount</td>
                <td style="padding:8px">' . $grandTotal . '</td>
            </tr><tr><td colspan="6" style="padding:8px"></td></tr>';

    foreach ($Approval as $value) {
        $html.='<tr style="border:1px solid #ddd;"><td style="padding:8px;border-top:1px solid #ddd" colspan="2"><strong>Allocated Budget :</strong>' . $finalAllocatedTotal . ' </td><td style="padding:8px;border-top:1px solid #ddd" colspan="4"><strong>Remaining Budget :</strong>' . $finalRemainingBudget . '</td></tr>
            <tr style="border:1px solid #ddd;"><td style="padding:8px;font-weight:bold;" colspan= "2">Delivary Location</td><td style="padding:8px" colspan="4">' . $value->location . '</td></tr>

            <tr style="border:1px solid #ddd;"><td style="padding:8px;width:20%" colspan="2" ><strong>Requisitioner Name :</strong><br/>' . $empName->name . ' </td><td style="padding:8px; colspan ="1"><strong>Email id :</strong><br/>' . $empName->emailid . '</td>
            <td style="padding:8px;" colspan="3" ><strong>Mobile No :</strong><br/>' . $empName->mobile . '</td>
            </tr>

            <tr style="border:1px solid #ddd;"><td colspan="6" style="padding:8px"></td></tr>
            <tr style="border:1px solid #ddd;">
            <td colspan="3" style="padding:8px;font-weight:bold;">Initiated By</td>
            <td colspan="3" style="padding:8px;font-weight:bold;">Approved By</td>
            </tr>
            <tr style="border:1px solid #ddd;">
            <td colspan="3" style="padding:8px">' . $empName->name . '</td>
            <td colspan="3" style="padding:8px"></td></tr> 
            <tr style="border:1px solid #ddd;">
            <td colspan="3" style="padding:8px;font-weight:bold;">Name & Signature</td>
            <td colspan="3" style="padding:8px;font-weight:bold;">Name & Signature</td></tr>';
    }
    $html.='</table>';
}// End of post ...
require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>

<div class="row" id="breadcrumb">
    <div class="col-lg-12">
        <h1 class="page-header">View Approval</h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i>View Approval
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div class="table-responsive">
            <?php
            if (isset($html)) {
                echo $html;
            }
            ?>
        </div>
    </div>
</div>
<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>