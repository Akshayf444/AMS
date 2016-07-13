<?php
require_once('database.php');
class BrandBudget{
	
	protected static $table_name="brand_budget";
	protected static $db_fields = array('brand_id','brand_name', 'qtr1', 'qtr2','qtr3','qtr4','qtr1_remaining','qtr2_remaining','qtr3_remaining','qtr4_remaining');
	public $brand_id;
	public $brand_name;
	public $qtr1;
	public $qtr2;
	public $qtr3;
	public $qtr4;
	public $qtr1_remaining;
	public $qtr2_remaining;
	public $qtr3_remaining;
	public $qtr4_remaining;

	// TM Report filters
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}

  	public static function find_by_brand_id($brand_id) {
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name ." WHERE brand_id = '$brand_id' ");
		return !empty($result_array) ? array_shift($result_array) : false;
  	}

  	  	public static function find_by_brand_name($brand_name) {
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name ." WHERE brand_name = '$brand_name' ");
		return !empty($result_array) ? array_shift($result_array) : false;
  	}

 	//Select Brands for adding budget    
 	public static function find_by_gpm_empid($gpm_empid){
 		$budgetList =array();
 		$brands =Brand::find_by_gpm_empid($gpm_empid);
 		foreach ($brands as $brand) {
 			$result = self::find_by_brand_id($brand->brand_id);
 			array_push($budgetList, $result);
 		}
 		return $budgetList;
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

	public static function count_all($bmempid) {
    global $database;
    $sql = "SELECT COUNT(*) FROM ".self::$table_name." WHERE bm_empid='$bmempid'";
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

	public function update($brand_id) {
	global $database;
	$attributes = $this->sanitized_attributes();
	$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE brand_id ='{$brand_id}'";
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

	public function SelectiveBudgetUpdate($column_name,$value,$brand_name){
		global $database;
		$sql ="UPDATE ".self::$table_name." SET $column_name ='$value' WHERE brand_name = '$brand_name' ";
		$database->query($sql);
	  	return ($database->affected_rows() == 1) ? true : false;
	}

	/*public function autoGenerate_id(){
    $num = self::count_all();
    ++$num; // add 1;

    $len = strlen($num);
    for($i=$len; $i< 4; ++$i) {
        $num = '0'.$num;
    }
    return 'BR'.$num;

	}*/

}
?>