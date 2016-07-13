<?php
session_start();
if (!isset($_SESSION['mm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['mm'];
$empName = MM::find_by_empid($empid);
$errors = array();

if (isset($_POST['submit'])) {
    $updateMM = new MM();
    $updateMM->name = $empName->name;
    $updateMM->MM_empid = $_POST['empid'];
    $updateMM->division = $empName->division;
    $updateMM->complete_profile = 1;
    $updateMM->password = $_POST['password'];
    $updateMM->mobile= $_POST['mobile'];
    $updateMM->emailid = $_POST['emailid'];
    //$updateMM->gpm_empid = $_POST['empid'];
    
    $found_mm = admin::employeeExist($updateMM->MM_empid);
    if ($found_mm) {
        array_push($errors, "MM Already Exist.");
        flashMessage("MM Already Exist.", "Error");
    }  else {
        $updateEmp = new GPM();
        $updateEmp->MM_empid = $updateMM->MM_empid;
        $updateEmp->updateMM($empid);
        $updateMM->update($empid);
        
        $_SESSION['mm'] = $updateMM->MM_empid;
        redirect_to("MMindex.php");
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
                            
                            <h4 class="modal-title">Dear <?php echo $empName->name;?> ,Please Complete Your Profile</h4>
                        </div>
                        <?php if (isset($_SESSION['message'])) {
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                        }?>
                        <form action="#" method="post" id="completeProfile">
                            <div class="modal-body">
                                <div>
                                    <label for="empid">Employee-Id *</label> <input type="text" name="empid" required class="form-control" value="<?php if (isset($_POST['submit'])) { echo $_POST['empid'];} ?>"/> </div>
                                <div><label for="emailid">Email-Id *</label> <input type="email" name="emailid" required class="form-control" value="<?php if (isset($_POST['submit'])) { echo $_POST['emailid'];} ?>"/> </div>
                                <div><label for="password">Set Your New Password *</label> <input type="text" name="password" required class="form-control" value="<?php if (isset($_POST['submit'])) { echo $_POST['password'];} ?>"/> </div>
                                <div><label for="mobile">10 Digit Mobile No*</label> <input type="text" name="mobile" required class="form-control" value="<?php if (isset($_POST['submit'])) { echo $_POST['mobile'];} ?>" maxlength="10"/> </div>
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