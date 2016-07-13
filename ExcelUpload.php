<?php  require_once(dirname(__FILE__)."/includes/initialize.php");
$errors=array();
	$max_file_size = 10485760;
	if(isset($_POST['Depot']) ) {
		if(isset($_FILES['file_upload1']) && !empty($_FILES['file_upload1'])){
		$photo = new Upload();

		$photo->attach_file($_FILES['file_upload1']);
		if(file_exists($photo->filename)) {
			array_push($errors, "file already exist");
		}
		else{
			$flag ='Depot';
			if($photo->save($flag)){
			array_push($errors, "Uploaded Succesfully.");
			}else{
				 $message = join("<br />", $photo->errors);
				array_push($errors, $message);
			}
		} 
	}
	}

		if(isset($_POST['Region'])) {
		if(isset($_FILES['file_upload2']) && !empty($_FILES['file_upload2'])){
		$photo = new Upload();
		$photo->attach_file($_FILES['file_upload2']);
		if(file_exists($photo->filename)) {
			array_push($errors, "file already exist");
		}
		else{
			$flag ='Region';
			if($photo->save($flag)){
			array_push($errors, "Uploaded Succesfully.");}
			else{
				 $message = join("<br />", $photo->errors);
				array_push($errors, $message);
			}
		} 
		}
	}

		if(isset($_POST['Manpower'])) {
		if(isset($_FILES['file_upload3']) && !empty($_FILES['file_upload3'])){
		$photo = new Upload();
		$photo->attach_file($_FILES['file_upload3']);
		if(file_exists($photo->filename)) {
			array_push($errors, "file already exist");
		}
		else{
			$flag ='Manpower';
			if($photo->save($flag)){
			array_push($errors, "Uploaded Succesfully.");}
			else{
				 $message = join("<br />", $photo->errors);
				array_push($errors, $message);
			}
		} 
		}
	}
?>

<div>
	<ul>
		<?php foreach($errors as $val){ ?>
        <li style="color:red;list-style-type:none;"><?php echo $val; ?></li>
    	<?php } ?>
	</ul>
	<h2>File  Upload</h2>

  <form action="ExcelUpload.php" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
    <p>Upload Depot File: <input type="file" name="file_upload1" /></p>
    <input type="submit" name="Depot" value="Upload" />
  </form> 
<hr/>
    <form action="ExcelUpload.php" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
    <p>Upload Region File: <input type="file" name="file_upload2" /></p>
    <input type="submit" name="Region" value="Upload" />
  </form>
<hr/>
    <form action="ExcelUpload.php" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
    <p>Upload Manpower File: <input type="file" name="file_upload3" /></p>
    <input type="submit" name="Manpower" value="Upload" />
  </form>
 </div>