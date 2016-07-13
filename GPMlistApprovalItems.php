<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);

if (isset($_GET['apr_id'])) {
    $apr_id = $_GET['apr_id'];
    $Items = ItemDetails::find_by_apr_id($apr_id);
    
} else {
    redirect_to("GPMallocationList.php");
}
require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Item Details
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Item Details
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered table-hover table-striped" >
            <tr>
                <th>Key No</th>
                <th>Item Description </th>
                <th>Receipt Date</th>
                <th>Action</th>
            </tr>
            <?php
            foreach ($Items as $Item) {


              ?>
            <tr>
                <td> <?php $PRdetail=PrDetails::find_by_item_id($Item->item_id);
                if(!empty($PRdetail)){ echo $PRdetail->key_no; }else{ echo '-'; }?>
                </td>
                <td><?php echo $Item->description ; ?></td>
                <td><?php $receivedQuantity =GRN::find_by_item_id2($Item->item_id); 
                            if(!empty($receivedQuantity)){
                                    echo date('d-m-Y',strtotime($receivedQuantity->date));
                            }else{
                                    echo '-';
                            } ?>
                </td>
                <td><a href="GPMprintAllocation.php?item_id=<?php echo $Item->item_id; ?>"><input type="button" value="View" class="btn btn-primary btn-xs"></a></td>
            </tr>
            
          <?php }  ?>
        </table>
    </div>
</div>