<?php
session_start();
if (!isset($_SESSION['CWH'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$active = true;
$Errors = array();
$GRNIds = array();
$PMTId = '';
$pageTitle = 'Search GRN';
if (isset($_POST['submit'])) {
    //  Creates new object of GRN

    $length = count($_POST['item_id']);

    for ($i = 0; $i < $length; $i++) {
        $newGRN = new GRN();
        $newGRN->item_id = $_POST['item_id'][$i];
        /*         * ** *** Collect PMT-Id For Sending mail ********* */
        $Itemdetail = Itemdetails::find_by_item_id($newGRN->item_id);
        $Approval = Approval::find_by_apr_id2($Itemdetail->apr_id);
        $PMTId = $Approval->empid;

        $newGRN->apr_id = $_POST['apr_id'][$i];
        $newGRN->date = date("Y-m-d H:i:s");
        if (!empty($_POST['grn_date'][$i])) {
            if (preg_match("/^(\d{1,2})\-(\d{1,2})\-(\d{4})$/", $_POST['grn_date'][$i])) {

                $dtA = new DateTime(strftime("%d-%m-%Y ", time()));
                $dtB = new DateTime($_POST['grn_date'][$i]);

                $newGRN->grn_date = date("Y-m-d", strtotime($_POST['grn_date'][$i]));
            } else {
                array_push($Errors, "Please Enter Valid Date Format.");
            }

            $dtA = new DateTime(strftime("%Y-%m-%d ", time()));
            $dtB = new DateTime($_POST['grn_date'][$i]);
        } else {
            array_push($Errors, "Please Enter GRN Date");
        }


        if (!empty($_POST['quantity_received'][$i])) {
            $newGRN->quantity_received = $_POST['quantity_received'][$i];
        } else {
            $newGRN->quantity_received = 0;
        }

        /* if (!empty($_POST['quantity_remaining'][$i])) {
          $newGRN->quantity_remaining = $_POST['quantity_remaining'][$i];
          } */

        if (empty($Errors)) {
            $receivedTillDate = GRN::find_received_quantity($newGRN->item_id);
            $isDelivered = ItemDetails::find_by_item_id($newGRN->item_id);
            if ($receivedTillDate == $isDelivered->quantity) {
                
            } else {
                $newGRN->grn_id = 0;
                $newGRN->create();
            }
            array_push($GRNIds, $newGRN->item_id);
        }
    }//End of loop

    if (empty($Errors)) {
        $_SESSION['Error'] = '<div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Success !!!!</strong> 
        </div>';

        $sendMail = GRN::sendmail($GRNIds, $PMTId);
        $result = $sendSMS = GRN::sendSMS($GRNIds, $PMTId);
        //redirect_to("PREsearchGRN.php");
    } else {
        $finalErrorList = array_unique($Errors);
    }
}
require_once(dirname(__FILE__) . "/layouts/CWHlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12">
        <h1 class="page-header">
            Take GRN
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-list"></i> Take GRN
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
    
        <?php
        if (!empty($Errors)) {
            foreach ($finalErrorList as $value) {
                echo "<li style='color:red;'>" . $value . "</li>";
            }
        }
        if (isset($_SESSION['Error'])) {
            echo $_SESSION['Error'];
            unset($_SESSION['Error']);
        }
        ?>
    
    </div>
</div>
<div class="row" style="margin-bottom:1em;">
    <div class="">
        <div class="col-lg-4 pull-center" >
            <div class="form-group input-group ">
                <input type="text"  class="form-control" placeholder="Key No. Or PO No. Or Approval Id" id="search"/>
                <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
            </div>
        </div>
        <div class="col-lg-3" id="loader" style="display:none">
            <img src="images/loader2.gif">
        </div>
    </div>
</div>
<div class="row" style="margin-bottom:1em;">
    <div class="col-lg-12 table-responsive">
        <form action="CWHsearchGRN.php" method="post">
            <div class="searchResult ">

            </div>

        </form>
    </div>
</div>
<div class="mask" style="display:none"></div>

<script>
    jQuery(function () {
        $("#save").hide();
        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;
        jQuery("#search ").keyup(function () {
            clearTimeout(typingTimer);
            if ($(this).val) {
                $(".mask").show();
                typingTimer = setTimeout(function () {
                    if ($("#search").val().length <= 6) {

                        var search_term = $("#search").val();
                        var dataString = 'search_term=' + search_term + '&approval=true';
                        sendRequest(dataString);
                    }

                    if ($("#search").val().length == 8) {

                        var search_term = $("#search").val();
                        var dataString = 'search_term=' + search_term + '&PR=true';
                        sendRequest(dataString);

                    }

                    if ($("#search").val().length == 12) {

                        var search_term = $("#search").val();
                        var dataString = 'search_term=' + search_term + '&KeyNo=true';
                        sendRequest(dataString);
                    }

                }, doneTypingInterval);
            }
            $(".mask").hide();
        });
    });

    function sendRequest(dataString) {
        $("#loader").show();
        var data = dataString;

        $.ajax({
            //Send request
            type: 'POST',
            data: data,
            url: 'CWHgetGRN.php',
            success: function (data) {
                $(".searchResult").html(data);
                $("#loader").hide();
            }
        });
    }
</script>

<script src="js/dateValidator.js"></script>
<script src="js/bootstrapDropdown.js"></script>

<?php require_once(dirname(__FILE__) . "/layouts/CWHlayouts/footer.php"); ?>