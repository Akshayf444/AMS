<?php
session_start();
if (!isset($_SESSION['gpm'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

if (isset($_POST['submit'])) {
    $username = $_SESSION['gpm'];
    $empName = GPM::find_by_empid($username);
    $password = trim($_POST['oldpwd']);
    $newPassword = trim($_POST['newpwd']);
    $found_user = GPM::authenticate($username, $password);
    if ($found_user) {
        $changePassword = GPM::changePassword($newPassword, $username);
        if ($changePassword) {
            $_SESSION['error'] ='<div class="alert alert-success alert-dismissible" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <strong>Password Changed Successfully.</strong> 
                              </div>';
        }
    }  else {
        $_SESSION['error'] ='<div class="alert alert-danger alert-dismissible" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <strong>Old Password Does Not Match.</strong> 
                              </div>';
    }
    
            
}

require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Change Password
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-list"></i> Change Password
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
    <div class="col-lg-4" >
        <form action="GPMchangePassword.php" method="post">

            <div class="col-lg-12">
                <label>Old Password</label>
                <input type="text" name="oldpwd" class="form-control" value="" required>
            </div>
            <div class="col-lg-12">
                <label>New Password</label>
                <input type="text" name="newpwd" class="form-control" value="" required>
            </div> 
            
            <div class="col-lg-12">
                <hr>
                <input type="submit" name="submit"  class="btn btn-primary btn-large" value="Save">
            </div>
        </form>
    </div>
</div>

<?php require_once(dirname(__FILE__) . "/layouts/gpmlayouts/footer.php");
?>