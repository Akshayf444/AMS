<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$PMTs = Employee::find_by_gpm($empid);
require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            List Of PMT's
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-desktop"></i> List Of PMT's
            </li>
        </ol>
    </div>
</div>
<div class="row" style="margin-bottom:2em;">
    <div class="col-lg-1 pull-center">
        <a href="GPMaddPMT.php"><button type="button" class="btn btn-default" >Add New</button></a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Emp Id</th>
                        <th>PMT Name</th>
                        <th>Associated Brand</th>
                        <th>Status</th>
                        <th style="width:10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($PMTs as $PMT) {
                        $AssignedBrand = Employee_Brand::find_by_empid2($PMT->empid);
                        ?>
                        <tr>
                            <td><?php echo $PMT->empid; ?></td>
                            <td><?php echo $PMT->name; ?></td>
                            <td><?php
                                if (!empty($AssignedBrand)) {
                                    echo Employee_Brand::explodeBrandList($AssignedBrand->brand_name);
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($PMT->status == 1) {
                                    echo 'Active';
                                } else {
                                    echo 'Deactivated';
                                }
                                ?></td>

                            <?php if ($PMT->status == 1) { ?>
                                <td><form action="GPMactivateAccount.php" method="post"><input type="submit" name="submit" class="btn btn-danger btn-xs" value="Deactivate">
                                        <input type="hidden" name="empid" value="<?php echo $PMT->empid; ?>">
                                    </form>
                                </td>
                            <?php } else { ?>
                                <td><form action="GPMactivateAccount.php" method="post"><input type="submit" name="submit" class="btn btn-success btn-xs" value="Activate">
                                        <input type="hidden" name="empid" value="<?php echo $PMT->empid; ?>">
                                    </form>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>