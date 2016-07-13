<?php
session_start();
if (!isset($_SESSION['PRE'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$Approvals = Approval::find_by_status2('Approved');
//Array For Collecting Form Errors
$Errors = array();
$finalErrorList = '';

$division = '';
$EncryptUrl = new Encryption();

/* if(isset($_POST['save'])){
  $newPODetails =new PoDetails();
  $newPODetails->po_id = $newPODetails->autoGenerate_id();
  $newPODetails->apr_id = $_POST['approvalid'];
  $Approvals = Approval::find_by_apr_id($newPODetails->apr_id);

  $length = count($_POST['po_date']);
  for ($i=0; $i < $length ; $i++) {

  if(!empty($_POST['po_date'][$i])){
  if (preg_match("/^(\d{1,2})\-(\d{1,2})\-(\d{4})$/", $_POST['po_date'][$i])) {
  $dtA = new DateTime(strftime("%d-%m-%Y ", time()));
  $dtB = new DateTime($_POST['po_date'][$i]);
  if ($dtB < $dtA) {
  array_push($Errors,"Cannot Use Past Date");
  }else{
  $newPODetails->po_date = date("Y-m-d",strtotime($_POST['po_date'][$i]));
  }

  }else{
  array_push($Errors, "Please Enter Valid Date Format.");
  }

  }else{
  array_push($Errors, "Please Enter PO Date");
  }

  if(!empty($_POST['po_no'][$i])){
  $newPODetails->po_no = trim($_POST['po_no'][$i]);
  }else{
  array_push($Errors, "Please Enter PO No");
  }

  if(!empty($_POST['cost'][$i])){
  $newPODetails->cost = trim($_POST['cost'][$i]);
  }else{
  array_push($Errors, "Please Enter Cost");
  }

  $newPODetails->date = strftime("%Y-%m-%d ", time());

  if (empty($Errors)) {
  $newPODetails->create();

  }else{
  array_push($Errors, "Error Occured.");
  }

  }
  if(empty($Errors)){
  $column_name="order_status";
  $status = "ordered" ;
  $UpdateOrderStatus = new Approval();
  foreach ($Approvals as $value) {
  $UpdateOrderStatus->update_PRE_status($value->apr_id,'False','process_for_po');
  $UpdateOrderStatus->update_PRE_status($value->apr_id,$status,$column_name);
  }
  redirect_to("PRElistApproval.php");
  }else{
  $finalErrorList = array_unique($Errors);
  }
  } */

require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            List Of Approvals
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> List Of Approvals
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
    if (isset($_SESSION['Error'])) {
        echo $_SESSION['Error'];
        unset($_SESSION['Error']);
    }
    ?>
</div>
<div class="row" style="margin-bottom:2em;">
    <div class="col-lg-4" >
        <form class="navbar-form navbar-left" role="search" method="post" action ="PREsearchResult.php">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Search" id="apr_id" autocomplete="off" name="apr_id" >
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                <div class="resultDropdown col-lg-6" >
                    <ul class="searchresult1" style="list-style-type:none"></ul>
                </div>
            </div>

        </form>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Approval Id</th>
                        <th>Title Of Approval</th>
                        <th>Brand</th>
                        <th>Division</th>
                        <th>Requision By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                            <?php
                            if (!empty($Approvals)) {
                                foreach ($Approvals as $Approval) {
                                    ?>
                            <tr>

                                <td><?php echo $Approval->apr_id; ?></td>
                                <td><?php echo $Approval->title; ?></td>
                                <td><?php
                                    $dropdown = ItemDetails::brandDropdown($Approval->apr_id, 1);
                                    echo $dropdown;
                                    ?>
                                </td>
                                <td><?php
                            $Empid = Approval::find_by_apr_id2($Approval->apr_id);
                            $empName = Employee::find_by_empid($Empid->empid);
                            $division = GPM::find_division1($empName->gpm_empid);
                            $divisionName = Division::find_by_div_id($division);
                            echo $divisionName->div_name;
                                    ?>
                                </td>

                                <td><?php $PMT = Employee::find_by_empid($Approval->empid);
                            echo $PMT->name; ?></td>

                                <td><?php echo date('d-m-Y', strtotime($Approval->date)); ?></td>

                                <td><?php
                            $status = Approval::approvalStatus($Approval->apr_id);
                            $finalStatus = join(",", $status);
                            echo $finalStatus;
                            ?></td>

        <?php $POdetails = PoDetails::proceed($Approval->apr_id);
        if ($Approval->receive == "received") {
            ?>

                                    <td>
                                        <a href="PREpodetails.php?apr_id=<?php echo $EncryptUrl->encode($Approval->apr_id); ?>">
                                            <button type="button" class="btn btn-xs btn-info PREapproval" style="width:100px">PO Details
                                            </button>
                                        </a>
                                    </td>

        <?php } elseif ($POdetails == true || $Approval->process_for_po == "processed") { ?>

                                    <td>
                                        <a href="PREpodetails.php?apr_id=<?php echo $EncryptUrl->encode($Approval->apr_id); ?>">
                                            <button type="button" class="btn btn-xs btn-info PREapproval dialog"  style="width:100px" >PO Details
                                            </button></a>
                                    </td>

        <?php } else { ?>

                                    <td>

                                        <a href="PREprocessForPR.php?apr_id=<?php echo $EncryptUrl->encode($Approval->apr_id); ?>">
                                            <button type="button" class="btn btn-xs btn-success PREapproval" style="width:100px">Process For PR
                                            </button>
                                        </a>
                                    </td>

        <?php } ?>
                            </tr>

    <?php }
} ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>                <!-- /.row -->
<script src="js/jquery-1.11.0.js"></script>
<script src="js/bootstrapDropdown.js"></script>

<script>
    jQuery(function () {
        $(".searchresult1").hide();
        jQuery("#apr_id").keyup(function () {
            var search_term = $(this).val().trim();
            $(".searchresult1").css("background", " url('images/loader.gif') no-repeat scroll center center #fff");
            $.post('PREsearchApproval.php', {search_term: search_term}, function (data) {
                $(".searchresult1").css('background', '#fff');
                var data1 = data;
                if (data1 == null || data1 == "") {
                    $(".searchresult1").html('');
                    $(".searchresult1").css('border', '0');
                } else {

                    $(".searchresult1").css('border', '0.2em #ddd solid');

                    $(".searchresult1").show();
                    $(".searchresult1").html(data);

                    $(".searchresult1 li").click(function () {
                        var result_value = $(this).text();
                        $("#apr_id").val(result_value);
                        $(".searchresult1").html('');
                        $(".searchresult1").css('border', '0');
                    });//End of click function

                }//end of else..
            });
        });//End Of Key Up function
    });
</script>

<script>
    $(document).on("click", ".dialog", function () {
        var myBookId = $(this).data('id');
        $(".modal-body #apr_id").val(myBookId);
    });
</script>
<script src="js/IEbuttonPatch.js"></script>
<?php require_once(dirname(__FILE__) . "/layouts/PRElayouts/footer.php"); ?>