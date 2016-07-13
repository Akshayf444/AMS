<?php //session_start(); if(!isset($_SESSION['gpm'])){header("Location:login.php"); }
 session_start();
 require_once(dirname(__FILE__)."/includes/initialize.php");
 	if(isset($_POST['brandid'])) {
	 	$brandid = $_POST['brandid'];

	 	$Brand =Brand::find_by_brand_id2($brandid);
	 	if ($Brand->status == 1) {
	 		//echo 
	 		$status = 0;
	 		$updateStatus = Brand::ManageBrand($Brand->brand_id,$status);
	 		
	 		if (isset($_SESSION['gpm'])) {
			redirect_to("GPMbrandlist.php");
		}else{
			redirect_to("MMbrandlist.php");
		}
	 	}
	 	if ($Brand->status == 0) {
	 		$status = 1;
	 		$updateStatus = Brand::ManageBrand($Brand->brand_id,$status);
	 		if (isset($_SESSION['gpm'])) {
			redirect_to("GPMbrandlist.php");
		}else{
			redirect_to("MMbrandlist.php");
		}
	 	}
	}else{
		if (isset($_SESSION['gpm'])) {
			redirect_to("GPMbrandlist.php");
		}else{
			redirect_to("MMbrandlist.php");
		}
	}
 ?>