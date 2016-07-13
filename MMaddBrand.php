<?php
require_once(dirname(__FILE__) . "/MMheader.php");

$errors = array();

$Brands = Brand::find_by_division1($empName->division);
$divisions = explode(",", $empName->division);

$ExistingBrands = array();
if (isset($_POST['submit'])) {

    for ($i = 0; $i < 5; $i++) {
        $brand = new Brand();
        $brand->brand_id = 0;
        $brand->div_id = $_POST['division'];
        $brand->status = 1;
        $finalBrand = trim($_POST['brand'][$i]);
        if (!empty($finalBrand) && ($finalBrand != ' ')) {
            $foundBrand = Brand::find_by_brand_name($finalBrand);
            if (!empty($foundBrand)) {
                array_push($errors, "Brand Already Exist");
                array_push($ExistingBrands, $finalBrand);
            } else {
                $brand->brand_name = $finalBrand;
            }
        } else {
            array_push($errors, "Brand Name Cannot be emp0ty");
        }

        if (empty($errors)) {
            $brand->create();
        }
    }

    if (!empty($ExistingBrands)) {
        $brandlist = join(",", $ExistingBrands);
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <strong>' . $brandlist . ' Already Exist</strong> 
                              </div>';
    }

    if (isset($_SESSION['error'])) {
        
    } else {
        echo "<script>window.location = 'MMbrandList.php';</script>";
    }
}


?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add Brand
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-plus"></i>  Add Brand
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php
        if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form action="#" method="post">
            <div class="table-responsive" style="border:0px">
                <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
                    
                    <label>Select Division</label>
     
                                    <select class="form-control" name="division">
                                        <?php if (!empty($divisions)) { 
                                            foreach ($divisions as $singleDivision) {
                                                $divisionName = Division::find_by_div_id($singleDivision);
                                             ?>
                                        <option value="<?php echo $divisionName->div_id;?>"><?php echo $divisionName->div_name;?></option>   
                                        <?php } } ?>
                                                               
                                    </select>
                </div>
            </div>
                <table class="table">
                    <tr style="border:0px">
                        <td style="border:0px">                
                            <label>Brand 1</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][0];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <label>Brand 2</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][1];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <label>Brand 3</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][2];
                            }
                            ?>" >
                        </td>
                    </tr>
                    <tr style="border:0px">
                        <td style="border:0px">
                            <label>Brand 4</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][3];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <label>Brand 5</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][4];
                            }
                            ?>" >

                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <button type="submit" class="btn btn-primary" name="submit" >Save</button>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>  
<?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>