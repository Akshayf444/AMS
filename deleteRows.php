<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
require_once(dirname(__FILE__)."/includes/initialize.php");
if (isset($_GET['item_id']) && isset($_GET['apr_id'])) {
	$deleteItem = new ItemDetails();
	$deleteItem->item_id = $_GET['item_id'];
	$deleteItem->delete($deleteItem->item_id);
	$EncryptUrl = new Encryption();
	redirect_to("editApproval.php?apr_id=". $EncryptUrl->encode($_GET['apr_id']));
}
?>