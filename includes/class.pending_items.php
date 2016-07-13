<?php
require_once('database.php');
class PendingItems{
	
	protected static $table_name="pending_items";
	protected static $db_fields = array('item_id');
	public $item_id;
	//public $depot_code;


	// TM Report filters
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}   



 	public static function find_by_region_id($region_id){
	$result_array= self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE region_id='$region_id' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
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


  	//Required for autogenerating id
	public static function count_all() {
    global $database;
    $sql = "SELECT COUNT(id) FROM ".self::$table_name;
    $result_set = $database->query($sql);
    $row = $database->fetch_array($result_set);
    	return array_shift($row);
    }

	private static function instantiate($record) {	
    $object = new self;
		foreach($record as $attribute=>$value){
			if($object->has_attribute($attribute)) {
		    	$object->$attribute = $value;
		  	}
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
		return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
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

	public function update($empid) {
	global $database;
	$attributes = $this->sanitized_attributes();
	$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE empid ='{$empid}'";
		$database->query($sql);
	  	return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;

	  $sql = "DELETE FROM ".self::$table_name;
	  $sql .= " WHERE id=". $database->escape_value($this->id);
	  $sql .= " LIMIT 1";
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;

	}

	public function autoGenerate_id(){
    $num = self::count_all();
    ++$num; // add 1;
    return 'RG'.$num;
	}

}
?>