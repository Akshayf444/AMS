<?php

//session_start(); if(!isset($_SESSION['gpm'])){header("Location:login.php"); }
session_start();
require_once(dirname(__FILE__) . "/includes/initialize.php");
if (isset($_POST['empid'])) {
    $empid = $_POST['empid'];

    $PMT = Employee::find_by_empid($empid);
    if ($PMT->status == 1) {
        //echo 
        $status = 0;
        $updateStatus = Employee::ManageAccount($PMT->empid, $status);
        if (isset($_SESSION['gpm'])) {
            redirect_to("GPMlistPmt.php");
        } else {
            redirect_to("MMlistPmt.php");
        }
    }
    if ($PMT->status == 0) {
        $status = 1;

        $updateStatus = Employee::ManageAccount($PMT->empid, $status);
        if (isset($_SESSION['gpm'])) {
            redirect_to("GPMlistPmt.php");
        } else {
            redirect_to("MMlistPmt.php");
        }
    }
} else {

    if (isset($_SESSION['gpm'])) {
        redirect_to("GPMlistPmt.php");
    } else {
        redirect_to("MMlistPmt.php");
    }
}
?>