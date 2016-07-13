<?php

require_once('database.php');
class SM {

    protected static $table_name = "sm";
    protected static $db_fields = array('sm_empid', 'name', 'region', 'division','password');
    public $sm_empid;
    public $name;
    public $region;
    public $division;
    public $password;
    
//    public $mobile;
//    public $division;
//    public $complete_profile;
//    
    public static function authenticate($empid = "", $password = "") {
        global $database;
        $empid = $database->escape_value($empid);
        $password = $database->escape_value($password);

        $sql = "SELECT * FROM sm ";
        $sql .= "WHERE  sm_empid = '{$empid}' ";
        $sql .= "AND  password = '{$password}' ";
        $sql .= "LIMIT 1";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_empid($empid) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE sm_empid = '$empid' ");
        return !empty($result_array) ? array_shift($result_array) : false;
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

    public function update($empid) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE MM_empid ='{$empid}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function assignDivision($empid) {
        global $database;
        
        $sql = "UPDATE " . self::$table_name . " SET division = '{$this->division}'";
        $sql .= " WHERE MM_empid ='{$empid}'";
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

    public static function changePassword($newPassword,$empid){
        $sql = "UPDATE " . self::$table_name . " SET password ='$newPassword' WHERE gpm_empid = '$empid' ";
        
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>