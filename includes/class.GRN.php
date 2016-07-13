<?php

require_once('database.php');

class GRN {

    protected static $table_name = "grn_details";
    protected static $db_fields = array('grn_id', 'apr_id', 'item_id', 'date', 'grn_date', 'quantity_received', 'quantity_remaining');
    public $grn_id = '';
    public $apr_id;
    public $item_id;
    public $date;
    public $grn_date;
    public $quantity_received;
    public $quantity_remaining;

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
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE item_id = '$item_id' ");
    }

    public static function find_by_grn_id($grn_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE grn_id = {$grn_id} ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_grn_date($grn_date) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE date = '$grn_date' LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_grn_date($apr_id) {
        global $database;
        $result_set = $database->query("SELECT DISTINCT(date) FROM " . self::$table_name . " WHERE apr_id='$apr_id' ");
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function find_by_item_id2($item_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE item_id = '$item_id' ORDER BY grn_id DESC");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_received_quantity($item_id) {
        global $database;
        $sql = "SELECT SUM(quantity_received) FROM " . self::$table_name . " WHERE item_id = '$item_id' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_numeric(array_shift($row))) {
            return array_shift($row);
        } else {
            return '0';
        }
    }

    public static function isProcessed($empid, $type) {
        $CountInProcess = 0;
        $CountProcessed = 0;
        $approvals = Approval::find_by_empid2($empid);
        foreach ($approvals as $approval) {
            $ItemDetails = ItemDetails::find_by_apr_id($approval->apr_id);
            foreach ($ItemDetails as $Item) {
                $AllocatedQuantity = ItemDetails::find_by_item_id($Item->item_id);
                $ReceivedQuantity = GRN::find_received_quantity($Item->item_id);
                if (!empty($ReceivedQuantity)) {
                    $finalReceived = $ReceivedQuantity;
                } else {
                    $finalReceived = 0;
                }

                if ($AllocatedQuantity->quantity != $finalReceived) {
                    $CountInProcess ++;
                }
                if ($AllocatedQuantity->quantity == $finalReceived) {
                    $CountProcessed ++;
                }
            }
        }

        if ($type == 'inProcess') {
            return $CountInProcess;
        }
        if ($type == 'Processed') {
            return $CountProcessed;
        }
    }

    public static function PREisProcessed($type) {
        $CountInProcess = 0;
        $CountProcessed = 0;
        $approvals = Approval::find_by_status2('Approved');
        foreach ($approvals as $approval) {
            $ItemDetails = ItemDetails::find_by_apr_id($approval->apr_id);
            foreach ($ItemDetails as $Item) {
                $AllocatedQuantity = ItemDetails::find_by_item_id($Item->item_id);
                $ReceivedQuantity = GRN::find_received_quantity($Item->item_id);
                if (!empty($ReceivedQuantity)) {
                    $finalReceived = $ReceivedQuantity;
                } else {
                    $finalReceived = 0;
                }

                if ($AllocatedQuantity->quantity != $finalReceived) {
                    $CountInProcess ++;
                }
                if ($AllocatedQuantity->quantity == $finalReceived) {
                    $CountProcessed ++;
                }
            }
        }

        if ($type == 'inProcess') {
            return $CountInProcess;
        }
        if ($type == 'Processed') {
            return $CountProcessed;
        }
    }

    public static function listItemsInProcess($empid, $type) {
        $ItemsInProcess = array();
        $ItemsProcessed = array();
        $approvals = Approval::find_by_empid2($empid);
        foreach ($approvals as $approval) {
            $ItemDetails = ItemDetails::find_by_apr_id($approval->apr_id);
            foreach ($ItemDetails as $Item) {
                $AllocatedQuantity = ItemDetails::find_by_item_id($Item->item_id);
                $ReceivedQuantity = GRN::find_received_quantity($Item->item_id);
                if (!empty($ReceivedQuantity)) {
                    $finalReceived = $ReceivedQuantity;
                } else {
                    $finalReceived = 0;
                }

                if ($AllocatedQuantity->quantity != $finalReceived) {
                    array_push($ItemsInProcess, $AllocatedQuantity->item_id);
                    //$CountInProcess ++ ;
                }
                if ($AllocatedQuantity->quantity == $finalReceived) {
                    array_push($ItemsProcessed, $AllocatedQuantity->item_id);
                    //$CountProcessed ++ ;
                }
            }/*             * * End Of inner for loop ** */
        }/*         * * End Of outer For loop ** */

        if ($type == 'inProcess') {
            return $ItemsInProcess;
        }
        if ($type == 'Processed') {
            return $ItemsProcessed;
        }
    }

    public static function PRElistItemsInProcess($type) {
        $ItemsInProcess = array();
        $ItemsProcessed = array();

        $approvals = Approval::find_by_status2('Approved');
        foreach ($approvals as $approval) {
            $ItemDetails = ItemDetails::find_by_apr_id($approval->apr_id);
            foreach ($ItemDetails as $Item) {
                $AllocatedQuantity = ItemDetails::find_by_item_id($Item->item_id);
                $ReceivedQuantity = GRN::find_received_quantity($Item->item_id);
                if (!empty($ReceivedQuantity)) {
                    $finalReceived = $ReceivedQuantity;
                } else {
                    $finalReceived = 0;
                }

                if ($AllocatedQuantity->quantity != $finalReceived) {
                    array_push($ItemsInProcess, $AllocatedQuantity->item_id);
                    //$CountInProcess ++ ;
                }
                if ($AllocatedQuantity->quantity == $finalReceived) {
                    array_push($ItemsProcessed, $AllocatedQuantity->item_id);
                    //$CountProcessed ++ ;
                }
            }/*             * * End Of inner for loop ** */
        }/*         * * End Of For ** */

        if ($type == 'inProcess') {
            /*             * * No of Items in process ** */
            return $ItemsInProcess;
        }
        if ($type == 'Processed') {
            /*             * * No of Delivered Items ** */
            return $ItemsProcessed;
        }
    }

    /*     * * Count No of Delivered items ** */

    public static function countDelivered($empid, $status) {
        global $database;
        $sql = "SELECT COUNT(item_id) FROM " . self::$table_name . " WHERE apr_id IN( SELECT apr_id FROM approvals WHERE empid = '$empid' AND status = '$status')  ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    /**     * ** Display Status Of An Item ***** */
    public static function isDelivered($item_id) {
        $ItemDetail = ItemDetails::find_by_item_id($item_id);
        $OrderedQuantity = $ItemDetail->quantity;
        $grnDetail = GRN::find_received_quantity($item_id);
        $podetails = PoDetails::find_by_item_id($item_id);
        $PRdetail = PrDetails::find_by_item_id($item_id);
        $Approved = Approval::isApproved($item_id, 'Approved');

        if (!empty($grnDetail) && !empty($podetails)) {

            $ReceivedQuantity = $grnDetail;

            if ($OrderedQuantity == $ReceivedQuantity) {
                return 'Delivered';
            } else {
                return 'Partially Received';
            }
        } elseif (!empty($podetails)) {
            return 'Process For GRN';
        } elseif (!empty($PRdetail)) {
            return 'Process For PO';
        } else {
            if (!empty($Approved)) {
                return 'Process For PR';
            } else {
                return 'Pending';
            }
        }
    }

    public static function isReadyForDispatch($item_id) {
        $ItemDetail = ItemDetails::find_by_item_id($item_id);
        $OrderedQuantity = $ItemDetail->quantity;
        $ReceivedQuantity = GRN::find_received_quantity($item_id);
        if ($OrderedQuantity == $ReceivedQuantity) {
            return TRUE;
        } else {
            return FALSE;
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
        //echo $sql;
        if ($database->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($grn_id) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE grn_id ='{$grn_id}'";
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
        return 'GRN' . $num;
    }

    /*     * * Send Mail to PMT ** */

    public static function sendmail($GRNIds, $PMTId) {
        $PMTName = Employee::find_by_empid($PMTId);
        $message = '<table style="border:1px solid #ddd;margin-top:1em"><tr style="border:1px solid #ddd;"><th style="border:1px solid #ddd;">Key No</th><th style="border:1px solid #ddd;">Description</th><th style="border:1px solid #ddd;">Ordered Quantity</th><th style="border:1px solid #ddd;">Received Till Date</th></tr>';
        if (!empty($GRNIds)) {
            foreach ($GRNIds as $GRNid) {
                $GRN = GRN::find_by_item_id2($GRNid);
                $receivedTillDate = GRN::find_received_quantity($GRN->item_id);
                $ItemDetail = ItemDetails::find_by_item_id($GRN->item_id);
                $KeyNo = PrDetails::find_by_item_id($GRN->item_id);
                $message .='<tr style="border:1px solid #ddd;">
								<td style="border:1px solid #ddd;">' . $KeyNo->key_no . '</td>
								<td style="border:1px solid #ddd;">' . $ItemDetail->description . '</td>
								<td style="border:1px solid #ddd;">' . $ItemDetail->quantity . '</td>
								<td style="border:1px solid #ddd;">' . $receivedTillDate . '</td>
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
            $mail->AddAddress($PMTName->emailid, "AMS Admin");

            $mail->Subject = "GRN Notification";

            $mail->IsHTML(true);

            $mail->Body = <<<EMAILBODY

	<strong>Dear {$PMTName->name}</strong><br/>
			Material requested by you has been delivered at CWH .<br/>
			The details are as following.
			{$message}

	<br/><br/>Thank You
	<hr/>
	Marketing Support Team<br/>
	Cipla Ltd.

EMAILBODY;

            $mail->Send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }

    /*     * *************** Send SMS to PMT ****************** */

    public static function sendsms($GRNIds, $PMTId) {
        $KeyCollection = array();
        $PMTName = Employee::find_by_empid($PMTId);
        if (!empty($GRNIds)) {
            foreach ($GRNIds as $GRNid) {
                $GRN = GRN::find_by_item_id2($GRNid);
                $KeyNo = PrDetails::find_by_item_id($GRN->item_id);
                array_push($KeyCollection, $KeyNo->key_no);
            }
        }

        $finalKeyNo = implode(",", $KeyCollection);
        $mobileNo = $PMTName->mobile;
        $message = 'Dear ' . $PMTName->name . ', Material No. ' . $finalKeyNo . ' requested by you has been delivered at CWH.for more details kindly check your mail.';
        $authKey = "78106A1u8VLmCC054cb666b";
        //$mobileNumber = $mobile;
        $senderId = "CIPAMS";
        $finalmessage = rawurlencode($message);
        //Define route 
        $route = "4";
        //Prepare you post parameters
        $postData = array(
            'authkey' => $authKey,
            'mobiles' => $mobileNo,
            'message' => $finalmessage,
            'sender' => $senderId,
            'route' => $route
        );

        //API URL
        $url = "https://control.msg91.com/sendhttp.php";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
                //,CURLOPT_FOLLOWLOCATION => true
        ));

        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        //get response
        $output = curl_exec($ch);

        //Print error if any
        if (curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);
        //echo $output;
    }

}

?>