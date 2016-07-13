<?php
require_once('database.php');
require_once('/Classes/PHPExcel.php');
require_once('/Classes/PHPExcel/IOFactory.php');
class Upload{

	public $id;
	public $filename;
	public $type;
	public $size;
	public $caption;
	private $temp_path;
  	protected $upload_dir="C:/wamp/www/ApprovalSystem/files";
  	
  	public $errors=array();
  
  	protected $upload_errors = array(
		// http://www.php.net/manual/en/features.file-upload.errors.php
	UPLOAD_ERR_OK 			=> "No errors.",
	UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
	UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
	UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
	UPLOAD_ERR_NO_FILE 		=> "No file.",
	UPLOAD_ERR_NO_TMP_DIR 	=> "No temporary directory.",
	UPLOAD_ERR_CANT_WRITE 	=> "Can't write to disk.",
	UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
	);

	// Pass in $_FILE(['uploaded_file']) as an argument
  public function attach_file($file) {
		// Perform error checking on the form parameters
		if(!$file || empty($file) || !is_array($file)) {
		  // error: nothing uploaded or wrong argument usage
		  $this->errors[] = "No file was uploaded.";
		  return false;
		} elseif($file['error'] != 0) {
		  // error: report what PHP says went wrong
		  $this->errors[] = $this->upload_errors[$file['error']];
		  return false;
		} else {
			// Set object attributes to the form parameters.
		  $this->temp_path  = $file['tmp_name'];
		  $this->filename   = basename($file['name']);
		  $this->type       = $file['type'];
		  $this->size       = $file['size'];
			// Don't worry about saving anything to the database yet.
			return true;

		}
	}
  
	public function save($flag) {
		$uploadFlag=$flag;
		if(isset($this->id)) {
			// Really just to update the caption
			$this->update();
		} else {
		  if(!empty($this->errors)) { return false; }
		  if(strlen($this->caption) > 255) {
				$this->errors[] = "The caption can only be 255 characters long.";
				return false;
			}
		  if(empty($this->filename) || empty($this->temp_path)) {
		    $this->errors[] = "The file location was not available.";
		    return false;
		  }
		$target_path = $this->upload_dir ."/". $this->filename;
		  
		if(file_exists($target_path)) {
		    $this->errors[] = "The file {$this->filename} already exists.";
		    return false;
		}

		if(move_uploaded_file($this->temp_path, $target_path)) {
			if($uploadFlag == 'Depot'){
				if($this->createDepot()) {
					unset($this->temp_path);
					return true;
				}
			}
				if($uploadFlag == 'Region'){
					if($this->createRegion()) {
						unset($this->temp_path);
						return true;
					}
				}
				if($uploadFlag == 'Manpower'){
					if($this->createManpower()) {
						unset($this->temp_path);
						return true;
					}
				}

			} else {
				// File was not moved.
		    $this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
		    return false;
			}
		}
	}
	


	public function image_path() {
	  return $this->upload_dir."/".$this->filename;
	}
	
	public function size_as_text() {
		if($this->size < 1024) {
			return "{$this->size} bytes";
		} elseif($this->size < 1048576) {
			$size_kb = round($this->size/1024);
			return "{$size_kb} KB";
		} else {
			$size_mb = round($this->size/1048576, 1);
			return "{$size_mb} MB";
		}
	}
	
	private static function instantiate($record) {
		// Could check that $record exists and is an array
    $object = new self;
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	//For uploading Employee Details
	public function createDepot() {
	global $database;
	$path = $this->image_path();

	$objPHPExcel = PHPExcel_IOFactory::load($path);

	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;

	    for ($row = 1; $row <= $highestRow; ++ $row) {
	        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
	            $cell = $worksheet->getCellByColumnAndRow($col, $row);
	            $val = $cell->getValue();
	            $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
	        }
	    }
	}

	for ($row = 2; $row <= $highestRow; ++ $row) {
		$val=array();
		for ($col = 0; $col < $highestColumnIndex; ++ $col) {
		   $cell = $worksheet->getCellByColumnAndRow($col, $row);
		   $val[] = $cell->getValue();

		}

		$depot = new Depot();
		$depot->depot_id =$depot->autoGenerate_id(); 
		$sql="INSERT INTO depot(depot_id, depot_code,depot_name)
		VALUES ('".$depot->depot_id . "','" . $val[0] . "','" . $val[1] . "')";
   		$database->query($sql);

	}
}

	//For uploading BM Details
	public function createRegion() {
	global $database;
	$path = $this->image_path();

	$objPHPExcel = PHPExcel_IOFactory::load($path);

	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;

	    for ($row = 1; $row <= $highestRow; ++ $row) {
	        
	        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
	            $cell = $worksheet->getCellByColumnAndRow($col, $row);
	            $val = $cell->getValue();
	            $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);  
	        }
	    }
	}

	for ($row = 2; $row <= $highestRow; ++ $row) {
		$val=array();
		for ($col = 0; $col < $highestColumnIndex; ++ $col) {
		   $cell = $worksheet->getCellByColumnAndRow($col, $row);
		   $val[] = $cell->getValue();
		}

		$RegionId = new Region();
		$RegionId->region_id = $RegionId->autoGenerate_id();

		$sql="INSERT INTO region(region_id,region_name,depot,depot_code)
		VALUES ('". $RegionId->region_id  . "','" . $val[0] . "','".$val[1] . "','" . $val[2] . "')";
    	$database->query($sql);

	}
}

	//For uploading SM Details
	public function createManpower() {
	global $database;
	$path = $this->image_path();

	$objPHPExcel = PHPExcel_IOFactory::load($path);

	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;

	for ($row = 1; $row <= $highestRow; ++ $row) {
		echo '<tr>';
	        for ($col = 0; $col < $highestColumnIndex; ++ $col) {
	            $cell = $worksheet->getCellByColumnAndRow($col, $row);
	            $val = $cell->getValue();
	            $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
	        }
	    }
	}

	for ($row = 2; $row <= $highestRow; ++ $row) {
		$val=array();
		for ($col = 0; $col < $highestColumnIndex; ++ $col) {
		   $cell = $worksheet->getCellByColumnAndRow($col, $row);
		   $val[] = $cell->getValue();
		}


		$ManpowerId = new Manpower();
		$ManpowerId->man_id = $ManpowerId->autoGenerate_id();
		$getRegionId =Region::find_by_region_name($val[1]);
		if(!empty($getRegionId)){
			$region_id = $getRegionId->region_id;
		}else{
			$region_id ='';
		}
		
		$sql="INSERT INTO manpower(man_id,division,region_id,region_name,no_of_persons)
		VALUES ('". $ManpowerId->man_id . "','" . $val[0] . "','".$region_id . "','".$val[1] . "','" . $val[2] . "')";
    	$database->query($sql);

	}
	}
}?>