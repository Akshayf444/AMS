<?php

require_once('database.php');

class Employee {

    protected static $table_name = "employees";
    protected static $db_fields = array('empid', 'name', 'emailid', 'password', 'type', 'gpm_empid', 'mobile', 'status', 'complete_profile', 'division');
    public $empid;
    public $name;
    public $emailid;
    public $password;
    public $type;
    public $gpm_empid;
    public $mobile;
    public $status;
    public $complete_profile;
    public $division;

    public static function authenticate($empid = "", $password = "") {
        global $database;
        $empid = $database->escape_value($empid);
        $password = $database->escape_value($password);

        $sql = "SELECT * FROM employees ";
        $sql .= "WHERE  empid = '{$empid}' ";
        $sql .= "AND  password = '{$password}' ";
        $sql .= "AND  status = 1 ";
        $sql .= "LIMIT 1";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_division($division = "") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE division='$division'");
    }

    public static function find_by_gpm($gpm_empid = "") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE gpm_empid='$gpm_empid'");
    }

    public static function find_by_mm($mm_empid = "") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE gpm_empid IN ( SELECT  gpm_empid FROM gpm WHERE MM_empid = '$mm_empid') ");
    }

    public static function find_by_pre_empid($pre_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE pre_empid='$pre_empid'");
    }

    public static function sendAllocationCopy($item_id, $gpm_empid) {
        $finalDepoName = '';

        $GPMdetails = GPM::find_by_empid($gpm_empid);
        $Items = AllocationDetails::find_by_item_id2($item_id);
        $AllocatedQuantity = AllocationDetails::find_allocated_quantity($item_id);
        $OrderedQuantity = ItemDetails::find_by_item_id($item_id);
        $balance = $OrderedQuantity->quantity - $AllocatedQuantity;
        $ItemTable = '';
        $ItemTable .='<table style="border:1px solid #ddd;margin-top:1em">
			<tr style="border:1px solid #ddd;">
                            <th style="border:1px solid #ddd;">Item Code</th>
                            <td style="border:1px solid #ddd;">' . $item_id . '</td>
			</tr>
			<tr>
                            <th style="border:1px solid #ddd;">Item Name</th>
                            <td style="border:1px solid #ddd;">' . $OrderedQuantity->description . '</td>
			</tr>
			<tr>
                            <th style="border:1px solid #ddd;">Quantity Ordered</th>
                            <td style="border:1px solid #ddd;">' . $OrderedQuantity->quantity . '</td>
			</tr>
			<tr>
                            <th style="border:1px solid #ddd;">Quantity Allocated</th>
                            <td style="border:1px solid #ddd;">' . $AllocatedQuantity . '</td>
			</tr>
			<tr>
                            <th style="border:1px solid #ddd;">Remaining At CWH</th>
                            <td style="border:1px solid #ddd;">' . $balance . '</td>
			</tr>
			</table>';
        //echo $ItemTable;

        $message = '<table style="border:1px solid #ddd;margin-top:1em"><tr style="border:1px solid #ddd;"><th style="border:1px solid #ddd;">Region</th><th style="border:1px solid #ddd;">Depot</th><th style="border:1px solid #ddd;">Approved Manpower</th><th style="border:1px solid #ddd;">Total Quantity</th></tr>';
        if (!empty($Items)) {
            foreach ($Items as $Item) {
                $regionName = Region::find_by_region_id($Item->region_id);
                $depotName = Depot::find_by_depot_code($regionName->depot_id);
                if (isset($depotName->depot_name)) {
                    $finalDepoName = $depotName->depot_name;
                } else {
                    $finalDepoName = 'NA';
                }
                $message .='<tr style="border:1px solid #ddd;">
				<td style="border:1px solid #ddd;">' . $finalDepoName . '</td>
				<td style="border:1px solid #ddd;">' . $regionName->region_name . '</td>
				<td style="border:1px solid #ddd;">' . $Item->no_of_persons . '</td>
				<td style="border:1px solid #ddd;">' . $Item->total_quantity . '</td>
                            </tr>';
            }/*             * * End Of For ** */
        }/*         * * End Of iF ** */

        $message .='</table>';

        $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

        $mail->IsSMTP(); // telling the class to use SMTP

        try {
            $mail->SMTPAuth = true;                  // enable SMTP authentication
            $mail->SMTPSecure = "ssl";                 // sets the prefix to the server
            $mail->Host = "smtpout.asia.secureserver.net";      // sets the SMTP server
            $mail->Port = 465;                   // set the SMTP port for the MAIL server
            $mail->Username = "m@techvertica.in";  //  username
            $mail->Password = "Priyanka@123";            // password

            $mail->FromName = "AMS Admin";
            $mail->From = "m@techvertica.in";
            $mail->AddAddress($GPMdetails->emailid, "AMS Admin");

            $mail->Subject = "Balance At CWH";

            $mail->IsHTML(true);

            $mail->Body = <<<EMAILBODY

		<strong>Dear {$GPMdetails->name}<strong><br/>
			Allocation For Material has more than 30% quantity at CWH .<br/>
			Here is more infomation.
			{$ItemTable}

		<br/>This Is For Your Information.<br/>
			{$message}
		<br/><br/>
		Thank You.



EMAILBODY;

            $mail->Send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }

    public static function findReplace($empid) {
        global $database;
        $result = $database->query("SELECT * FROM " . self::$table_name . " WHERE bm_empid='$empid'");
        $row = $database->fetch_array($result);
        return $row;
    }

    public static function find_by_empid($empid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' LIMIT 1");
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

    public function update($empid) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE empid ='{$empid}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function updateGPM($empid) {
        global $database;
        $sql = "UPDATE employees SET gpm_empid = '$this->gpm_empid' WHERE gpm_empid = '$empid' ";
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
        //$number = getTotalEmployees();
        //$id = 'EP'.$number;
        $num = self::count_all();
        ++$num; // add 1;
        return 'EP' . $num;
    }

    public static function sendsms($mobileNo, $message) {
        $smsUser = 'manish';
        $smsPassword = '123456';
        $var = "user=" . $smsUser . "&password=" . $smsPassword . "&senderid=MSPSGC&mobiles=" . $mobileNo . "&sms=" . $message;
        $curl = curl_init('http://trans.smsmojo.in/sendsms.jsp');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    public static function ManageAccount($empid, $status) {
        $sql = "UPDATE " . self::$table_name . " SET status = $status WHERE empid = '$empid' ";

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function changePassword($newPassword, $empid) {
        $sql = "UPDATE " . self::$table_name . " SET password ='$newPassword' WHERE empid = '$empid' ";

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>