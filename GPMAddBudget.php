<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$Brands = Brand::find_by_division1($empName->division);

if (isset($_POST['submit'])) {
    $AddBrandBudget = new BrandBudget();
    $length = count($_POST['brand_name']);
    for ($i = 0; $i < $length; $i++) {
        $AddBrandBudget->brand_id = trim($_POST['brand_id'][$i]);
        $AddBrandBudget->brand_name = trim($_POST['brand_name'][$i]);
        $AddBrandBudget->qtr1 = trim($_POST['qtr1'][$i]);
        $AddBrandBudget->qtr2 = trim($_POST['qtr2'][$i]);
        $AddBrandBudget->qtr3 = trim($_POST['qtr3'][$i]);
        $AddBrandBudget->qtr4 = trim($_POST['qtr4'][$i]);
        $AddBrandBudget->qtr1_remaining = trim($_POST['qtr1'][$i]);
        $AddBrandBudget->qtr2_remaining = trim($_POST['qtr2'][$i]);
        $AddBrandBudget->qtr3_remaining = trim($_POST['qtr3'][$i]);
        $AddBrandBudget->qtr4_remaining = trim($_POST['qtr4'][$i]);

        $found_brand = BrandBudget::find_by_brand_id($AddBrandBudget->brand_id);
        if (empty($found_brand)) {
            $result = $AddBrandBudget->create();
        } else {
            $AddBrandBudget->update($AddBrandBudget->brand_id);
        }

        $_SESSION['Error'] = "Budget Added Successfully.";
    }
    redirect_to("gpmViewBudget.php");
}

require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-2" style="background:Red;float:right;position:fixed;top:10%;right:0;z-index:1" >
        <h4 >Total : <span id="total"></span></h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Budget Allocation
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i>Budget Allocation
            </li>
        </ol>
    </div>
</div>
<div id="errors" class="row">
    <div class="col-lg-12">
<?php
if (isset($_SESSION['Error'])) {
    echo $_SESSION['Error'];
    unset($_SESSION['Error']);
}
?>
    </div>
</div>
<div class="row">
    <div class="col-lg-10">
        <form action="gpmAddBudget.php" method="post">

            <div class="table-responsive" style="margin-top:2em">
                <table class="table table-bordered table-hover table-striped" id="items">
                    <thead>
                        <tr>
                            <th>Brand Name</th>
                            <th>Quarter 1</th>
                            <th>Quarter 2</th>
                            <th>Quarter 3</th>
                            <th>Quarter 4</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($Brands as $Brand) { 
    
    $Budget =BrandBudget::find_by_brand_id($Brand->brand_id);
    ?>
                            <tr class="targetfields">
                                <td><input class="form-control"  name="brand_name[]" type="text" value="<?php echo $Brand->brand_name; ?>" readonly></td>
                                <td><input class="form-control qtr1 common"  name="qtr1[]" type="text" value="<?php if(!empty($Budget)){ echo $Budget->qtr1;}?>" ></td>
                                <td><input class="form-control qtr2 common"  name="qtr2[]" type="text" value="<?php if(!empty($Budget)){ echo $Budget->qtr2;}?>" ></td>
                                <td><input class="form-control qtr3 common"  name="qtr3[]" type="text" value="<?php if(!empty($Budget)){ echo $Budget->qtr3;}?>" ></td>
                                <td><input class="form-control qtr4 common"  name="qtr4[]" type="text" value="<?php if(!empty($Budget)){ echo $Budget->qtr4;}?>" >
                                    <input  name="brand_id[]" type="hidden" value="<?php echo $Brand->brand_id; ?>" >
                                </td>
                                <td><input class="form-control subtotal"   type="text" value="" disabled></td>
                            </tr>
<?php } ?>
                        <tr class="targetfields">
                            <td><strong>Total</strong></td>
                            <td><input class="form-control qtr1total" type="text" value="" disabled></td>
                            <td><input class="form-control qtr2total" type="text" value="" disabled></td>
                            <td><input class="form-control qtr3total" type="text" value="" disabled></td>
                            <td><input class="form-control qtr4total" type="text" value="" disabled></td>
                            <td><input class="form-control grandtotal" type="text" value="" disabled></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                <button type="submit" class="btn btn-primary" name="submit" >Save</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<script>
    jQuery(function () {

        jQuery("#items ").delegate('.common ', 'keyup', function () {
            var total = 0;
            var qtr1total = 0;
            var qtr2total = 0;
            var qtr3total = 0;
            var qtr4total = 0;

            jQuery("#items .targetfields").each(function () {

                //get the  values of quantity and rate 
                var qtr1 = parseFloat(jQuery(this).find(".qtr1").val()) || 0;
                var qtr2 = parseFloat(jQuery(this).find(".qtr2").val()) || 0;
                var qtr3 = parseFloat(jQuery(this).find(".qtr3").val()) || 0;
                var qtr4 = parseFloat(jQuery(this).find(".qtr4").val()) || 0;

                //calculate subtotal
                qtr1total += qtr1;
                qtr2total += qtr2;
                qtr3total += qtr3;
                qtr4total += qtr4;

                var subtotal = qtr1 + qtr2 + qtr3 + qtr4;
                jQuery(this).find(".subtotal").val(subtotal.toFixed(2));


                //calculate GrandTotal
                if (!isNaN(subtotal))
                    total += subtotal;

                /* if(!isNaN(subtotal))
                 total+=subtotal;
                 
                 if(!isNaN(subtotal))
                 total+=subtotal;
                 if(!isNaN(subtotal))
                 total+=subtotal;
                 if(!isNaN(subtotal))
                 total+=subtotal;*/

            });

            //Display final total
            jQuery("#total").html(total.toFixed(2));
            jQuery(".grandtotal").val(total.toFixed(2));
            jQuery(".qtr1total").val(qtr1total.toFixed(2));
            jQuery(".qtr2total").val(qtr2total.toFixed(2));
            jQuery(".qtr3total").val(qtr3total.toFixed(2));
            jQuery(".qtr4total").val(qtr4total.toFixed(2));
        });
    });
</script>
<script>
    $(window).load(function () {
        var total = 0;
        var qtr1total = 0;
        var qtr2total = 0;
        var qtr3total = 0;
        var qtr4total = 0;

        jQuery("#items .targetfields").each(function () {

            //get the  values of quantity and rate 
            var qtr1 = parseFloat(jQuery(this).find(".qtr1").val()) || 0;
            var qtr2 = parseFloat(jQuery(this).find(".qtr2").val()) || 0;
            var qtr3 = parseFloat(jQuery(this).find(".qtr3").val()) || 0;
            var qtr4 = parseFloat(jQuery(this).find(".qtr4").val()) || 0;

            //calculate subtotal
            qtr1total += qtr1;
            qtr2total += qtr2;
            qtr3total += qtr3;
            qtr4total += qtr4;

            var subtotal = qtr1 + qtr2 + qtr3 + qtr4;
            jQuery(this).find(".subtotal").val(subtotal.toFixed(2));


            //calculate GrandTotal
            if (!isNaN(subtotal))
                total += subtotal;

            /* if(!isNaN(subtotal))
             total+=subtotal;
             
             if(!isNaN(subtotal))
             total+=subtotal;
             if(!isNaN(subtotal))
             total+=subtotal;
             if(!isNaN(subtotal))
             total+=subtotal;*/

        });

        //Display final total
        jQuery("#total").html(total.toFixed(2));
        jQuery(".grandtotal").val(total.toFixed(2));
        jQuery(".qtr1total").val(qtr1total.toFixed(2));
        jQuery(".qtr2total").val(qtr2total.toFixed(2));
        jQuery(".qtr3total").val(qtr3total.toFixed(2));
        jQuery(".qtr4total").val(qtr4total.toFixed(2));

    });
</script>
<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>