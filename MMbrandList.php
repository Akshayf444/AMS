<?php  require_once(dirname(__FILE__)."/MMheader.php");

$divisions =explode(",", $empName->division);
$brands = array();

foreach ($divisions as $singleDivision) {
    $brandList = Brand::find_by_division1($singleDivision);
    foreach ($brandList as $value) {
       array_push($brands, $value);
    }
    
}

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Manage Brands
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Manage Brands
            </li>
        </ol>
    </div>
</div>
<div class="row" style="margin-bottom:2em;">
    <div class="col-lg-1 pull-center">
        <a href="MMaddBrand.php"><button type="button" class="btn btn-default" >Add New</button></a>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <table class="table table-bordered table-hover table-striped" >
            <tr>
                <th>Brand Name</th>
                <th>Action </th>
            </tr>
            <?php foreach ($brands as $brand) { ?>
                <tr>
                    <td><?php echo $brand->brand_name; ?></td>

                    <?php if ($brand->status == 0) { ?>

                        <td><form action="GPMactivateBrand.php" method="post"><input type="submit" name="submit" class="btn btn-danger btn-xs" value="De-activate">
                                <input type="hidden" name="brandid" value="<?php echo $brand->brand_id; ?>">
                            </form></td>
                    <?php } else { ?>
                        <td><form action="GPMactivateBrand.php" method="post"><input type="submit" name="submit" class="btn btn-success btn-xs" value="Activate">
                                <input type="hidden" name="brandid" value="<?php echo $brand->brand_id; ?>">
                            </form></td>
                    <?php } ?>
                </tr>
            <?php }/** ** End Of Brand Loop *** */ ?>
        </table>
    </div>
</div>
<?php  require_once(dirname(__FILE__)."/MMfooter.php");
?>