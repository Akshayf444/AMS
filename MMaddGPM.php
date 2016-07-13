<?php
require_once(dirname(__FILE__) . "/MMheader.php");

$errors = array();

$Brands = Brand::find_by_division1($empName->division);
$divisions = explode(",", $empName->division);

$ExistingBrands = array();
if (isset($_POST['submit'])) {
    $newEmployee = new GPM();
    $newEmployee->gpm_empid = $_POST['empid'];
    $newEmployee->division = $_POST['division'];
    $newEmployee->MM_empid = $_SESSION['mm'];
    $newEmployee->complete_profile = 0;

    if (!empty($_POST['name'])) {
        $newEmployee->name = $_POST['name'];
    } else {
        array_push($errors, "Please Enter PMT Name.");
    }


    if (!empty($_POST['emailid'])) {
        if (filter_var($_POST['emailid'], FILTER_VALIDATE_EMAIL)) {
            $newEmployee->emailid = trim($_POST['emailid']);
        } else {
            array_push($errors, "Invalid Email Address");
        }
    } else {
        array_push($errors, "Invalid Email Address");
    }


    if (!empty($_POST['password'])) {
        $newEmployee->password = $_POST['password'];
    } else {
        array_push($errors, "Please Enter Password.");
    }

    if (!empty($_POST['mobile'])) {

        $newEmployee->mobile = $_POST['mobile'];
    } else {
        array_push($errors, "Please Enter Mobile No.");
    }



    if (empty($errors)) {
        $found_employee = GPM::find_by_empid($newEmployee->empid);
        if (!empty($found_employee)) {
            array_push($errors, "GPM Already Exist");
        } else {
            $newEmployee->create();
            flashMessage("GPM Record Added Successfully",'Success');
            echo "<script>window.location = 'MMlistGPM.php';</script>";
        }
    }
    //var_dump($errors);
    $message = join(",", $errors);
    //echo $message;
    flashMessage($message, "Error");
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add GPM
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-plus"></i>  Add GPM
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
                            <?php
                            if (!empty($divisions)) {
                                foreach ($divisions as $singleDivision) {
                                    $divisionName = Division::find_by_div_id($singleDivision);
                                    ?>
                                    <option value="<?php echo $divisionName->div_id; ?>"><?php echo $divisionName->div_name; ?></option>   
                                <?php }
                            }
                            ?>

                        </select>
                    </div>
                </div>
                <div class="row" style="margin-bottom:1em;">
                    <div class="col-lg-6  col-sm-6 col-md-6 col-xs-6">
                        <label>Employee Id</label>
                        <input type="text" name="empid"  value="<?php
                        if (isset($_POST['empid'])) {
                            echo $_POST['empid'];
                        }
                        ?>"  class="form-control" id="empid" required/>
                    </div>
                </div>
                <div class="row" style="margin-bottom:1em;">
                    <div class="col-lg-6  col-sm-6 col-md-6 col-xs-6">
                        <label>Name</label>
                        <input type="text" name="name"  value="<?php
                        if (isset($_POST['name'])) {
                            echo $_POST['name'];
                        }
                        ?>"  class="form-control" id="name" required/>
                    </div>
                </div>
                <div class="row" style="margin-bottom:1em;">
                    <div class="col-lg-6  col-sm-6 col-md-6 col-xs-6">
                        <label>Email Id</label>
                        <input type="text" name="emailid" value="<?php
                        if (isset($_POST['emailid'])) {
                            echo $_POST['emailid'];
                        }
                        ?>"  class="form-control" id="emailid" required />
                    </div>
                </div>
                <div class="row" style="margin-bottom:1em;">
                    <div class="col-lg-6  col-sm-6 col-md-6 col-xs-6">
                        <label>Mobile</label>
                        <input type="text" name="mobile"  value="<?php
                        if (isset($_POST['mobile'])) {
                            echo $_POST['mobile'];
                        }
                        ?>"  class="form-control" id="mobile" required/>
                    </div>
                </div>
                <div class="row" style="margin-bottom:1em;">
                    <div class="col-lg-6  col-sm-6 col-md-6 col-xs-6">
                        <label>Password</label>
                        <input type="text" name="password" value="<?php
                        if (isset($_POST['password'])) {
                            echo $_POST['password'];
                        }
                        ?>"  class="form-control" id="password" required />
                    </div>
                </div>
                <hr/>
                <div>
                    <input type="submit" name="submit" value="Save" class="btn btn-primary">
                </div>
        </form>
    </div>
</div>  
<?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>