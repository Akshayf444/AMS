<?php
require_once(dirname(__FILE__) . "/MMheader.php");
$ErrorMessages = '';
$GPMs = GPM::find_by_mm_id($empid);
$errors = array();

if (isset($_POST['submit'])) {
    $newEmployee = new Employee();
    $newEmployee->empid = $_POST['empid'];
    $newEmployee->status = 'Active';
    $newEmployee->gpm_empid = $_POST['gpm'];
    $newEmployee->type = 'PMT';

    $findGPM = GPM::find_by_empid($newEmployee->gpm_empid);
    $newEmployee->division = $findGPM->division;

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
        $found_employee = Employee::find_by_empid($newEmployee->empid);
        if (!empty($found_employee)) {
            array_push($errors, "Employee Already Exist");
        } else {
            $newEmployee->create();
            $_SESSION['message'] = 'Success';
            echo "<script>window.location = 'MMlistPmt.php';</script>";
        }
    }


    $message = join(",", $errors);
    //echo $message;
    flashMessage($message, "Error");
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add PMT
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-desktop"></i> Add PMT
            </li>
        </ol>
    </div>
</div>
<div class="row" >
    <div class="col-lg-12" id="errors">
        <ul style='color:red;'>
<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<?php ?>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-lg-3"></div>
    <div class="col-lg-6 col-sm-6 col-md-6 pull-center">
        <form action="#" method="post" id="form1">  
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-10 col-sm-10 col-md-10 col-xs-10">
                    <label>Employee Id</label>
                    <input type="text" name="empid"  value="<?php
                    if (isset($_POST['empid'])) {
                        echo $_POST['empid'];
                    }
                    ?>"  class="form-control" id="empid" required/>
                </div>
                <div class="col-lg-10 col-sm-10 col-md-10 col-xs-10">
                    <label>Name</label>
                    <input type="text" name="name"  value="<?php
                    if (isset($_POST['name'])) {
                        echo $_POST['name'];
                    }
                    ?>"  class="form-control" id="name" required/>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-10 col-sm-10 col-md-10 col-xs-10">
                    <label>GPM</label>

                    <select class="form-control" name="gpm">
<?php
if (!empty($GPMs)) {
    foreach ($GPMs as $GPM) {
        ?>
                                <option value="<?php echo $GPM->gpm_empid; ?>"><?php echo $GPM->name; ?></option>   
                        <?php
                        }
                    }
                    ?>

                    </select>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-10 col-sm-10 col-md-10 col-xs-10">
                    <label>Email Id</label>
                    <input type="text" name="emailid" value="<?php
                    if (isset($_POST['emailid'])) {
                        echo $_POST['emailid'];
                    }
                    ?>"  class="form-control" id="emailid" required />
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-10 col-sm-10 col-md-10 col-xs-10">
                    <label>Mobile</label>
                    <input type="text" name="mobile"  value="<?php
                    if (isset($_POST['mobile'])) {
                        echo $_POST['mobile'];
                    }
                    ?>"  class="form-control" id="mobile" required/>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-10 col-sm-10 col-md-10 col-xs-10">
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
    </div>
</form>
</div>

<script src="js/jquery.validate.min.js"></script>
<script>
// When the browser is ready...
    $(function () {

        $("#form1").validate({
            // Specify the validation rules
            rules: {
                empid: "required",
                name: "required",
                emailid: {
                    required: true,
                    email: true
                },
                password: "required",
                mobile: {
                    required: true,
                    number: true
                }

            },
            // Specify the validation error messages
            messages: {
                empid: "Please Enter Employee Id.",
                name: "Please Enter PMT Name.",
                emailid: {
                    required: "Please Enter Emaild id",
                    email: "Please Enter Valid Email Address."
                },
                password: "Please Enter Password.",
                mobile: "Please Enter Mobile No."
            },
            submitHandler: function (form) {
                form.submit();
            }
        });

    });

</script>
<?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>