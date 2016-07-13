<?php

require_once('database.php');

class TempBudget {

    protected static $table_name = "temperorybudget";
    protected static $db_fields = array('brand', 'budget', 'empid');
    public $brand;
    public $budget = 0;
    public $empid;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_brand_id($brand_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE brand_id = '$brand_id' ");
    }

    public static function find_by_brand_name($brand_name,$empid) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE brand = '$brand_name' AND empid = '$empid' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_empid($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE emp_id='$empid'");
    }

    public static function truncate($empid) {
        $sql = "DELETE FROM " . self::$table_name ." WHERE empid = '$empid' ";
        global $database;
        $database->query($sql);
    }

    //Select Brands for adding budget    
    public static function find_by_gpm_empid($gpm_empid) {

        $sql = "SELECT * FROM " . self::$table_name . " WHERE emp_id IN ( SELECT empid FROM employees WHERE gpm_empid ='$gpm_empid' )";
        return self::find_by_sql($sql);
    }

    //Assigning brands to employee

    public static function find_by_division($division) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE div_id='$division'");
    }

    public static function find_division1($brand_name) {
        global $database;
        $sql = "SELECT div_id FROM " . self::$table_name . " WHERE brand_name = '$brand_name' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
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

    public static function count_all($bmempid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE bm_empid='$bmempid'";
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

    public function update($brand_name) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET budget = '$this->budget' ";
        
        $sql .= " WHERE brand ='{$brand_name}'";
        //echo $sql;
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

}

?>