<?php

require_once('database.php');

class Approval {

    protected static $table_name = "approvals";
    protected static $db_fields = array('apr_id', 'title', 'vendor', 'location', 'remark', 'status', 'date', 'empid', 'process_for_po', 'order_status', 'receive');
    public $apr_id;
    public $title;
    public $vendor;
    public $location;
    public $remark;
    public $status = "Pending";
    public $date;
    public $empid;
    public $process_for_po;
    public $order_status;
    public $receive;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    //For preventing url manipulation.......
    public static function SecurityCheck($apr_id, $empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid ='$empid' AND apr_id = '{$apr_id}' ");
    }
    
    public static function EditSecurityCheck($apr_id, $empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid ='$empid' AND apr_id = '{$apr_id}' AND status ='Pending'   ");
    }

    public static function isApproved($item_id, $status) {
        $sql = "SELECT * FROM approvals WHERE apr_id IN (SELECT apr_id FROM item_details WHERE item_id = {$item_id} ) AND status = '$status' ";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function SearchName($title) {
        $names = array();
        $sql = "SELECT apr_id FROM approvals WHERE apr_id LIKE '%$title%' AND status = 'Approved' ";
        global $database;
        $result = $database->query($sql);
        while ($row = $database->fetch_array($result)) {
            echo "<li>" . $row['apr_id'] . "</li>";
        }
    }

    //For PRE
    public static function SecurityCheck2($apr_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id ='{$apr_id}' AND status = 'Approved' ");
    }

    public static function find_by_empid($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid ='$empid' ");
    }

    public static function find_by_empid2($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid ='$empid' AND status = 'Approved'");
    }

    public static function find_by_apr_id($apr_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id='{$apr_id}' LIMIT 1 ");
    }

    public static function find_by_status($status, $empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE status ='$status' AND empid = '$empid' ORDER BY apr_id DESC");
    }

    public static function find_by_status2($status) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE status ='$status' ORDER BY apr_id DESC");
    }

    //For Approval List in GPM Panel
    public static function find_by_gpm_empid($gpm_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid IN (SELECT empid FROM employees WHERE gpm_empid = '$gpm_empid') AND status = 'Approved' ");
    }
    
    public static function find_by_mm_empid($mm_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid IN ("
                . " SELECT empid FROM employees WHERE gpm_empid IN ("
                . " SELECT gpm_empid FROM gpm WHERE MM_empid = '$mm_empid' )) AND status = 'Approved' ");
    }


    public static function find_by_apr_id2($apr_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id='{$apr_id}' LIMIT 1 ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_apr_id3($apr_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id='{$apr_id}' AND status ='Approved' LIMIT 1 ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function approvalStatus($apr_id) {
        $status = array();

        $Items = ItemDetails::find_by_apr_id($apr_id);
        foreach ($Items as $Item) {
            $ItemStatus = GRN::isDelivered($Item->item_id);
            array_push($status, $ItemStatus);
        }

        return array_unique($status);
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
        $sql = "SELECT COUNT(apr_id) FROM " . self::$table_name;
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
            $this->apr_id = $database->insert_id();
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
        $sql .= " WHERE apr_id ='{$apr_id}' ";
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
        $sql = "UPDATE approvals SET $column_name= '$status'  WHERE apr_id ='{$apr_id}' ";
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
        return 'AP' . $num;
    }

}

?>