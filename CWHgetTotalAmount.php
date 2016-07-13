<?php session_start(); if(!isset($_SESSION['CWH'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['CWH']; 

if(isset($_POST['search_term']) && trim($_POST['search_term']) != ''){
	$item_id=$_POST['search_term'];
	$qty_per_person = AllocationDetails::find_qty_per_person($item_id);
	if(!empty($qty_per_person)){
		echo $qty_per_person;
	}else{
		echo 0;
	}

}

if(isset($_POST['search_term2']) && trim($_POST['search_term2']) != ''){
	$item_id=$_POST['search_term2']; 
	$ItemName = ItemDetails::find_by_item_id($item_id);
	?>
<h4 class="modal-title" id="myModalLabel">Copy From <?php if(!empty($ItemName)){
	echo $ItemName->description;
}?>
</h4>
<input type="hidden" value="<?php echo $item_id; ?>"  name="copyFrom">
<?php } ?>