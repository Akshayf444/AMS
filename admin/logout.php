<?php session_start();

if(isset($_SESSION['employee'])){ 

	$_SESSION['employee']=null;
	echo "<script>window.location='login.php';</script>";
}else{
	if(isset($_SESSION['PRE'])){ 
	$_SESSION['PRE']=null;
	echo "<script>window.location='login.php';</script>";
	}else{
		if(isset($_SESSION['gpm'])){ 
		$_SESSION['gpm']=null;
		echo "<script>window.location='login.php';</script>";
		}else{
			if(isset($_SESSION['CWH']) || isset($_SESSION['mm'])){ 
			$_SESSION['CHW']=null;
                        $_SESSION['mm'] =null;
			echo "<script>window.location='login.php';</script>";
			}
		}
	}
}
session_destroy();
echo "<script>window.location='login.php';</script>";
?>