<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$empName = Employee::find_by_empid($empid);
$errors = array();

if (isset($_POST['submit'])) {
    $updatePMT = new Employee();
    $updatePMT->name = $empName->name;
    $updatePMT->empid = $_POST['empid'];
    $updatePMT->type = 'PMT';
    $updatePMT->status = 1;
    $updatePMT->complete_profile = 1;
    $updatePMT->password = $_POST['password'];
    $updatePMT->mobile = $_POST['mobile'];
    $updatePMT->emailid = $_POST['emailid'];
    $updatePMT->gpm_empid = $empName->gpm_empid;

    $found_pmt = admin::employeeExist($updatePMT->empid);
    if ($found_pmt) {
        array_push($errors, "Employee Already Exist.");
        flashMessage("Employee Already Exist.", "Error");
    } else {
        $updateEmp = new Employee_Brand();
        $updateEmp->empid = $updatePMT->empid;
        $updateEmp->updateEmployeeBrand($empid);
        $updatePMT->update($empid);

        $_SESSION['employee'] = $updatePMT->empid;
        redirect_to("index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>GPM</title>
        <script src="js/jquery-1.11.0.js"></script>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/bootstrap-multiselect.css" rel="stylesheet">   
        <link href="css/main.css" rel="stylesheet">
        <link href="css/sb-admin.css" rel="stylesheet">
        <link href="css/plugins/morris.css" rel="stylesheet">
        <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="js/highcharts.js"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="wrapper">
            <div class="modal" data-show="true" id="myModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h4 class="modal-title">Dear <?php echo $empName->name; ?> ,Please Complete Your Profile</h4>
                        </div>
                        <?php
                        if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        }
                        ?>
                        <form action="#" method="post" id="completeProfile">
                            <div class="modal-body">
                                <div>
                                    <label for="empid">Employee-Id *</label> <input type="text" name="empid" required class="form-control" value="<?php if (isset($_POST['submit'])) {
                            echo $_POST['empid'];
                        } ?>"/> </div>
                                <div><label for="emailid">Email-Id *</label> <input type="email" name="emailid" required class="form-control" value="<?php if (isset($_POST['submit'])) {
                            echo $_POST['emailid'];
                        } ?>"/> </div>
                                <div><label for="password">Set Your New Password *</label> <input type="text" name="password" required class="form-control" value="<?php if (isset($_POST['submit'])) {
                            echo $_POST['password'];
                        } ?>"/> </div>
                                <div><label for="mobile">10 Digit Mobile No*</label> <input type="text" name="mobile" required class="form-control" value="<?php if (isset($_POST['submit'])) {
                            echo $_POST['mobile'];
                        } ?>" maxlength="10"/> </div>
                            </div>
                            <div class="modal-footer">
                                <a href="logout.php" class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-primary" name="submit">Save changes</button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<script src="js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        $('#myModal').modal('show');
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false
        })
    });
    $("#completeProfile").validate({
        rules: {
            empid: "required",
            emailid: {
                required: true,
                email: true
            },
            password: "required",
            mobile: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            }

        },
        messages: {
            empid: "Please Enter Your Employee Id",
            password: "Please Enter Password",
            emailid: {
                required: "Please Enter Your Email Id",
                email: "Please Enter Valid Email Address"
            },
            mobile: {
                required: "Please Enter Your Mobile No",
                number: "Please Enter valid mobile No",
                minlength: "Mobile No Must Be 10 Digit",
                maxlength: "Mobile No Must Be 10 Digit"
            }
        }
    });
</script>
</body>

</html>