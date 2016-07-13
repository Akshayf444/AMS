<?php

require_once('database.php');

class AllocationDetails {

    protected static $table_name = "allocation_details";
    protected static $db_fields = array('alloc_id', 'depot_id', 'region_id', 'item_id', 'no_of_persons', 'qty_per_person', 'total_quantity');
    public $alloc_id;
    public $depot_id;
    public $region_id;
    public $item_id;
    public $no_of_persons;
    public $qty_per_person;
    public $total_quantity;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_alloc_id($alloc_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE alloc_id = '{$alloc_id}' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_item_id($item_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE item_id = '$item_id' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_item_category($apr_id) {
        $category_list = array();
        global $database;
        $sql = "SELECT DISTINCT(item_category) AS List FROM " . self::$table_name . " WHERE apr_id = '$apr_id' ";
        $result_set = $database->query($sql);
        while ($row = $database->fetch_array($result_set)) {
            array_push($category_list, $row['List']);
        }
        return implode(", ", $category_list);
    }

    public static function find_by_item_id2($item_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE item_id = '$item_id' ");
    }
    
    public static function find_by_region_id($region_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE region_id = '$region_id' ");
    }

    public static function ready_for_dispatch($region_id){
        $finalAllocationList = array();
        $AllocationList = AllocationDetails::find_by_region_id($region_id);
        foreach ($AllocationList as $Allocation) {
            $dispatched = GRN::isReadyForDispatch($Allocation->item_id);
            if ($dispatched) {
                $SingleAllocation = AllocationDetails::find_by_alloc_id($Allocation->alloc_id);
                array_push($finalAllocationList, $SingleAllocation->alloc_id);
            }  
        }
        
        return $finalAllocationList;
    }

    //For replacing employee ids............
    public static function find_by_empid($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE emp_id='$empid'");
    }

    public static function find_equal_items($apr_id) {
        return self::find_by_sql("SELECT * FROM item_details WHERE quantity IN (
   			SELECT quantity FROM item_details GROUP BY quantity HAVING count(*) > 1 AND apr_id ='$apr_id'
		)");
    }

    //For Checking Alloction Status....
    public static function allocated($apr_id) {
        $itemCount = ItemDetails::count_by_apr_id($apr_id);
        $Approval = Approval::find_by_apr_id2($apr_id);
        $AllocationCount = 0;
        $approvalId = $apr_id;
        $Items = ItemDetails::find_by_apr_id($approvalId);
        if (!empty($Items)) {
            foreach ($Items as $Item) {
                $found_Allocation = self::find_by_item_id2($Item->item_id);
                $ItemStatus = ItemDetails::find_by_item_id($Item->item_id);
                if (!empty($found_Allocation) && ($ItemStatus->allocated == 0)) {
                    $AllocationCount++;
                }
            }
        }
        if ($AllocationCount == $itemCount) {
            return "Allocated";
        } else {
            return 'Allocate';
        }
    }

    public static function find_allocated_quantity($item_id) {
        global $database;
        $sql = "SELECT SUM(total_quantity) FROM " . self::$table_name . " WHERE item_id ='$item_id' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row[0])) {
            return '';
        } else {
            return array_shift($row);
        }
    }

    public static function find_qty_per_person($item_id) {
        global $database;
        $sql = "SELECT DISTINCT(qty_per_person) FROM " . self::$table_name . " WHERE item_id ='$item_id' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row[0])) {
            return '';
        } else {
            return array_shift($row);
        }
    }

    public static function find_alloc_id($item_id, $region_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE item_id = '$item_id' AND region_id ='$region_id' LIMIT 1");
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

    public function update($alloc_id) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE alloc_id ='{$alloc_id}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function autoGenerate_id() {
        $num = self::count_all();
        ++$num; // add 1;
        return 'ALOC' . $num;
    }

}

?>