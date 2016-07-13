<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$grandTotal = 0;
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$Manpowers = Manpower::find_by_division($empName->division);

if (isset($_POST['save'])) {
    
    $length = count($_POST['manpower_id']);
    for ($i = 0; $i < $length; $i++) {
        $UpdateManpower = new Manpower();
        $UpdateManpower->id = $_POST['manpower_id'][$i];
        $UpdateManpower->region_id = $_POST['region_id'][$i];
        $UpdateManpower->no_of_persons = $_POST['no_of_persons'][$i];
        $UpdateManpower->division = $empName->division;
        
        $UpdateManpower->update($UpdateManpower->id);
    }
    
    redirect_to("GPMupdateManpower.php");
}
require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Update Manpower
        </h1>

        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <i class="fa fa-dashboard"></i> Update Manpower
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="table-responsive" style="margin-top:2em">
            <form action="GPMupdateManpower.php" method="post" >
            <table class="table table-bordered table-hover table-striped" id="items">
                <tr>
                    <th>Region</th>
                    <th>No Of Persons</th>
                </tr>
                <?php foreach ($Manpowers as $Manpower) { ?>
                    <tr>
                        <td>
                            <?php $regionName = Region::find_by_region_id($Manpower->region_id);
                            echo $regionName->region_name;
                            ?>
                            <input type="hidden" name="region_id[]" value="<?php echo $regionName->region_id; ?>">
                            <input type="hidden" name="manpower_id[]" value="<?php echo $Manpower->id; ?>">
                        </td>
                        <td><input class="form-control rate" value="<?php
                            echo $Manpower->no_of_persons;
                            $grandTotal+=$Manpower->no_of_persons;
                            ?>"  name="no_of_persons[]" type="text" >
                        </td>
                    </tr>
<?php } ?>
                <tr>
                    <td colspan="1"><strong>Total</strong></td>
                    <td><?php echo $grandTotal;
$grandTotal = 0; ?></td>
                </tr>
                <tr>
                    <td colspan="5">
                        <button type="submit" class="btn btn-primary" id="save" name="save"  >Save</button>
                    </td>
                </tr>
                
            </table>
                </form>
        </div>
    </div>
</div>
<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>