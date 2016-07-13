<?php

function strip_zeros_from_date($marked_string = "") {
    // first remove the marked zeros
    $no_zeros = str_replace('*0', '', $marked_string);
    // then remove any remaining marks
    $cleaned_string = str_replace('*', '', $no_zeros);
    return $cleaned_string;
}

function redirect_to($location = NULL) {
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}

function output_message($message = "") {
    if (!empty($message)) {
        return "<p class=\"message\">{$message}</p>";
    } else {
        return "";
    }
}

/* function __autoload($class_name) {
  $class_name = strtolower($class_name);
  $path ="/includes/{$class_name}.php";
  if(file_exists($path)) {
  require_once($path);
  } else {
  die("The file {$class_name}.php could not be found.");
  }
  } */

//for log file
function log_action($action, $message = "") {
    $logfile = SITE_ROOT . DS . 'logs' . DS . 'log.txt';
    $new = file_exists($logfile) ? false : true;
    if ($handle = fopen($logfile, 'a')) { // append
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action}: {$message}\n";
        fwrite($handle, $content);
        fclose($handle);
        if ($new) {
            chmod($logfile, 0755);
        }
    } else {
        echo "Could not open log file for writing.";
    }
}

//converting date to text
function datetime_to_text($datetime = "") {
    $unixdatetime = strtotime($datetime);
    return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function logged_in() {
    return isset($_SESSION['admin_id']);
}

function confirm_logged_in() {
    if (!logged_in()) {
        redirect_to("adminlogin.php");
    } else {
        redirect_to("registerAdmin.php");
    }
}

function confirm_emp_logged_in() {
    if (!logged_in()) {
        redirect_to("login.php");
    }
}

function find_admin_by_id($admin_id) {
    global $connection;

    $safe_admin_id = mysqli_real_escape_string($connection, $admin_id);

    $query = "SELECT * ";
    $query .= "FROM admins ";
    $query .= "WHERE id = {$safe_admin_id} ";
    $query .= "LIMIT 1";
    $admin_set = mysqli_query($connection, $query);
    confirm_query($admin_set);
    if ($admin = mysqli_fetch_assoc($admin_set)) {
        return $admin;
    } else {
        return null;
    }
}

function encryptData($value) {
    $key = "top secret key";
    $text = $value;
    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $crypttext = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $text, MCRYPT_MODE_ECB, $iv);
    return $crypttext;
}

function decryptData($value) {
    $key = "top secret key";
    $crypttext = $value;
    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $decrypttext = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
    return trim($decrypttext);
}

/**
 * Checks date if matches given format and validity of the date. 
 * Examples: 
 * <code> 
 * is_date('22.22.2222', 'mm.dd.yyyy'); // returns false 
 * is_date('11/30/2008', 'mm/dd/yyyy'); // returns true 
 * is_date('30-01-2008', 'dd-mm-yyyy'); // returns true 
 * is_date('2008 01 30', 'yyyy mm dd'); // returns true 
 * </code> 
 * @param string $value the variable being evaluated. 
 * @param string $format Format of the date. Any combination of <i>mm<i>, <i>dd<i>, <i>yyyy<i> 
 * with single character separator between. 
 */
function is_valid_date($value, $format = 'dd.mm.yyyy') {
    if (strlen($value) >= 6 && strlen($format) == 10) {

        // find separator. Remove all other characters from $format 
        $separator_only = str_replace(array('m', 'd', 'y'), '', $format);
        $separator = $separator_only[0]; // separator is first character 

        if ($separator && strlen($separator_only) == 2) {
            // make regex 
            $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
            $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
            $regexp = str_replace($separator, "\\" . $separator, $regexp);
            if ($regexp != $value && preg_match('/' . $regexp . '\z/', $value)) {

                // check date 
                $arr = explode($separator, $value);
                $day = $arr[0];
                $month = $arr[1];
                $year = $arr[2];
                if (@checkdate($month, $day, $year))
                    return true;
            }
        }
    }
    return false;
}

function flashMessage($message, $type) {
    session_start();
    if (ucfirst($type) == 'Error') {

        $_SESSION['message'] = '<div class="alert alert-danger"> '
                . '<a href="#" class="close" data-dismiss="alert">&times;</a>'
                . '<strong>' . $message . '</strong></div>';
    }
    if (ucfirst($type) == 'Success') {
        $_SESSION['message'] = '<div class="alert alert-success"> '
                . '<a href="#" class="close" data-dismiss="alert">&times;</a>'
                . '<strong>Success!! </strong>' . $message . '</div>';
    }
}
