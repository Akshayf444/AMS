<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$n = 1;
$brandlist = array();
//Array for collecting errors
$Errors = array();

//Array For displaying errors
$finalErrorList = '';

$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$division = GPM::find_division1($empName->gpm_empid);

$brandlist = Employee_Brand::find_by_empid($empid);
//For encrypting URLs

$EncryptUrl = new Encryption();

//Selecting all Brands for current employee
$brands = Brand::find_by_division($division);
$brands2 = explode(",", $brandlist);

if (isset($_GET['apr_id'])) {
    $apr_id = $EncryptUrl->decode($_GET['apr_id']);
    $SecurityCheck = Approval:: EditSecurityCheck($apr_id, $empid);
    if (empty($SecurityCheck)) {
        redirect_to("AccessDenied.php");
    }
    $Items = ItemDetails::find_by_apr_id($apr_id);
    $Approval = Approval::find_by_apr_id($apr_id);
} else {
    redirect_to("listApproval.php");
}

/* * * Quaterwise Final Budget ** */
/* * * Quarter 4 ** */
if (date("M", time()) == 'Jan' || date("M", time()) == 'Feb' || date("M", time()) == 'Mar') {
    $finalBudget = 0;

    foreach ($brands2 as $brand) {
        $quarter = 'BETWEEN 1 AND 3';
        $Budget = BrandBudget::find_by_brand_id($brand);
        $Expense = ItemDetails::edit_brandwise_expense($brand, $quarter, $apr_id);

        if (!empty($Budget)) {
            $finalBudget +=$Budget->qtr4 - $Expense;
        } else {
            $finalBudget = 0;
        }
    }
}

/* * * Quarter 1 ** */
if (date("M", time()) == 'Apr' || date("M", time()) == 'May' || date("M", time()) == 'Jun') {
    $finalBudget = 0;

    foreach ($brands2 as $brand) {
        $quarter = 'BETWEEN 4 AND 6';
        $Budget = BrandBudget::find_by_brand_id($brand);
        $Expense = ItemDetails::edit_brandwise_expense($brand, $quarter, $apr_id);

        if (!empty($Budget)) {
            $finalBudget +=$Budget->qtr1 - $Expense;
        } else {
            $finalBudget = 0;
        }
    }
}

/* * * Quarter 2 ** */
if (date("M", time()) == 'Jul' || date("M", time()) == 'Aug' || date("M", time()) == 'Sep') {
    $finalBudget = 0;

    foreach ($brands2 as $brand) {
        $quarter = 'BETWEEN 7 AND 9';
        $Budget = BrandBudget::find_by_brand_id($brand);
        $Expense = ItemDetails::edit_brandwise_expense($brand, $quarter, $apr_id);

        if (!empty($Budget)) {
            $finalBudget +=$Budget->qtr2 - $Expense;
        } else {
            $finalBudget = 0;
        }
    }
}

/* * * Quarter 3 ** */
if (date("M", time()) == 'Oct' || date("M", time()) == 'Nov' || date("M", time()) == 'Dec') {
    $finalBudget = 0;
    foreach ($brands2 as $brand) {
        $quarter = 'BETWEEN 10 AND 12';
        $Budget = BrandBudget::find_by_brand_id($brand);
        $Expense = ItemDetails::edit_brandwise_expense($brand, $quarter, $apr_id);

        if (!empty($Budget)) {
            $finalBudget +=$Budget->qtr3 - $Expense;
        } else {
            $finalBudget = 0;
        }
    }
}

if (isset($_POST['submit'])) {
    /**     * ****************Add Approval**************************** */
    $newApproval = new Approval();
    $newApproval->empid = $empid;
    foreach ($Approval as $value) {
        $newApproval->apr_id = $value->apr_id;
    }
    if (!empty($_POST['title'])) {
        $newApproval->title = $_POST['title'];
    } else {
        array_push($Errors, "Please Enter Title For An Approval.\n");
    }

    if (!empty($_POST['vendor'])) {
        $newApproval->vendor = $_POST['vendor'];
    } else {
        array_push($Errors, "Please Provide Vendor Details.");
    }

    if (!empty($_POST['location'])) {
        $newApproval->location = $_POST['location'];
    } else {
        array_push($Errors, "Please Provide Location Details.");
    }

    $newApproval->remark = $_POST['remark'];
    $newApproval->date = strftime("%Y-%m-%d ", time());


    /**     * *******************Add Item Details************************** */
    $length = count($_POST['item_category']);
    for ($i = 0; $i < $length; $i++) {
        if (!empty($_POST[$n])) {
            
        } else {
            array_push($Errors, "Please Select Atleast One Brand For Each Item.");
        }

        $n++;
    }

    $n = 1;

    for ($i = 0; $i < $length; $i++) {

        $newItemDetails = new ItemDetails();
        if (!empty($_POST['item_id'][$i])) {
            $newItemDetails->item_id = $_POST['item_id'][$i];
        } else {
            $newItemDetails->item_id = 0;
        }

        if (!empty($_POST[$n])) {
            $newItemDetails->brand_id = implode(",", $_POST[$n]);
            $brandCount = count($_POST[$n]);
            $newItemDetails->brand_count = $brandCount;
        } else {
            array_push($Errors, "Please Select Atleast One Brand For Each Item.\n");
        }

        $newItemDetails->item_category = trim($_POST['item_category'][$i]);
        if (!empty($_POST['description'][$i])) {
            $newItemDetails->description = trim($_POST['description'][$i]);
        } else {
            array_push($Errors, "Please Provide An Item Description.\n");
        }

        $newItemDetails->quantity = trim($_POST['quantity'][$i]);
        $newItemDetails->value = trim($_POST['value'][$i]);
        $newItemDetails->amount = trim($_POST['amount'][$i]);
        $newItemDetails->apr_id = $newApproval->apr_id;
        $newItemDetails->allocated = 1;

        $itemExist = ItemDetails::find_by_item_id($newItemDetails->item_id);
        if (!empty($itemExist) && empty($Errors)) {

            $newItemDetails->update($newItemDetails->item_id);
        }
        if (empty($itemExist) && empty($Errors)) {

            $newItemDetails->item_id = 0;

            $newItemDetails->create();
        }

        $n++;
    }

    if (empty($Errors)) {
        $newApproval->update($newApproval->apr_id);
    }

    if (empty($Errors)) {
        redirect_to("ApprovalSheet.php?apr_id=" . $EncryptUrl->encode($newApproval->apr_id));
    } else {
        $finalErrorList = array_unique($Errors);
    }
}
require_once(dirname(__FILE__) . "/layouts/header.php");
?>
<div class="row">
    <div class="col-lg-2" style="background:yellow;float:right;position:fixed;top:10%;right:0;z-index:1" id="budget">
        <h4 >Your Budget:<input type="text" id="budget1" value="<?php echo $finalBudget; ?>"  readonly></h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit Approval</h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i>Edit Approval
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <?php
    if (!empty($Errors)) {
        foreach ($finalErrorList as $value) {
            echo "<ul><li style='color:red;'>" . $value . "</li></ul>";
        }
    }
    ?>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12">
        <form  role="form" action="editApproval.php?apr_id=<?php echo $_GET['apr_id']; ?>" method="post">
            <div class="table-responsive">
                <table class="table" style="width:50%">
                    <?php
                    if (!empty($Approval)) {
                        foreach ($Approval as $value) {
                            ?>

                            <tr>
                                <th style="border-top:none"> <label for="title" class=" control-label">Approval Title</label></th>
                                <td style="border-top:none">
                                    <input type="text" class="form-control" name="title" value="<?php echo $value->title; ?>" required>
                                </td>
                            </tr>
                            <tr>
                                <th style="border-top:none"><label for="vendor" class=" control-label">Vendor/Artist/Agency</label></th>
                                <td style="border-top:none">
                                    <input type="text" class="form-control" name="vendor" value="<?php echo $value->vendor; ?>" required>
                                </td>
                            </tr>
                            <tr>  
                                <th style="border-top:none"><label for="location" class=" control-label">Delivary Location</label></th>  
                                <td style="border-top:none">
                                    <input type="text" class="form-control" name="location" value="<?php echo $value->location; ?>" required>
                                </td>
                            </tr> 
                            <?php
                        }
                    }
                    ?>   
                </table>
            </div>
            <div class="table-responsive" style="margin-top:2em">
                <table class="table table-bordered table-hover table-striped" id="items">
                    <thead>

                        <tr>
                            <th style="width:15%">Brand/Division</th>
                            <th style="width:12%">Item Category</th>
                            <th>Description Of An Item</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:10%">Value/Item</th>
                            <th style="width:15%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $classNo = 1;

                        foreach ($Items as $item) {
                            $brandlist = explode(",", $item->brand_id);
                            ?>
                            <tr class="targetfields" id="<?php echo $classNo . "11"; ?>">
                                <td>
                                    <div class="form-group">
                                        <select class="form-control multiselect"  multiple="multiple" name="<?php echo $classNo; ?>[]">
                                            <?php foreach ($brands as $brand) { ?>
                                                <option  value="<?php echo $brand->brand_id; ?>"  <?php
                                                foreach ($brandlist as $brand2) {
                                                    if ($brand2 == $brand->brand_id) {
                                                        echo "selected";
                                                    }
                                                }
                                                ?> ><?php echo $brand->brand_name; ?></option>
                                                     <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select class="form-control" name="item_category[]">
                                            <option <?php
                                            if ($item->item_category == 'Print') {
                                                echo 'selected';
                                            }
                                            ?> >Print</option>                            
                                            <option <?php
                                            if ($item->item_category == 'Gift') {
                                                echo 'selected';
                                            }
                                            ?> >Gift</option>
                                            <option <?php
                                            if ($item->item_category == 'E-Input') {
                                                echo 'selected';
                                            }
                                            ?> >E-Input</option>
                                            <option <?php
                                            if ($item->item_category == 'Publisher') {
                                                echo 'selected';
                                            }
                                            ?> >Publisher</option>
                                            <option <?php
                                            if ($item->item_category == 'Promo Services') {
                                                echo 'selected';
                                            }
                                            ?> >Promo Services</option>
                                            <option <?php
                                            if ($item->item_category == 'Miscellaneous') {
                                                echo 'selected';
                                            }
                                            ?> >Miscellaneous</option>
                                        </select>
                                    </div>
                                </td>
                                <td><input class="form-control itemDescription"  name="description[]" type="text" value="<?php echo $item->description; ?>" required></td>
                                <td><input class="form-control quantity common" name="quantity[]" type="text" value="<?php echo $item->quantity; ?>"></td>
                                <td><input class="form-control rate common" name="value[]"  type="text" value="<?php echo $item->value; ?>"></td>
                                <td><input class="form-control subtotal" name="amount[]" type="text" value="<?php echo $item->amount; ?>" readonly></td>
                                <td><a href="deleteRows.php?item_id=<?php echo $item->item_id; ?>&apr_id=<?php echo $item->apr_id; ?>"><button type="button" class="btn btn-xs btn-info " ><span class="glyphicon glyphicon-trash"></span></button></a>
                                    <input type="hidden" value="<?php echo $item->item_id; ?>" name="item_id[]">
                                </td>

                            </tr>

                            <?php
                            $classNo ++;
                        }
                        ?>  
                </table>
                <table class="table table-bordered">
                    <tr>
                    <button type="button" class="btn btn-link" onclick="addRow()" >Add Rows</button>
                    <span id="add" style="display:none"><img src="images/loader3.gif"></span>
                    </tr>
                    <tr>
                        <td colspan="7"><strong>Total :</strong><span id="total"></span></td>
                    </tr>
                    <tr><td colspan="7">
                            <div class="form-group">
                                <label>Remarks</label>
                                <?php
                                if (!empty($Approval)) {
                                    foreach ($Approval as $value) {
                                        ?>
                                        <textarea class="form-control" rows="3" name="remark" ><?php echo $value->remark; ?></textarea>
                                        <?php
                                    }
                                }
                                ?> 
                            </div></td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <button type="submit" class="btn btn-primary" name="submit"  onClick="return validate();">Save And Print</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>        
        </form>
        <input type="hidden" value="<?php echo $apr_id; ?>"  id="approvalid">
    </div>
</div>

<script src="js/addNewRow.js" type="text/javascript"></script>

<script>
                                jQuery(function () {
                                    jQuery("#items ").delegate('.common', 'keyup', function () {
                                        var total = 0;
                                        jQuery("#items .targetfields").each(function () {
                                            var qty = parseFloat(jQuery(this).find(".quantity").val()) || 0;
                                            var rate = parseFloat(jQuery(this).find(".rate").val()) || 0;

                                            var subtotal = qty * rate;
                                            jQuery(this).find(".subtotal").val(subtotal.toFixed(2));
                                            if (!isNaN(subtotal))
                                                total += subtotal;
                                        });
                                        jQuery("#total").html(total.toFixed(2));
                                    });

                                    jQuery("#items ").delegate('.subtotal', 'keyup', function () {
                                        var total = 0;
                                        jQuery("#items .targetfields").each(function () {
                                            var subtotal = parseFloat(jQuery(this).find(".subtotal").val());
                                            //total = parseInt($("#total").val());
                                            if (!isNaN(subtotal))
                                                total += subtotal;
                                        });
                                        jQuery("#total").html(total.toFixed(2));
                                    });

                                });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.multiselect').multiselect({
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 300
        });
    });
</script>
<script>
    function initializeBalance() {
        var search_term = $("#approvalid").val();
        //alert(search_term);
        $.post('editTempBudget.php', {search_term: search_term}, function (data) {
            ajaxRequest();
        });
    }

    function ajaxRequest() {
        console.log('In ajax request');
        $("#budget").css("background", " url('images/loader.gif') no-repeat scroll center center red");

        var jsonObj = [];
        var counter = 0;
        jQuery("#items .targetfields").each(function () {
            item = {};
            var subtotal = parseFloat(jQuery(this).find(".subtotal").val()) || 0;
            var brands = jQuery(this).find(".multiselect").val();

            if (brands != null) {
                var brand_count = parseInt(brands.length);
                var selected_brands = brands.join();

                var total = (subtotal / brand_count);
                item["brand_count"] = brand_count;
                item["selected_brands"] = selected_brands;
                item["total"] = total;
                jsonObj.push(item);
                counter++;
            } else {
                //alert("Please Select Atlest One Brand For Each Item");
                return false;
            }

        });

        console.log(jsonObj);

        var jsonString = JSON.stringify(jsonObj);
        //alert(jsonString);
        $.ajax({
            //Send request
            type: 'POST',
            data: {data: jsonString},
            url: 'calculateBudget.php',
            success: function (data) {
                if (data == null || data == '') {
                    alert();
                } else {
                    $("#budget").css("background", "yellow");
                    $("#budget").html(data);
                    //$("#SaveApproval").unbind("submit").submit();

                }

            }
        });

    }

    function brandValidation() {
        var count = 0;
        console.log('in Brand Validation');
        jQuery("#items .targetfields").each(function () {
            var agencyName = jQuery("#AgencyName").val();
            var location = jQuery("#DelivaryLocation").val();

            var description = jQuery(this).find(".itemDescription").val();
            var brands = jQuery(this).find(".multiselect").val();

            if (brands == null || brands == '') {
                count++;
                alert('Please Select Atleast One Brand For Each Item');
                throw new Error('Something Went Wrong');

                //return false;
            }
            if (description == null || description == '') {
                alert('Please Enter Item Description In Each Row');
                count++;
                throw new Error('Something Went Wrong');

                //return false;
            }

        });
        if (count > 0) {
            alert(count);
            throw new Error('Something Went Wrong');
            return false;
        }
    }
</script>
<script>
    var typingTimer;                //timer identifier
    var doneTypingInterval1 = 300;
    jQuery("#items ").delegate('.rate ', 'keyup', function () {


        var currentRow = $(this).closest('tr');
        var rowid = currentRow.attr('id');

        //alert(rowid);
        clearTimeout(typingTimer);
        if ($(this).val) {
            typingTimer = setTimeout(function () {

                //do stuff here e.g ajax call etc....            

                //var brands = new Array();

                var brands = $("#" + rowid).find(".multiselect").val();
                var description = $("#" + rowid).find(".itemDescription").val();

                //alert(description);
                var finalDescription = description.trim();
                //alert(brands);


                if ((brands != null)) {
                    if ((finalDescription != '')) {
                        console.log('process ajax request');
                        initializeBalance();
                    } else {
                        alert("Enter Item Description");
                    }
                } else {
                    console.log('process alert');
                    alert("Please Select Brand");
                }



            }, doneTypingInterval1);
        }

    });
</script>
<script>
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;
    jQuery("#items ").delegate('.multiselect ', 'change', function () {
        //alert("in multiselect");
        var currentRow = $(this).closest('tr');
        var rowid = currentRow.attr('id');

        //alert(rowid);
        clearTimeout(typingTimer);
        if ($(this).val) {
            typingTimer = setTimeout(function () {
                var brands = $("#" + rowid).find(".multiselect").val();
                var description = $("#" + rowid).find(".itemDescription").val();
                var subtotal = parseFloat($("#" + rowid).find(".subtotal").val()) || 0;
                //alert(description);
                var finalDescription = description.trim();
                // alert(brands);
                if (subtotal > 0) {
                    if ((brands != null)) {
                        if ((finalDescription != '')) {

                            console.log('process ajax request on dropdown click');
                            initializeBalance();
                        }

                    } else {

                        console.log('process alert on dropdown click');
                    }
                }

            }, doneTypingInterval);
        }

    });
</script>
<?php require_once(dirname(__FILE__) . "/layouts/footer.php"); ?>