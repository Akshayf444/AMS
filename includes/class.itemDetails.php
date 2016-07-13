<?php

require_once('database.php');

class ItemDetails {

    protected static $table_name = "item_details";
    protected static $db_fields = array('item_id', 'brand_id', 'item_category', 'description', 'quantity', 'value', 'amount', 'apr_id', 'brand_count', 'allocated');
    public $item_id;
    public $brand_id;
    public $item_category;
    public $description;
    public $quantity;
    public $value;
    public $amount;
    public $apr_id;
    public $brand_count;
    public $allocated;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_apr_id($apr_id) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id = '$apr_id' ");
    }

    public static function find_quarterwise_items($quarter) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE month(date) $quarter ) ";
        return self::find_by_sql($sql);
    }

    public static function find_quarter_and_categorywise_items($quarter, $category) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE month(date) $quarter )  AND item_category = '$category' ";
        return self::find_by_sql($sql);
    }

    public static function find_quarter_and_categorywise_items_PMT($quarter, $category, $empid) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE  empid ='$empid' AND  month(date) $quarter )  AND item_category = '$category' ";
        return self::find_by_sql($sql);
    }

    public static function find_all_categorywise($category) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE item_category = '$category' ";
        return self::find_by_sql($sql);
    }

    public static function find_all_categorywise_PMT($category, $empid) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE item_category = '$category' AND apr_id IN( SELECT apr_id FROM approvals WHERE empid = '$empid' ) ";
        return self::find_by_sql($sql);
    }

    public static function find_by_item_id($item_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE  item_id = {$item_id} ");
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

    public static function list_category($empid) {
        $category_list = array();
        global $database;
        $sql = "SELECT DISTINCT (item_category) AS List FROM " . self::$table_name . " WHERE apr_id IN (
  			SELECT apr_id FROM approvals WHERE empid ='$empid'
  		) ";

        $result_set = $database->query($sql);
        while ($row = $database->fetch_array($result_set)) {
            array_push($category_list, $row['List']);
        }

        return $category_list;
    }

    public static function uniqueBrands($apr_id) {
        $brands = ItemDetails::find_by_apr_id($apr_id);
        $allBrands = array();
        $finalBrands = array();
        foreach ($brands as $brand) {
            $allBrands = preg_split('/,/', $brand->brand_id);
            foreach ($allBrands as $value) {
                array_push($finalBrands, $value);
                //$division =Brand::find_division1($value);
            }
        }
        return $finalBrands;
    }

    public static function brandDropdown($apr_id, $type) {

        $dropdownList = '';
        //echo $apr_id;
        if ($type == 0) {
            $finalBrandList = $apr_id;
        } else {
            $finalBrands = ItemDetails::uniqueBrands($apr_id);
            $finalBrandList = join(", ", array_unique($finalBrands));
        }

        $brands = explode(",", $finalBrandList);
        $firstElement = array_shift($brands);

        $BrandCount = count($brands);

        $dropdownList = '<div class="dropdown">';

        if ($BrandCount != 0) {
            $brandName = Brand::find_by_brand_id2($firstElement);
            if (!empty($brandName)) {
                $dropdownList.= $brandName->brand_name . ' AND ' . $BrandCount . ' More';
            }
        } else {
            $brandName = Brand::find_by_brand_id2($firstElement);
            if (!empty($brandName)) {
                $dropdownList.= $brandName->brand_name;
            }
        }

        if (!empty($brands)) {
            $dropdownList.= '<b class="caret"></b>
            <ul class="dropdown-menu">';
            foreach ($brands as $brand) {
                $brandName = Brand::find_by_brand_id2($brand);
                if (!empty($brandName)) {
                    $dropdownList.= '<li><a href="#"> ' . $brandName->brand_name . '</a></li>';
                }
            }
            $dropdownList.='</ul></div>';
        }

        return $dropdownList;
    }

    public static function PRElist_category() {
        $category_list = array();
        global $database;
        $sql = "SELECT DISTINCT (item_category) AS List FROM " . self::$table_name;

        $result_set = $database->query($sql);
        while ($row = $database->fetch_array($result_set)) {
            array_push($category_list, $row['List']);
        }
        return $category_list;
    }

    public static function find_quarterwise_expense($empid, $category, $quarter) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE empid = '$empid' AND month(date) $quarter ) AND item_category = '$category' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_numeric(array_shift($row))) {
            return array_shift($row);
        } else {
            return '0';
        }
    }

    public static function PREfind_quarterwise_expense($category, $quarter) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE month(date) $quarter ) AND item_category = '$category' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_numeric(array_shift($row))) {
            return array_shift($row);
        } else {
            return '0';
        }
    }

    public static function GPMfind_quarterwise_expense($gpm_empid, $category, $quarter) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE apr_id IN( 
  						SELECT apr_id FROM approvals WHERE month(date) $quarter AND empid IN( 
  						  SELECT empid FROM employees WHERE gpm_empid = '$gpm_empid')
  						) AND item_category = '$category' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_numeric(array_shift($row))) {
            return floatval(array_shift($row));
        } else {
            return '0';
        }
    }

    public static function MMfind_quarterwise_expense($mm_empid, $category, $quarter) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE apr_id IN( 
                        SELECT apr_id FROM approvals WHERE month(date) $quarter AND empid IN( 
                          SELECT empid FROM employees WHERE gpm_empid IN (
                            SELECT gpm_empid FROM gpm WHERE MM_empid = '$mm_empid'))
                        ) AND item_category = '$category' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_numeric(array_shift($row))) {
            return floatval(array_shift($row));
        } else {
            return '0';
        }
    }

    //For replacing employee ids............
    public static function find_by_empid($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id IN ( SELECT apr_id FROM approvals WHERE empid ='$empid' ) ");
    }

    public static function find_equal_items($apr_id) {
        return self::find_by_sql("SELECT * FROM item_details WHERE quantity IN (
   			SELECT quantity FROM item_details GROUP BY quantity HAVING count(*) > 2 AND apr_id ='$apr_id'
		)");
    }

    //FOR TEMPORARY BUDGET TABLE AS WELL AS BRANDWISE EXPENSE REPORT // 
    //************ DO NOT TOUCH HERE ***************//
    public static function find_brandwise_expense($Brand, $quarter) {
        $expense = 0;
        $Items = ItemDetails::find_quarterwise_items($quarter);

        foreach ($Items as $Item) {

            $availableBrands = explode(",", $Item->brand_id);
            $brandCount = $Item->brand_count;
            if (in_array($Brand, $availableBrands)) {
                $expense += ($Item->amount / $brandCount );
            }
        }
        return floatval($expense);
    }

    public static function edit_brandwise_expense($Brand, $quarter, $apr_id) {
        $expense = 0;
        $Items = ItemDetails::find_quarterwise_items($quarter);

        foreach ($Items as $Item) {

            $availableBrands = explode(",", $Item->brand_id);
            $brandCount = $Item->brand_count;
            if (in_array($Brand, $availableBrands) && $Item->apr_id != $apr_id) {
                $expense += ($Item->amount / $brandCount );
            }
        }
        return floatval($expense);
    }

    //************ DO NOT TOUCH HERE ***************//

    public static function find_brand_categorywise_expense($Brand, $quarter, $category) {
        $expense = 0;
        if ($quarter == 'null') {
            $Items = ItemDetails::find_all_categorywise($category);
        } else {
            $Items = ItemDetails::find_quarter_and_categorywise_items($quarter, $category);
        }

        foreach ($Items as $Item) {

            $availableBrands = explode(",", $Item->brand_id);
            $brandCount = $Item->brand_count;
            if (in_array($Brand, $availableBrands)) {
                $expense += ($Item->amount / $brandCount );
            }
        }
        return floatval($expense);
    }

    public static function find_brand_categorywise_expense_PMT($empid, $quarter, $category) {
        $expense = 0;
        if ($quarter == 'null') {
            $Items = ItemDetails::find_all_categorywise_PMT($category, $empid);
        } else {
            $Items = ItemDetails::find_quarter_and_categorywise_items_PMT($quarter, $category, $empid);
        }

        foreach ($Items as $Item) {

            $expense += ($Item->amount );
        }
        return floatval($expense);
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
        $sql = "SELECT COUNT(item_id) FROM " . self::$table_name;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_by_apr_id($apr_id) {
        global $database;
        $sql = "SELECT COUNT(item_id) FROM " . self::$table_name . " WHERE apr_id ='$apr_id' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function countApproved($empid, $status) {
        global $database;
        $sql = "SELECT COUNT(item_id) FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE empid = '$empid' AND status = '$status')  ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function PREcountApproved($status) {
        global $database;
        $sql = "SELECT COUNT(item_id) FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE status = '$status')  ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function listApproved($empid, $status) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE empid = '$empid' AND status = '$status')");
    }

    public static function PRElistApproved($status) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE  status = '$status')");
    }

    public static function drawPieChart($empid, $Category) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE item_category ='$Category' AND apr_id IN (
    	SELECT apr_id FROM approvals WHERE empid ='$empid'
    ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['0'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function PREdrawPieChart($Category) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE item_category ='$Category' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['0'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function GPMdrawPieChart($gpm_empid, $Category) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE item_category ='$Category' AND apr_id IN (
      SELECT apr_id FROM approvals WHERE empid IN(
        SELECT empid FROM employees WHERE gpm_empid = '$gpm_empid'
      )
    ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['0'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function MMdrawPieChart($mm_empid, $Category) {
        global $database;
        $sql = "SELECT SUM(amount) FROM " . self::$table_name . " WHERE item_category ='$Category' AND apr_id IN (
      SELECT apr_id FROM approvals WHERE empid IN(
        SELECT empid FROM employees WHERE gpm_empid IN (
            SELECT gpm_empid FROM gpm WHERE MM_empid = '{$mm_empid}'
        )
      )
    ) ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['0'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function listItemWiseExpense($empid, $Category) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE item_category ='$Category' AND apr_id IN (
	    	SELECT apr_id FROM approvals WHERE empid ='$empid'
	    ) ";
        return self::find_by_sql($sql);
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

    public function update($item_id) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE item_id = {$item_id}";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function Update_key_no($item_id, $key_no) {
        global $database;
        $sql = " UPDATE " . self::$table_name . " SET key_no = '$key_no' WHERE item_id = {$item_id} ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete($item_id) {
        global $database;
        $sql = "DELETE FROM " . self::$table_name;
        $sql .= " WHERE item_id ={$item_id} ";
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function autoGenerate_id() {
        $num = self::count_all();
        ++$num; // add 1;
        return 'IT' . $num;
    }

    public function update_alloc_status($item_id, $status) {
        global $database;
        $sql = "UPDATE item_details SET allocated = '$status'  WHERE item_id ={$item_id} ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>