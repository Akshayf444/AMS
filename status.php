<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['employee'];
    $empName =Employee::find_by_empid($empid);
     require_once(dirname(__FILE__)."/layouts/header.php");
?>

<?php require_once(dirname(__FILE__)."/layouts/footer.php");?>