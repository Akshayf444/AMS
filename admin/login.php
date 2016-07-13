<?php
if (isset($_SESSION['admin'])) {
    header("Location:login.php");
}
require_once("../includes/initialize.php");
$errors = array();

if (isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $found_user = Admin::authenticate($username, $password);

    if ($found_user) {
            session_start();
            $_SESSION['admin']=$username;
            redirect_to("index.php");
        
    } else { 

        array_push($errors, "Incorrect Username/Password Combination.");

     }
       
} else { // Form has not been submitted.
    $username = "";
    $password = "";
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

        <title>Login</title>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">
        <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.11.0.js"></script>

        <style>
            .btn-primary {
                color: #fff;
                background-color: rgba(68, 141, 203, 0.52);
                border-color: #357ebd;
            }
            .form-control {
                color: #f5f5f5;
                background-color: rgba(255, 255, 255, 0.37);

            }
        </style>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="../js/html5.js"></script>
          <script src="../js/respond.js"></script>
          <link href="../css/IEstyle.css" rel="stylesheet">
        <![endif]-->
    </head>
    <body class="body-Login-back img-responsive" style="background: url('../images/back.jpg') center ;" id="bg">



        <div class="container-fluid" style="margin-top:5em">
            <div class="col-lg-4 col-md-3 col-sm-2 col-xs-1"></div>
            <div class="col-lg-4 col-md-6 col-sm-8 col-xs-10">
                <div class="login-panel panel panel-default" style="background-color: rgba(0, 0, 0, 0.2);color:#fff;">                  
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-12" style="text-align:center;color:red">
                            <?php foreach ($errors as $val) { ?>
                                <strong><?php echo $val; ?></strong>
                            <?php } ?> 
                        </div>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="login.php" method="post">
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control"  type="text" name="username" autocomplete="off" 
                                       value="<?php
                                       if (isset($_POST['username'])) {
                                           echo $_POST['username'];
                                       }
                                       ?>">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control"  type="password" name="password">
                            </div>
                            <button type="submit" class="btn btn-danger btn-block btn-lg" name="submit" ><strong> Login </strong></button>
                        </form>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4"></div>   
        </div>

        <div class="" style="text-align:center">
            <div class="container">
                <p><strong>Powered By </strong><a href="http://www.techvertica.com" class="external roll-link" rel="nofollow"><span data-title="Techvertica">Techvertica</span></a></p>
            </div>
        </div>
    </body>
</html>