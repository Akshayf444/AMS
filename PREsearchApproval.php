<?php session_start(); if(!isset($_SESSION['PRE'])){header("Location:login.php"); }
require_once(dirname(__FILE__)."/includes/initialize.php");

if(isset($_POST['search_term']) && !empty($_POST['search_term'])){
	$approvals=Approval::SearchName($_POST['search_term']);
}
?>