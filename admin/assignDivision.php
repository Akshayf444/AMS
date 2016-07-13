<?php
require_once(dirname(__FILE__) . "/header.php");

$brands = Division::find_all();
$Employees = MM::find_all();

$length = sizeof($Employees);

$n = 1;
$errors = array();
//Process Form data
if (isset($_POST['submit'])) {

    /*    foreach ($Employees as $Employee){ 
      if(empty($_POST[$Employee->MM_empid])){
      array_push($errors, "Please Select Atleast one Brand For Each Employee");
      }
      } */

    foreach ($Employees as $Employee) {

        $updateMM = new MM();

        if (!empty($_POST[$Employee->MM_empid])) {
            $brandList = implode(",", $_POST[$Employee->MM_empid]);
            $updateMM->MM_empid = $Employee->MM_empid;
            $updateMM->division = $brandList;
        } else {
            $brandList = 'NA';
            $updateMM->MM_empid = $Employee->MM_empid;
            $updateMM->division = $brandList;
            //array_push($errors, "Please Select Atleast one Brand For Each Employee");
        }

        if (empty($errors)) {

            $updateMM->assignDivision($updateMM->MM_empid);
        } else {
            
        }
    }

    //var_dump($_POST);

    /* if(empty($errors)){
      $_SESSION['Error'] ="Success";

      } */
    echo "<script>window.location = 'assignDivision.php';</script>";
    //var_dump($_POST)
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Assign Division
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Assign Division
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
        <div class="table-responsive" style="overflow-x: scroll;font-size: 11px;">
            <form action="#" method="post">
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
                        <?php foreach ($brands as $brand) { ?>
                            <tr style="text-align:center">
                                <th><?php echo $brand->div_name; ?></th>
                                <?php
                                foreach ($Employees as $Employee) {
                                    /*                                     * ** Select Assigned Brands *** */
                                    $AssignedBrands = $Employee->division;
                                    if (!empty($AssignedBrands)) {
                                        $brandlist = explode(",", $AssignedBrands);
                                    }
                                    ?>
                                    <td>
                                        <input type="hidden" name="MM_empid[]" value="<?php echo $Employee->MM_empid; ?>">
                                        <input type="checkbox" name="<?php echo $Employee->MM_empid . "[]"; ?>" value ="<?php echo $brand->div_id; ?>" class="<?php
                                        echo str_replace(' ', '', $brand->div_id);
                                        echo " " . $Employee->MM_empid;
                                        ?>" 
                                        <?php
                                        if (isset($_POST[$Employee->MM_empid])) {
                                            foreach ($_POST[$Employee->MM_empid] as $value) {
                                                if ($value == $brand->div_id) {
                                                    echo 'checked';
                                                }
                                            }
                                        } else {
                                            if (!empty($brandlist)) {
                                                foreach ($brandlist as $empBrand) {
                                                    if ($brand->div_id == $empBrand) {
                                                        echo 'checked';
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                               >
                                    </td>	
                                    <?php
                                    unset($brandlist);
                                }/*                                 * ** End Of Employee Loop *** */
                                ?>
                            </tr>
<?php }/* * ** End Of Brand Loop *** */ ?>
                        <tr>
                            <td colspan="<?php echo $length + 1; ?>">
                                <button type="reset" class="btn btn-success" name="reset" >Reset</button>
                                <button type="submit" class="btn btn-primary" name="submit" >Save</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(":checkbox").click(function () {

            var myClass = $(this).attr("class");
            var result = myClass.split(" ");

            //alert(result[0]); alert(result[1]);
            if ($(this).prop("checked")) {
                $('.' + result[0]).not(this).remove();
                //$('.' + result[1]).not(this).remove();
            } else {
                $('.' + myClass).not(this).show();
            }
        });
    });
</script>
<?php require_once(dirname(__FILE__) . "/footer.php"); ?>