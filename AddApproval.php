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

$brandlist = Employee_Brand::find_by_empid($empid);
$division = GPM::find_division1($empName->gpm_empid);


$EncryptUrl = new Encryption();

//Selecting all assigned Brands for current employee
$brands = Brand::find_by_division($division);
$brands2 = explode(",", $brandlist);

/* * * Quaterwise Final Budget ** */
/* * * Quarter 4 ** */
if (date("M", time()) == 'Jan' || date("M", time()) == 'Feb' || date("M", time()) == 'Mar') {
    $finalBudget = 0;

    foreach ($brands2 as $brand) {
        $quarter = 'BETWEEN 1 AND 3';
        $Budget = BrandBudget::find_by_brand_id($brand);
        $Expense = ItemDetails::find_brandwise_expense($brand, $quarter);

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
        $Expense = ItemDetails::find_brandwise_expense($brand, $quarter);

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
        $Expense = ItemDetails::find_brandwise_expense($brand, $quarter);

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
        $Expense = ItemDetails::find_brandwise_expense($brand, $quarter);

        if (!empty($Budget)) {
            $finalBudget +=$Budget->qtr3 - $Expense;
        } else {
            $finalBudget = 0;
        }
    }
}


if (isset($_POST['save']) && isset($_POST['item_category'])) {



    /*     * ********************** Add Approval Details ******************************* */
    $newApproval = new Approval();
    $newApproval->empid = $empid;
    //$newApproval->apr_id = 0;
    $title = trim($_POST['title']);
    $vendor = trim($_POST['vendor']);
    $location = trim($_POST['location']);
    if (!empty($title)) {
        $newApproval->title = $_POST['title'];
    } else {
        array_push($Errors, "Please Enter Title For An Approval.\n");
    }

    if (!empty($vendor)) {
        $newApproval->vendor = $_POST['vendor'];
    } else {
        array_push($Errors, "Please Provide Vendor Details.");
    }

    if (!empty($location)) {
        $newApproval->location = $_POST['location'];
    } else {
        array_push($Errors, "Please Provide Location Details.");
    }

    $newApproval->remark = $_POST['remark'];
    $newApproval->date = strftime("%Y-%m-%d ", time());

    /**     * ******************** Add Values to item_details. ******************************* */
    $length = count($_POST['item_category']);
    for ($i = 0; $i < $length; $i++) {
        if (!empty($_POST[$n])) {
            
        } else {
            array_push($Errors, "Please Select Atleast One Brand For Each Item.");
        }

        $n++;
    }
    if (empty($Errors)) {
        $newApproval->create();
    }

    $n = 1;

    for ($i = 0; $i < $length; $i++) {

        if (isset($_POST[$n])) {
            $newItemDetails = new ItemDetails();
            $newItemDetails->item_id = 0;

            if (!empty($_POST[$n])) {
                $newItemDetails->brand_id = implode(",", $_POST[$n]);
                $brandCount = count($_POST[$n]);
                $newItemDetails->brand_count = $brandCount;
            } else {
                array_push($Errors, "Please Select Atleast One Brand For Each Item.");
            }

            $newItemDetails->item_category = trim($_POST['item_category'][$i]);
            if (!empty($_POST['description'][$i])) {
                $newItemDetails->description = trim($_POST['description'][$i]);
            } else {
                array_push($Errors, "Please Provide An Item Description.");
            }

            $newItemDetails->quantity = trim($_POST['quantity'][$i]);
            $newItemDetails->value = trim($_POST['value'][$i]);
            $newItemDetails->amount = trim($_POST['amount'][$i]);
            $newItemDetails->apr_id = $newApproval->apr_id;
            $newItemDetails->allocated = 1;

            if (empty($Errors)) {
                $newItemDetails->create();
            }

            $n++;
        }
    }/*     * * End of for loop ** */


    if (empty($Errors)) {
        redirect_to("ApprovalSheet.php?apr_id=" . $EncryptUrl->encode($newApproval->apr_id));
    } else {
        $finalErrorList = array_unique($Errors);
    }
}

//import header file From layouts
require_once(dirname(__FILE__) . "/layouts/header.php");
?>
<div class="row">
    <div class="col-lg-3" style="background:yellow;float:right;position:fixed;top:10%;right:0;z-index:1" id="budget">
        <h4 >Your Budget:<input type="text" id="budget1" value="<?php echo $finalBudget; ?>"  readonly ></h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">

        <h1 class="page-header">Add New Approval</h1>

        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> Add New Approval
            </li>
        </ol>
    </div>
</div>
<div class="row" >
    <div class="col-lg-12" id="errors">
        <ul style='color:red;'>
            <?php
            if (!empty($Errors)) {
                foreach ($finalErrorList as $value) {
                    echo "<ul><li style='color:red;'>" . $value . "</li></ul>";
                }
            }
            ?>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12">
        <form  action="AddApproval.php" method="post" id="form1" >
            <div class="table-responsive col-lg-6 col-sm-6 form-group">
                <table class="table" >
                    <tr>
                    <div class="form-group">
                        <td style="border-top:none"> <label for="title" class=" control-label">Approval Title</label></td>
                        <td style="border-top:none">
                            <input type="text" class="form-control" name="title" id="ApprovalTitle" value="<?php
                            if (isset($_POST['title'])) {
                                echo $_POST['title'];
                            }
                            ?>" required>
                        </td>
                    </div>
                    </tr>

                    <tr>
                        <th style="border-top:none"><label for="vendor" class=" control-label">Vendor/Artist/Agency</label></th>
                        <td style="border-top:none">
                            <input type="text" class="form-control" name="vendor" id="AgencyName" value="<?php
                            if (isset($_POST['vendor'])) {
                                echo $_POST['vendor'];
                            }
                            ?>" required>
                        </td>
                    </tr>
                    <tr>  
                        <th style="border-top:none"><label for="location" class=" control-label">Delivery Location</label></th>  
                        <td style="border-top:none">
                            <input type="text" class="form-control" name="location" id="DelivaryLocation" value="<?php
                            if (isset($_POST['location'])) {
                                echo $_POST['location'];
                            }
                            ?>" required>
                        </td>
                    </tr>     
                </table>
            </div>

            <div class="table-responsive col-lg-12" >
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
                        <tr class="targetfields" id="111">
                            <td>
                                <div class="form-group">
                                    <select  class="form-control multiselect" name="1[]" multiple="multiple" id="11"  required>
                                        <?php foreach ($brands as $brand) { ?>
                                            <option  value="<?php echo $brand->brand_id; ?>"  <?php
                                            if (isset($_POST['1'])) {
                                                foreach ($_POST['1'] as $value) {
                                                    if ($value == $brand->brand_name) {
                                                        echo "selected";
                                                    }
                                                }
                                            }
                                            ?> ><?php echo $brand->brand_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control" name="item_category[]">
                                        <option>Print</option>                            
                                        <option>Gift</option>
                                        <option>E-Input</option>
                                        <option>Publisher</option>
                                        <option>Promo Services</option>
                                        <option>Miscellaneous</option>
                                    </select>
                                </div>
                            </td>
                            <td><div class="form-group"><input class="form-control itemDescription"  name="description[]" value="<?php
                                    if (isset($_POST['description'])) {
                                        echo $_POST['description'][0];
                                    }
                                    ?>" type="text" id="" required></div>
                            </td>
                            <td><input class="form-control quantity common" name="quantity[]" type="text" value="<?php
                                if (isset($_POST['quantity'])) {
                                    echo $_POST['quantity'][0];
                                }
                                ?>" >
                            </td>
                            <td><input class="form-control rate common" name="value[]"  type="text" value="<?php
                                if (isset($_POST['value'])) {
                                    echo $_POST['value'][0];
                                }
                                ?>">
                            </td>
                            <td><input class="form-control subtotal" name="amount[]" type="text"  value="<?php
                                if (isset($_POST['amount'])) {
                                    echo $_POST['amount'][0];
                                }
                                ?>" readonly >
                            </td>
                            <td><button type="button" class="btn btn-xs btn-info delete"  ><span class="glyphicon glyphicon-trash"></span></button></td>
                        </tr>
                        <tr class="targetfields" id="211">
                            <td>
                                <div class="form-group">
                                    <select  class="form-control multiselect" name="2[]" multiple="multiple" id="21"  required>
                                        <?php foreach ($brands as $brand) { ?>
                                            <option  value="<?php echo $brand->brand_id; ?>" <?php
                                            if (isset($_POST['2'])) {
                                                foreach ($_POST['2'] as $value) {
                                                    if ($value == $brand->brand_name) {
                                                        echo "selected";
                                                    }
                                                }
                                            }
                                            ?> > <?php echo $brand->brand_name; ?></option>
                                                 <?php } ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control " name="item_category[]">
                                        <option>Print</option>                            
                                        <option>Gift</option>
                                        <option>E-Input</option>
                                        <option>Publisher</option>
                                        <option>Promo Services</option>
                                        <option>Miscellaneous</option>
                                    </select>
                                </div>
                            </td>
                            <td><div class="form-group"><input class="form-control itemDescription"  name="description[]" type="text" value="<?php
                                    if (isset($_POST['description'])) {
                                        echo $_POST['description'][1];
                                    }
                                    ?>" required></div></td>
                            <td><input class="form-control quantity common" name="quantity[]" type="text" value="<?php
                                if (isset($_POST['quantity'])) {
                                    echo $_POST['quantity'][1];
                                }
                                ?>"></td>
                            <td><input class="form-control rate common" name="value[]"  type="text" value="<?php
                                if (isset($_POST['value'])) {
                                    echo $_POST['value'][1];
                                }
                                ?>"></td>
                            <td><input class="form-control subtotal" name="amount[]" type="text" value="<?php
                                if (isset($_POST['amount'])) {
                                    echo $_POST['amount'][1];
                                }
                                ?>" readonly></td>
                            <td><button type="button" class="btn btn-xs btn-info delete" ><span class="glyphicon glyphicon-trash"></span></button></td>
                        </tr>
                        <tr class="targetfields" id="311">
                            <td>
                                <div class="form-group">
                                    <select class="form-control multiselect" name="3[]" multiple="multiple" id="31"  required>
                                        <?php foreach ($brands as $brand) { ?>
                                            <option  value="<?php echo $brand->brand_id; ?>" <?php
                                            if (isset($_POST['3'])) {
                                                foreach ($_POST['3'] as $value) {
                                                    if ($value == $brand->brand_name) {
                                                        echo "selected";
                                                    }
                                                }
                                            }
                                            ?> > <?php echo $brand->brand_name; ?></option>
                                                 <?php } ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control" name="item_category[]">
                                        <option>Print</option>                            
                                        <option>Gift</option>
                                        <option>E-Input</option>
                                        <option>Publisher</option>
                                        <option>Promo Services</option>
                                        <option>Miscellaneous</option>
                                    </select>
                                </div>
                            </td>

                            <td><div class="form-group"><input class="form-control itemDescription"  name="description[]" value="<?php
                                    if (isset($_POST['description'])) {
                                        echo $_POST['description'][2];
                                    }
                                    ?>" type="text"  required></div></td>
                            <td><input class="form-control quantity common" name="quantity[]" type="text" value="<?php
                                if (isset($_POST['quantity'])) {
                                    echo $_POST['quantity'][2];
                                }
                                ?>"></td>
                            <td><input class="form-control rate common" name="value[]"  type="text" value="<?php
                                if (isset($_POST['value'])) {
                                    echo $_POST['value'][2];
                                }
                                ?>" ></td>
                            <td><input class="form-control subtotal" name="amount[]" type="text" value="<?php
                                if (isset($_POST['amount'])) {
                                    echo $_POST['amount'][2];
                                }
                                ?>" readonly></td>
                            <td><button type="button" class="btn btn-xs btn-info delete"  ><span class="glyphicon glyphicon-trash"></span></button></td>
                        </tr>
                </table>
                <table class="table table-bordered">
                    <tr>
                    <button type="button" class="btn btn-link" onclick="addRow()" >Add Rows</button>        
                    <span id="add" style="display:none"><img src="images/loader3.gif"></span>
                    </tr>
                    <tr>
                        <td colspan="7"><strong>Total :</strong><span id="total"></span></td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div class="form-group">
                                <label>Remarks</label>
                                <input type="hidden" name="multiselectValues" value="1,2,3">
                                <textarea class="form-control" rows="3" name="remark"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div class="form-group"><button type="submit" class="btn btn-primary" name="save" id="SaveApproval"  onClick="return validate();">Save And Print</button></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>        
        </form>
    </div>
</div>

<script src="js/addNewRow.js" type="text/javascript"></script>
<script>
                                jQuery(function () {

                                    var typingTimer;                //timer identifier
                                    var doneTypingInterval = 1000;
                                    //function for calculating total and GraandTotal 
                                    jQuery("#items ").delegate('.common ', 'keyup', function () {
                                        var total = 0;
                                        jQuery("#items .targetfields").each(function () {
                                            //get the  values of quantity and rate 
                                            var qty = parseFloat(jQuery(this).find(".quantity").val()) || 0;
                                            var rate = parseFloat(jQuery(this).find(".rate").val()) || 0;

                                            //calculate subtotal
                                            var subtotal = qty * rate;

                                            jQuery(this).find(".subtotal").val(subtotal.toFixed(2));
                                            //calculate GrandTotal
                                            if (!isNaN(subtotal))
                                                total += subtotal;
                                        });

                                        //Display final total
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
    function validate() {
        var orderQuanitity = $("#budget1").val();
        if (orderQuanitity < 0) {

            alert("Balance is less than zero");
            return false;
        }
    }

    function initializeBalance() {
        var jsonString = '';
        $.ajax({
            //Send request
            type: 'POST',
            data: {data: jsonString},
            url: 'initializeTempBudget.php',
            success: function (data) {
                ajaxRequest();
            }
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