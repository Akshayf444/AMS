<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$errors = array();
$empid = $_SESSION['gpm'];
$empName = GPM::find_by_empid($empid);
$Brands = Brand::find_by_division1($empName->division);
$ExistingBrands = array();
if (isset($_POST['submit'])) {

    for ($i = 0; $i < 5; $i++) {
        $brand = new Brand();
        $brand->brand_id = 0;
        $brand->div_id = $empName->division;
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
        redirect_to("GPMbrandList.php");
    }
}

require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add Brand
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
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
        <form action="GPMeditBudget.php" method="post">
            <div class="table-responsive" style="border:0px">
                <table class="table">
                    <tr style="border:0px">
                        <td style="border:0px">                

                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][0];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][1];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][2];
                            }
                            ?>" >
                        </td>
                    </tr>
                    <tr style="border:0px">
                        <td style="border:0px">
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][3];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
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
<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php"); ?>