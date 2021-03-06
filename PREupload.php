<?php
session_start();
if (!isset($_SESSION['PRE'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

if (isset($_POST['submit']) && $_POST['submit'] != "") {
    //print_r($_FILES);
    //$con = $this->connect_db();
    //$sheet_no = (int) $fields['sheet_number'] - 1;
    //$is_special = (int) $fields['is_special'];

    $path = "files/";
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];

    list($txt, $ext) = explode(".", $name);


    $actual_image_name = $name;
    $src = "files/" . $actual_image_name;
    $tmp = $_FILES['file']['tmp_name'];

    error_reporting(E_ALL ^ E_NOTICE);

    if (move_uploaded_file($tmp, $path . $actual_image_name)) {    //if(file_put_contents($src, $temp))
        //echo 'hi';
        error_reporting(E_ALL ^ E_NOTICE);
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');
        //$data->read('Senator.xls');
        $data->read($src);

        $data->sheets[0]['numRows'];
        $Pr_No = '';
        $line_item = '';
        $Po_No = '';
        $Po_date = '';

        for ($i = 1; $i < $data->sheets[0]['numRows']; $i++) {
            $date = date('Y-m-d H:i:s');


            $pr_No = $data->sheets[0]['cells'][$i + 1][1];
            $line_item = $data->sheets[0]['cells'][$i + 1][2];
            $Po_No = $data->sheets[0]['cells'][$i + 1][3];
            $Po_date = $data->sheets[0]['cells'][$i + 1][4];
            //echo $pr_No.":".$line_item.":".$Po_No.":".$Po_date."</br>";

            $found_pr_entry = PoDetails::match_pr_no($pr_No, $line_item);
            //var_dump($found_pr_entry);
            if (!empty($found_pr_entry)) {
                $pr_po = new PrPo();
                $pr_po->po_id = $found_pr_entry->po_id;
                $pr_po->po_date = $Po_date;
                $pr_po->po_no = $Po_No;

                $found_existing_entry = PrPo::find_by_po_id($pr_po->po_id);
                if (!empty($found_existing_entry)) {
                    $pr_po->id = $found_existing_entry->id;
                    $pr_po->update();
                } else {
                    $pr_po->create();
                }
            } else {
                
            }
        }
    } else {
        echo "failed";
    }
}

require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Upload PO Details
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> Upload PO Details
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form method="post" enctype="multipart/form-data" action="#" class="form-horizontal">
            <label>Import File</label>
            <div class="form-group">
                <div class="col-lg-6">
                    <input type="file" name="file" required="required" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-6">
                    <input type="submit" name="submit" value="Upload" class="btn btn-warning">
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once(dirname(__FILE__) . "/layouts/PRElayouts/footer.php"); ?>