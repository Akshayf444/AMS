<?php
require_once(dirname(__FILE__) . "/MMheader.php");

$Regions = explode(",", $empName->region);
$Divisions = explode(",", $empName->division);
$TMlist = TM::find_by_sm_empid($empName->sm_empid);
if (isset($_POST['region'])) {

    $region_id = trim($_POST['region']);
    $_SESSION['region'] = $region_id;
    $AllocationList = AllocationDetails::ready_for_dispatch($region_id);

    $tmcount = TM::count_by_sm_empid($empName->sm_empid);
}

if (isset($_POST['dispatch'])) {

    //var_dump($_POST);
    $newDispatchDetails = new DispatchDetails();
    foreach ($TMlist as $Employee) {
        if (isset($_POST[$Employee->tm_empid])) {
            if (!empty($_POST[$Employee->tm_empid])) {
                $ItemList = $_POST['item_id'];
                foreach ($_POST[$Employee->tm_empid] as $value) {
                    $newDispatchDetails->tm_empid = $Employee->tm_empid;
                    $newDispatchDetails->allocated_value = $value;
                    $newDispatchDetails->item_id = array_shift($ItemList);
                    $newDispatchDetails->date = date('Y-m-d H:i:s', time());
                    $newDispatchDetails->region_id = $_SESSION['region'];
                    $found_dispatch_entry = DispatchDetails::find_dispatch_entry($newDispatchDetails->tm_empid, $newDispatchDetails->item_id);
                    if ($found_dispatch_entry == FALSE) {
                        $newDispatchDetails->dis_id = $found_dispatch_entry->dis_id;
                        $newDispatchDetails->update();
                    } else {
                        $newDispatchDetails->create();
                    }
                }
            }
        }
    }
    
    echo '<script>window.location ="SMindex.php";</script> ';
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Dispatch
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="SMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Dispatch
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="row" style="margin-bottom:1em;">
            <form action="" method="post">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
                    <label>Region</label>

                    <select class="form-control" name="region" onchange="this.form.submit()">
                        <option value="none" >--- SELECT REGION ---</option>
                        <?php
                        if (!empty($Regions)) {
                            foreach ($Regions as $Region) {
                                $RegionName = Region::find_by_region_id($Region);
                                ?>
                                <option value="<?php echo $RegionName->region_id; ?>"  <?php
                                if (isset($_POST['region']) && $_POST['region'] == $RegionName->region_id) {
                                    echo "SELECTED";
                                }
                                ?>><?php echo $RegionName->region_name; ?></option>   
                                        <?php
                                    }
                                }
                                ?>

                    </select>
                </div>
            </form>
            <!--            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
                            <label>Division</label>
            
                            <select class="form-control" name="division">
            <?php
//                    if (!empty($Divisions)) {
//                        foreach ($Divisions as $Division) {
//                            
            ?>
                                        <option value="//<?php //echo $GPM->gpm_empid;                       ?>"><?php //echo $GPM->name;                       ?></option>   
                                        //<?php
//                        }
//                    }
            ?>
            
                            </select>
                        </div>-->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form action="" method="post">
            <div class="table-responsive" style="margin-top:2em">
                <table class="table table-bordered table-hover table-striped" >
                    <thead>
                        <tr>
                            <th>Key No.</th>
                            <th>Item Description</th>
                            <th>
                                Quantity
                                <input type="hidden" name="region_id" value="<?php
                                if (isset($_POST['region'])) {
                                    echo $_POST['region'];
                                }
                                ?>">
                            </th>
                            <?php
                            if (!empty($TMlist)) {
                                foreach ($TMlist as $Employee) {
                                    ?>
                                    <th><?php echo $Employee->tm_name; ?></th>

                                    <?php
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($AllocationList)) {
                            foreach ($AllocationList as $Allocation) {


                                $roundFigure = 0;
                                $SingleAllocation = AllocationDetails::find_by_alloc_id($Allocation);
                                $DispatchEntryExist = DispatchDetails::entryExist($SingleAllocation->item_id, $_SESSION['region']);



                                $ItemDetail = ItemDetails::find_by_item_id($SingleAllocation->item_id);
                                $PRdetail = PrDetails::find_by_item_id($SingleAllocation->item_id);

                                /**********************~| Allocate quantity to each TM |~***************************/
                                if ($SingleAllocation->total_quantity % $tmcount == 0) {
                                    $roundFigure = ($SingleAllocation->total_quantity / $tmcount );
                                } else {
                                    $roundFigure = ceil($SingleAllocation->total_quantity / $tmcount);
                                }
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $PRdetail->key_no; ?>
                                        <input type="hidden" name="item_id[]" value="<?php echo $SingleAllocation->item_id; ?>">

                                    </td>
                                    <td><?php echo $ItemDetail->description; ?></td>
                                    <td><input type="text" name="quantity" value="<?php echo $SingleAllocation->total_quantity; ?>" class="form-control" readonly="readonly"></td>
                                    <?php
                                    $count = 0;
                                    $finalRoundFigure = 0;
                                    foreach ($TMlist as $Employee) {
                                        $count ++;
                                        ?>
                                        <td><input type="text" name="<?php echo $Employee->tm_empid . "[]"; ?>" value="<?php
                                            if ($tmcount == $count) {
                                                echo $SingleAllocation->total_quantity - $finalRoundFigure;
                                            } else {
                                                $finalRoundFigure = $roundFigure + $finalRoundFigure;
                                                echo $roundFigure;
                                            }
                                            ?>" class="form-control" ></td>
                                               <?php }/**                                                * ********* End Of TM loop ********************* */
                                               ?>

                                </tr>
                                <?php
                            }/**                             * ********** End of for ********* */
                            ?>
                        </tbody>
                    </table>
                </div> 
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <input type="submit" class="btn btn-info"  value="Save" name="dispatch">
                    </div>
                </div>
            <?php }/**             * ******* End of if *********** */
            ?>
        </form>
    </div>

</div>
<?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>

