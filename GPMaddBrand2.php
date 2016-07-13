<?php
require_once(dirname(__FILE__) . "/MMheader.php");
$GPMs = GPM::find_by_mm_id($empid);
$divisions = explode(",", $empName->division);
$brands = array();
$disable = TRUE;
foreach ($divisions as $singleDivision) {
    $brandList = Brand::find_by_division1($singleDivision);
    foreach ($brandList as $value) {
        array_push($brands, $value);
    }
}

$Employees = Employee::find_by_mm($empid);
//



$n = 1;
$errors = array();
//Process Form data
if (isset($_POST['division']) && ($_POST['division'] != '')) {
    $disable = false;
    //$GPM = GPM::find_by_empid($_POST['gpm']);
    $Employees = Employee::find_by_division($_POST['division']);
    $brands = Brand::find_by_division1($_POST['division']);
    $length = sizeof($Employees);
}
if (isset($_POST['submit'])) {

    /*    foreach ($Employees as $Employee){ 
      if(empty($_POST[$Employee->empid])){
      array_push($errors, "Please Select Atleast one Brand For Each Employee");
      }
      } */

    $Employees = Employee::find_by_mm($empid);
    foreach ($Employees as $Employee) {

        $newEmployee_Brand = new Employee_Brand();

        if (isset($_POST[$Employee->empid])) {

            if (!empty($_POST[$Employee->empid])) {
                $brandList = implode(",", $_POST[$Employee->empid]);
                $newEmployee_Brand->empid = $Employee->empid;
                $newEmployee_Brand->brand_name = $brandList;
            } else {
                $brandList = 'NA';
                $newEmployee_Brand->empid = $Employee->empid;
                $newEmployee_Brand->brand_name = $brandList;
                //array_push($errors, "Please Select Atleast one Brand For Each Employee");
            }
        }
        if (empty($errors)) {
            $foundBrand = Employee_brand::exist($newEmployee_Brand->empid);
            if (empty($foundBrand)) {
                $newEmployee_Brand->create();
            } else {
                $newEmployee_Brand->update($newEmployee_Brand->empid);
            }
        } else {
            
        }
    }

    //var_dump($_POST);

    /* if(empty($errors)){
      $_SESSION['Error'] ="Success";

      } */
    echo "<script>window.location = 'GPMaddBrand2.php';</script>";
    //var_dump($_POST)
}
?>

<script src="js/jquery-1.11.0.js"></script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Assign Brands
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Assign Brands
            </li>
        </ol>
    </div>
</div>
<div id="errors" class="row">
    <div class="col-lg-12">
        <?php
        if (isset($_SESSION['Error'])) {
            echo $_SESSION['Error'];
            unset($_SESSION['Error']);
        }
        ?>
        <ul>
            <?php
            foreach (array_unique($errors) as $value) {
                echo $value;
            }
            ?>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form action="GPMaddBrand2.php" method="post">
            <div class="">
                <label>Division</label>

                <select class="form-control" name="division" onchange="this.form.submit()" style="width:30%">
                    <option value="" style="text-align: center">--- Select Division ---</option>
                    <?php
                    if (!empty($divisions)) {

                        foreach ($divisions as $singleDivision) {
                            $divisionName = Division::find_by_div_id($singleDivision);
                            ?>
                            <option value="<?php echo $divisionName->div_id; ?>"  <?php
                            if (isset($_POST['division']) && $_POST['division'] == $divisionName->div_id) {
                                echo "SELECTED";
                            }
                            ?>><?php echo $divisionName->div_name; ?></option>   
                                    <?php
                                }
                            }
                            ?>

                </select>
            </div>
        </form>

        <form action="GPMaddBrand2.php" method="post">

            <div class="table-responsive" style="margin-top:2em">
                <table class="table table-bordered table-hover table-striped" >
                    <thead>
                        <tr>
                            <td></td>
                            <?php
                            if (!empty($Employees)) {
                                foreach ($Employees as $Employee) {
                                    ?>
                                    <th><?php echo $Employee->name; ?></th>

                                    <?php
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($brands)) {
                            foreach ($brands as $brand) {
                                ?>
                                <tr style="text-align:center">
                                    <th><?php echo $brand->brand_name; ?></th>
                                    <?php
                                    foreach ($Employees as $Employee) {
                                        /*                                         * ** Select Assigned Brands *** */
                                        $AssignedBrands = Employee_Brand::find_by_empid2($Employee->empid);
                                        if (!empty($AssignedBrands)) {
                                            $brandlist = explode(",", $AssignedBrands->brand_name);
                                        }
                                        ?>
                                        <td>
                                            <input type="hidden" name="empid[]" value="<?php echo $Employee->empid; ?>">
                                            <p><input type="checkbox" name="<?php echo $Employee->empid . "[]"; ?>" value ="<?php echo $brand->brand_id; ?>" class="<?php echo str_replace(' ', '', $brand->brand_name); ?>" 
                                                <?php
                                                if (isset($_POST[$Employee->empid])) {
                                                    foreach ($_POST[$Employee->empid] as $value) {
                                                        if ($value == $brand->brand_id) {
                                                            echo 'checked';
                                                        }
                                                    }
                                                } else {
                                                    if (!empty($brandlist)) {
                                                        foreach ($brandlist as $empBrand) {
                                                            if ($brand->brand_id == $empBrand) {
                                                                echo 'checked';
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                <?php
                                                if ($disable == TRUE) {
                                                    echo 'readonly';
                                                }
                                                ?>></p>
                                        </td>	
                                        <?php
                                        unset($brandlist);
                                    }/*                                     * ** End Of Employee Loop *** */
                                    ?>
                                </tr>
                                <?php
                            }
                        }/*                         * ** End Of Brand Loop *** */
                        ?>
                        <?php if ($disable == FALSE) { ?>
                            <tr>
                                <td colspan="<?php
                                if (isset($length)) {
                                    echo $length + 1;
                                }
                                ?>">


                                    <button type="submit" class="btn btn-primary" name="submit" >Save</button>


                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(":checkbox").click(function () {
            var myClass = $(this).attr("class");
            if ($(this).prop("checked")) {
                $('.' + myClass).not(this).remove();
            } else {
                $('.' + myClass).not(this).show();
            }
        });
    });
</script>
<?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>