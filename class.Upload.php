<?php
require_once('database.php');

class Upload{
	protected static $table_name="ppt";
	protected static $db_fields = array('filename', 'type', 'size','category','title','description','ppt_id');

	public $filename;
	public $type;
	public $size;
	public $category;
	public $title;
	public $description;
	public $ppt_id;
	
	private $temp_path;
  	protected $upload_dir="/home/a9470696/public_html/files";
  	
  	public $errors=array();
	public static function find_by_ppt_id($ppt_id){
		return self::find_by_sql("SELECT * FROM ppt WHERE ppt_id = '$ppt_id' LIMIT 1");
	}
	
	public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    	return $object_array;
  	}

  	protected $upload_errors = array(
		// http://www.php.net/manual/en/features.file-upload.errors.php
	UPLOAD_ERR_OK 			=> "No errors.",
	UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
	UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
	UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
	UPLOAD_ERR_NO_FILE 		=> "No file Selected.",
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
  
	public function save() {
		
		  if(!empty($this->errors)) { return false; }

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
			$this->create();
			
			unset($this->temp_path);
			return true;
			} else {
				// File was not moved.
		    $this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
		    return false;
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
	
	public static function count_all() {
    global $database;
    $sql = "SELECT COUNT(id) FROM ".self::$table_name;
    $result_set = $database->query($sql);
    $row = $database->fetch_array($result_set);
    	return array_shift($row);
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
	
	public function create() {
	global $database;
	$attributes = $this->sanitized_attributes();
	$sql = "INSERT INTO ".self::$table_name." (";
	$sql .= join(", ", array_keys($attributes));
	$sql .= ") VALUES ('";
	$sql .= join("', '", array_values($attributes));
	$sql .= "')";
		if($database->query($sql)) {
	    	return true;
	  	}else{
	    	return false;
	  	}
	}
	
	public function autoGenerate_id(){
    $num = self::count_all();
    ++$num; // add 1;
    return 'PPT'.$num;
	}

}?>