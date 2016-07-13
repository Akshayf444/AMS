<?php

require_once('database.php');

class Brand {

    protected static $table_name = "brand";
    protected static $db_fields = array('brand_id', 'brand_name', 'div_id', 'status');
    public $brand_id;
    public $brand_name;
    public $div_id;
    public $status;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_brand_id($brand_id="") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE brand_id = {$brand_id} ");
    }

    public static function find_by_brand_id2($brand_id="") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE brand_id = '{$brand_id}' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_brand_name($brand_name="") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE brand_name = '$brand_name' ");
    }

    public static function find_by_empid($empid ="") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE emp_id='$empid'");
    }

    //Select Brands for adding budget    
    public static function find_by_gpm_empid($gpm_empid="") {

        $sql = "SELECT * FROM " . self::$table_name . " WHERE emp_id IN ( SELECT empid FROM employees WHERE gpm_empid ='$gpm_empid' )";
        return self::find_by_sql($sql);
    }

    //Assigning brands to employee

    public static function find_by_division($division="") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE div_id='$division'  AND status = 0 ");
    }
    
    public static function brand_array_by_division($division){
        $BrandList = array();
        $Brands = self::find_by_division($division);
        foreach ($Brands as $brand) {
            array_push($BrandList, $brand->brand_id);
        }
        return $BrandList;
    }

    public static function find_by_division1($division) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE div_id='$division'  ");
    }


    public static function find_division1($brand_name="") {
        global $database;
        $sql = "SELECT div_id FROM " . self::$table_name . " WHERE brand_name = '$brand_name' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row)) {
            return '';
        }  else {
            return array_shift($row);
        }
        
    }

    public static function find_by_sql($sql = "") {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    private static function instantiate($record) {
        $object = new self;
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
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
        foreach (self::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    protected function sanitized_attributes() {
        global $database;
        $clean_attributes = array();
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    public function create() {
        global $database;
        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . self::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        if ($database->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($brand_name="") {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE brand_name ='{$brand_name}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    //For Adding Budget For Brand
    public function SelectiveUpdate($brand_budget, $remaining_budget, $brand_name) {
        global $database;
        $sql = "UPDATE " . self::$table_name . " SET brand_budget = '$brand_budget', remaining_budget ='$remaining_budget' WHERE brand_name = '$brand_name' ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function SelectiveBudgetUpdate($remaining_budget, $brand_name) {
        global $database;
        $sql = "UPDATE " . self::$table_name . " SET remaining_budget ='$remaining_budget' WHERE brand_name = '$brand_name' ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete() {
        global $database;

        $sql = "DELETE FROM " . self::$table_name;
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function autoGenerate_id() {
        $num = self::count_all();
        ++$num; // add 1;

        return 'BR' . $num;
    }

    public static function ManageBrand($brand_id, $status) {
        $sql = "UPDATE " . self::$table_name . " SET status = $status WHERE brand_id = {$brand_id} ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>