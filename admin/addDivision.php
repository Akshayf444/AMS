<?php
require_once(dirname(__FILE__) . "/header.php");

$ExistingBrands = array();
$errors = array();
if (isset($_POST['submit'])) {

    //var_dump($_POST['brand']);
    $finalBrands = array_values(array_filter($_POST['brand']));
    $count = count($finalBrands);

    //var_dump($finalBrands);
    
    for ($i = 0; $i < $count; $i++) {
        $brand = new Division();
        $brand->div_id = 0;

        $foundBrand = Division::find_by_div_name($finalBrands[$i]);
        if (!empty($foundBrand)) {
            array_push($errors, "Division Already Exist");
            array_push($ExistingBrands, $finalBrands[$i]);
        } else {
            $brand->div_name = $finalBrands[$i];
        }

        if (empty($errors) && empty($ExistingBrands)) {
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
        echo "<script>window.location = 'addDivision.php';</script>";
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add Division
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-desktop"></i> Add Division
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }?>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form action="addDivision.php" method="post">
            <div class="table-responsive" style="border:0px">
                <table class="table">
                    <tr style="border:0px">
                        <td style="border:0px">                
                            <label>Division 1</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][0];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <label>Division 2</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][1];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <label>Division 3</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][2];
                            }
                            ?>" >
                        </td>
                    </tr>
                    <tr style="border:0px">
                        <td style="border:0px">
                            <label>Division 4</label>
                            <input type="text" class="form-control" name="brand[]" value="<?php
                            if (isset($_POST['brand'])) {
                                echo $_POST['brand'][3];
                            }
                            ?>" >
                        </td>
                        <td style="border:0px">
                            <label>Division 5</label>
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
<?php require_once(dirname(__FILE__) . "/footer.php"); ?>