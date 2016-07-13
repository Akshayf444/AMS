<?php

require_once('database.php');

class PrDetails {

    protected static $table_name = "pr_details";
    protected static $db_fields = array('pr_id', 'apr_id', 'item_id', 'key_no', 'pr_date');
    public $pr_id;
    public $apr_id;
    public $item_id;
    public $key_no;
    public $pr_date;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_apr_id($apr_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id='$apr_id' LIMIT 1 ");
    }

    public static function find_by_status($status, $empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE status ='$status' AND empid = '$empid' ");
    }

    public static function find_by_item_id($item_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE item_id = '$item_id' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_key_no($key_no) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE key_no = '$key_no' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function SearchKeyNo($key_no) {
        $names = array();
        $sql = "SELECT key_no FROM pr_details WHERE key_no LIKE '%$key_no%' ";
        global $database;
        $result = $database->query($sql);
        while ($row = $database->fetch_array($result)) {
            echo "<li>" . $row['key_no'] . "</li>";
        }
    }

    public static function proceed($apr_id) {
        $itemCount = ItemDetails::count_by_apr_id($apr_id);
        $POCount = 0;
        $approvalId = $apr_id;
        $Items = ItemDetails::find_by_apr_id($approvalId);
        if (!empty($Items)) {
            foreach ($Items as $Item) {
                $found_pr = self::find_by_item_id($Item->item_id);
                if (!empty($found_pr)) {
                    $POCount++;
                }
            }
        }

        if ($POCount == $itemCount) {
            return true;
        } else {
            return false;
        }
    }

    public static function find_pr_date($apr_id) {
        global $database;
        $result_set = $database->query("SELECT DISTINCT(pr_date) FROM " . self::$table_name . " WHERE apr_id='$apr_id' ");
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

    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(id) FROM " . self::$table_name;
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

    public function update($apr_id) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE apr_id ='{$apr_id}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function updateStatus($apr_id, $status) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET status = '$status' ";
        $sql .= " WHERE apr_id ='{$apr_id}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function update_PRE_status($apr_id, $status, $column_name) {
        global $database;
        $sql = "UPDATE " . self::$table_name . " SET '$column_name' = '$status' WHERE apr_id = '$apr_id' ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    //For admin

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
        return 'PR' . $num;
    }

}

?>