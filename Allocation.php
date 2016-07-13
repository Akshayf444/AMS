<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$ItemList = array();

$copyFromFlag = false;
$CopyFromItemid = '';
$errors = array();

$grandTotal = 0;
if (isset($_GET['apr_id'])) {
    /*     * ** Collect Approval Id *** */
    $apr_id = $_GET['apr_id'];

    /*     * ** Collect List Of Items From Approvals *** */
    $Items2 = ItemDetails::find_by_apr_id($apr_id);
    $Approval = Approval::find_by_apr_id($apr_id);
    $equalItems = ItemDetails::find_equal_items($apr_id);
} else {
    $Items = array();
    $Approval = array();
    $equalItems = array();
}

/* * ** Find Division For Current Employee According To His GPM ** */
$division = GPM::find_division1($empName->gpm_empid);
$Manpowers = Manpower::find_by_division($division);



if (isset($_POST['save']) && isset($_GET['apr_id'])) {

    if (isset($_POST['alloc_id'])) {
        /*         * ** Edit Allocation Details *** */
        $newAllocation = new AllocationDetails();
        $newAllocation->item_id = $_POST['item_id'];
        $newAllocation->qty_per_person = $_POST['qty_per_person'];

        $length = count($_POST['depot_id']);
        for ($i = 0; $i < $length; $i++) {
            $newAllocation->alloc_id = $_POST['alloc_id'][$i];
            $newAllocation->depot_id = $_POST['depot_id'][$i];
            $newAllocation->region_id = $_POST['region_id'][$i];
            $newAllocation->no_of_persons = $_POST['no_of_persons'][$i];
            $newAllocation->total_quantity = $_POST['total'][$i];
            $newAllocation->update($newAllocation->alloc_id);
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
            $updateItem = new ItemDetails();
            $updateItem->update_alloc_status($newAllocation->item_id, $token);
            //$newApproval->update_PRE_status();
            if ($token == 1) {
                $sendMail = Employee::sendAllocationCopy($newAllocation->item_id, $empName->gpm_empid);
            }
        }
        $_SESSION['Error'] = 'Updated Successfully';


        redirect_to("Allocation.php?apr_id=" . $_GET['apr_id']);
    } else {

        /*         * * ** Add Allocation Details *** * */
        $newAllocation = new AllocationDetails();
        $newAllocation->item_id = $_POST['item_id'];
        $newAllocation->qty_per_person = $_POST['qty_per_person'];

        $length = count($_POST['depot_id']);
        for ($i = 0; $i < $length; $i++) {
            $newAllocation->alloc_id = 0;
            $newAllocation->depot_id = $_POST['depot_id'][$i];
            $newAllocation->region_id = $_POST['region_id'][$i];
            $newAllocation->no_of_persons = $_POST['no_of_persons'][$i];
            if (!empty($_POST['total'])) {
                $newAllocation->total_quantity = $_POST['total'][$i];
            } else {
                array_push($errors, "Enter total");
            }

            $newAllocation->create();
        }/** End Of for loop ** */
        if (empty($errors)) {
            $_SESSION['Error'] = 'Details Added Successfully';
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
            $updateItem = new ItemDetails();
            $updateItem->update_alloc_status($newAllocation->item_id, $token);
            //$newApproval->update_PRE_status();
            if ($token == 1) {
                $sendMail = Employee::sendAllocationCopy($newAllocation->item_id, $empName->gpm_empid);
            }
        }
        redirect_to("Allocation.php?apr_id=" . $_GET['apr_id']);
    }
}


/* * ** Copy Details Of Allocation To Another Item *** */
if (isset($_POST['copy_to_all'])) {
    if (isset($_POST['copyFrom'])) {

        /*         * ** Item From Which We Are Going To Copy Allocation Details *** */
        $ItemsToCopy = AllocationDetails::find_by_item_id2($_POST['copyFrom']);

        if (!empty($_POST['item_id'])) {
            $length = count($_POST['item_id']);
            for ($i = 0; $i < $length; $i++) {

                $item_id = $_POST['item_id'][$i];
                $newAllocation = new AllocationDetails();
                $newAllocation->item_id = $item_id;

                /*                 * ** Check Whether Item Details Already Exist For Editing *** */
                $foundItem = AllocationDetails::find_by_item_id2($item_id);

                if (!empty($foundItem)) {
                    foreach ($ItemsToCopy as $Items) {
                        $newAllocation->qty_per_person = $Items->qty_per_person;
                        $newAllocation->depot_id = $Items->depot_id;
                        $newAllocation->region_id = $Items->region_id;

                        /*                         * * Find Allocation Id For Updating Details *** */
                        $AllocationId = AllocationDetails::find_alloc_id($item_id, $newAllocation->region_id);
                        if (!empty($AllocationId)) {
                            $newAllocation->alloc_id = $AllocationId->alloc_id;
                        }
                        $newAllocation->no_of_persons = $Items->no_of_persons;
                        $newAllocation->total_quantity = $Items->total_quantity;

                        /**                         * * Update Details *** */
                        $newAllocation->update($newAllocation->alloc_id);
                        $_SESSION['Error'] = 'Updated Successfully';
                    }
                } else {
                    /**                     * * Create New Allocation *** */
                    foreach ($ItemsToCopy as $Items) {
                        $newAllocation->qty_per_person = $Items->qty_per_person;
                        $newAllocation->alloc_id = 0;
                        $newAllocation->depot_id = $Items->depot_id;
                        $newAllocation->region_id = $Items->region_id;
                        $newAllocation->no_of_persons = $Items->no_of_persons;
                        $newAllocation->total_quantity = $Items->total_quantity;
                        $newAllocation->create();
                        $_SESSION['Error'] = 'Added Successfully';
                    }
                }
            }
        } else {
            array_push($errors, "Selected Item Don't Have Anything To Copy.");
            $_SESSION['Error'] = 'Selected Item Dont Have Anything To Copy';
            redirect_to("Allocation.php?apr_id=" . $_GET['apr_id']);
        }
    } else {
        $_SESSION['Error'] = 'Dont Have Anything To Copy';
    }

    if (empty($errors)) {
        //$_SESSION['Error'] ='Copied Successfully';
    }
}



require_once(dirname(__FILE__) . "/layouts/header.php");
?>
<!-- Page Heading -->

<div class="row" id="Budget">
    <div class="col-lg-3 col-sm-6 col-md-4 col-xs-6" style="background:#fff;float:right;position:fixed;top:10%;right:0;z-index:1;outline:1px solid #ddd" id="budget">
        <table class="table " id="items">
            <tr>
                <td style="border-top:none;width:50%"><strong>Ordered Quantity</strong></td>
                <td style="border-top:none"><input type="text" class="form-control" id="order" value="0" readonly></td> 
            </tr>
            <tr>
                <td style="border-top:none;width:50%"><strong>Total</strong></td>
                <td style="border-top:none"><input type="text" class="form-control" id="total1" value="" readonly></td>
            </tr>
            <tr>
                <td style="width:50%"><strong>Balance At CWH</strong></td>
                <td><span id="balance"></span></td> 
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?php
            //Approval title 
            foreach ($Approval as $value) {
                ?>
                Allocation for <?php echo $value->title; ?> 
            <?php } ?>
        </h1>

        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <i class="fa fa-dashboard"></i> Allocation
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->
<div id="errors" class="row">
    <div class="col-lg-12">
        <ul style="color:red;font-weight:bold;list-style-type:none">
            <?php
            foreach ($errors as $value) {
                echo "<li>" . $value . "</li>";
            }
            ?>
            <?php
            if (isset($_SESSION['Error'])) {
                echo "<li>" . $_SESSION['Error'] . "</li>";
                unset($_SESSION['Error']);
            }
            ?>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 ">

        <form action="Allocation.php?apr_id=<?php echo $_GET['apr_id']; ?>" method="post" id="form1">

            <div class="table-responsive ">
                <table class="table" style="width:40%">
                    <tr>
                        <th style="border-top:none"> <label  class=" control-label">Select Item</label></th>
                        <td style="border-top:none">

                            <select class="form-control" name="item_id" onchange="Search()" id="item_value">
                                <option value="">Select Item</option>
                                <?php foreach ($Items2 as $Item1) { ?>
                                    <option value="<?php echo trim($Item1->item_id); ?>"><?php echo trim($Item1->description); ?></option>
                                <?php } ?>
                            </select>

                        </td>
                        <td style="border-top:none">
                            <span id="add" style="display:none"><img src="images/loader3.gif"></span>
                        </td>

                    </tr>
                    <tr>
                        <th style="border-top:none"><label  class=" control-label">Quantity/Person</label></th>
                        <td style="border-top:none">
                            <input type="text" class="form-control" id="quantity" name="qty_per_person" required >
                        </td>
                    </tr>     
                </table>
            </div>
            <div class="form1">
                <div class="table-responsive col-lg-9 " >
                    <table class="table table-bordered table-hover table-striped" id="items">
                        <thead>
                            <tr>
                                <th>Depot</th>
                                <th>Region</th>
                                <th>Approved Manpower</th>
                                <th>Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody
                        <?php foreach ($Manpowers as $Manpower) { ?>

                                <tr class="targetfields" >
                                    <td>
                                        <div class="form-group">
                                            <label><?php
                                                $regionName = Region::find_by_region_id($Manpower->region_id);
                                                $depotName = Depot::find_by_depot_code($regionName->depot_id);
                                                if (isset($depotName->depot_name)) {
                                                    echo $depotName->depot_name;
                                                } else {
                                                    echo 'NA';
                                                }
                                                ?></label> 
                                            <input type="hidden" name="depot_id[]" value="<?php
                                            if (isset($depotName->depot_id)) {
                                                echo $depotName->depot_id;
                                            } else {
                                                echo 'NA';
                                            }
                                            ?>">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <label><?php echo $regionName->region_name; ?></label>
                                            <input type="hidden" name="region_id[]" value="<?php echo $regionName->region_id; ?>">
                                        </div>
                                    </td>


                                    <td><input class="form-control rate" value="<?php
                                        echo $Manpower->no_of_persons;
                                        $grandTotal+=$Manpower->no_of_persons;
                                        ?>"  name="no_of_persons[]" type="text" readonly="readonly"></td>
                                    <td><input class="form-control subtotal"  name="total[]" type="text" value="" ></td>
                                </tr>

                            <?php } ?>
                            <tr>
                                <td colspan="2"><strong>Total</strong></td>
                                <td><?php
                                    echo $grandTotal;
                                    $grandTotal = 0;
                                    ?></td>
                                <td><input type="text" class="form-control" id="total" value="" readonly></td> 
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <button type="submit" class="btn btn-primary" id="save" name="save"  onClick="return validate();">Save</button>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" id="copy" >Copy To All</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 

        </form>

    </div>
</div>

<div class="col-lg-8">
    <form action="Allocation.php?apr_id=<?php echo $_GET['apr_id']; ?>" method="post">
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <div id="displayHeading"></div>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                    </tr>
                                    <?php
                                    if (!empty($equalItems)) {
                                        foreach ($equalItems as $Item) {
                                            ?>
                                            <tr>
                                                <td><input type="checkbox" name="item_id[]" value="<?php echo $Item->item_id; ?>"></td>
                                                <td><?php echo $Item->description; ?></td>
                                                <td><?php echo $Item->quantity; ?></td>
                                            </tr>    
                                            <?php
                                            //End Of If.....
                                        } //End Of For
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="copy_to_all" onClick="return validate();">Copy</button>
                    </div>
                    </form>
                </div><!--End Of Model Pop up-->
            </div>

            <div class="modal hide fade" id="stop"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <div id="displayHeading">STOP</div>
                        </div>
                        <div class="modal-body">
                            </h1>STOP PLEASE</h1>
                        </div>
                    </div>
                </div>
            </div><!--End Of Model stop-->

            <div class="modal fade" id="cwh"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <div id="displayHeading"></div>
                        </div>
                        <div class="modal-body">
                            </h1>STOP PLEASE</h1>
                        </div>
                    </div>                        
                </div>
            </div><!--End Of Model  CWH-->

            <script src="js/jquery.validate.min.js"></script>

            <script>
                            function Search() {
                                $("#add").show();
                                $("#save").show();

                                var search_term = $("#item_value").val();
                                var search_term2 = $("#item_value").val();
                                $.post('getTotalAmount.php', {search_term: search_term}, function (data) {

                                    $("#quantity").val(data);
                                });

                                $.post('getItemDetails.php', {search_term: search_term}, function (data) {
                                    var data1 = data;
                                    if (data1 == null || data1 == "") {
                                        alert('Allocation Details Not Found For This Item');
                                    } else {
                                        $(".form1").html(data1);
                                        $.post('getTotalAmount.php', {search_term2: search_term2}, function (data) {
                                            $("#displayHeading").html(data);
                                            //alert(data);
                                        });
                                    }
                                });

                                $.post('getItemDetails.php', {search_term2: search_term2}, function (data) {
                                    var data1 = data;
                                    if (data1 == null || data1 == "" || data1 == 0) {

                                    } else {
                                        //alert(data1);
                                        $("#Budget").html(data1);
                                    }
                                    hide();
                                });




                            }
                            function hide() {

                                $("#add").hide();
                            }

            </script>

            <script>
                $(document).ready(function () {
                    $("#save").css("display", "none");
                    $('#item_value').change(function () {
                        if ($(this).val() == '') {
                            $('#save').css({'display': 'none'});
                        } else {
                            $('#save').show();
                        }
                    });
                });
            </script>

            <script>
                jQuery(function () {
                    calculate();
                });
            </script>

            <script>
                // When the browser is ready...
                $(function () {

                    // Setup form validation on the #register-form element
                    $("#form1").validate({
                        // Specify the validation rules
                        rules: {
                            quantity: "required"
                        },
                        // Specify the validation error messages
                        messages: {
                            quantity: "Please Enter Quantity"
                        },
                        submitHandler: function (form) {
                            form.submit();
                        }
                    });

                });

            </script>

            <script>
                function validate() {
                    var orderQuanitiy = $("#order").val();
                    var total = $("#total1").val();
                    var balance = orderQuanitiy - total;
                    var minimumBalance = (orderQuanitiy * 30) / 100;

                    if (balance < 0) {
                        //$('#cwh').modal({ keyboard: true });
                        //$('#cwh').modal('show'); 
                        alert("Balance At CWH cannot be less than zero");
                        //$("#stop").show();
                        $("#copyToAll").hide();
                        return false;
                    }
                    if (balance > minimumBalance) {
                        var r = confirm("Balance At CWH more than 30% of Ordered Quantity. Do you still want to proceed?");
                        if (r == true) {
                            $('#form1').append('<input type="hidden" name="token" value="1">')
                            //return false;
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        $('#form1').append('<input type="hidden" name="token" value="0">')
                        return true;
                    }
                }

                function calculate() {
                    jQuery("#quantity").keyup(function (event) {
                        var total = 0;
                        var qty = parseInt(jQuery("#quantity").val()) || 0;
                        jQuery("#items .targetfields").each(function () {
                            var rate = parseInt(jQuery(this).find(".rate").val()) || 0;
                            var subtotal = qty * rate;
                            jQuery(this).find(".subtotal").val(subtotal);
                            if (!isNaN(subtotal))
                                total += subtotal;
                        });
                        var order = jQuery("#order").val();
                        var balance = order - total;

                        jQuery("#total").val(total.toFixed(2));
                        jQuery("#total1").val(total.toFixed(2));
                        jQuery("#balance").html(balance.toFixed(2));
                    });

                    jQuery(".subtotal").keyup(function (event) {
                        var total = 0;
                        jQuery("#items .targetfields").each(function () {
                            var subtotal = parseInt(jQuery(this).find(".subtotal").val());

                            if (!isNaN(subtotal))
                                total += subtotal;
                        });
                        var order = jQuery("#order").val();
                        var balance = order - total;

                        jQuery("#total").val(total.toFixed(2));
                        jQuery("#total1").val(total.toFixed(2));
                        jQuery("#balance").html(balance.toFixed(2));

                    });

                    jQuery(".rate").keyup(function (event) {
                        var total = 0;
                        var qty = parseInt(jQuery("#quantity").val()) || 0;
                        jQuery("#items .targetfields").each(function () {
                            var rate = parseInt(jQuery(this).find(".rate").val()) || 0;
                            var subtotal = qty * rate;
                            jQuery(this).find(".subtotal").val(subtotal);
                            if (!isNaN(subtotal))
                                total += subtotal;
                        });
                        var order = jQuery("#order").val();
                        var balance = order - total;

                        jQuery("#total").val(total.toFixed(2));
                        jQuery("#total1").val(total.toFixed(2));
                        jQuery("#balance").html(balance.toFixed(2));
                    });

                }
            </script>

            <script>
                /*jQuery(function() {
                 
                 var total = 0;
                 var qty = parseInt(jQuery("#quantity").val())||0;
                 jQuery("#items .targetfields").each(function() {
                 var rate = parseInt(jQuery(this).find(".rate").val())||0;
                 var subtotal = qty * rate;
                 //jQuery(this).find(".subtotal").val(subtotal);
                 if(!isNaN(subtotal))
                 total+=subtotal;
                 });
                 var order = jQuery("#order").val();
                 var balance = order - total;
                 
                 // console.log(order);
                 //console.log(balance);
                 
                 jQuery("#total").html(total.toFixed(2));
                 jQuery("#total1").html(total.toFixed(2));
                 jQuery("#balance").html(balance.toFixed(2));
                 
                 });*/
            </script>

            <?php require_once(dirname(__FILE__) . "/layouts/footer.php"); ?>