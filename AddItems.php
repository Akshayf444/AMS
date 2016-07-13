<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['employee'];

    if(isset($_POST['search_term'])){
    	global $database;

    }
?>