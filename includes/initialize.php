<?php

date_default_timezone_set("Asia/Kolkata");
/** Register All Classes Here * */
try {
    require_once('config.php');
    require_once('functions.php');
    require_once('database.php');

    require_once('employee.php');
    require_once('class.GPM.php');
    require_once('class.GRN.php');
    require_once('class.mm.php');

    require_once('class.approval.php');
    require_once('class.brand.php');
    require_once('class.depot.php');
    require_once('class.region.php');
    require_once('class.itemDetails.php');
    require_once('class.manpower.php');
    require_once('class.po.php');
    require_once('class.pr_details.php');
    require_once('class.pr_po.php');

    require_once('class.brandBudget.php');
    require_once('class.tempBudget.php');
    require_once('class.admin.php');

    /** ******************* */
    require_once('class.sm.php');
    require_once('class.tm.php');
    require_once('class.dispatch.php');
    /** ******************* */
    require_once('class.employee_brand.php');
    require_once('class.allocation.php');
    require_once('class.division.php');
    require_once('phpMailer/class.phpmailer.php');
    require_once('phpMailer/class.smtp.php');
    require_once('Encryption.php');

    /** ******************* */
    require_once('excel/reader.php');
    /** ******************* */
} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
}
?>