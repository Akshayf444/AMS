<?php
session_start();
if (!isset($_SESSION['PRE'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

if (isset($_POST['submit']) && $_POST['submit'] != "") {
    if ($_FILES["file"]["type"] == "application/vnd.ms-excel") {
        $path = "files/";
        $name = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];

        list($txt, $ext) = explode(".", $name);


        $actual_image_name = $name;
        $src = "files/" . $actual_image_name;
        $tmp = $_FILES['file']['tmp_name'];

        error_reporting(E_ALL ^ E_NOTICE);

        if (move_uploaded_file($tmp, $path . $actual_image_name)) {    //if(file_put_contents($src, $temp
            error_reporting(E_ALL ^ E_NOTICE);
            $data = new Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('CP1251');
            //$data->read('Senator.xls');
            $data->read($src);

            $data->sheets[0]['numRows'];
            $key_no = '';
            $line_item = '';
            $Po_No = '';
            $Po_date = '';

            for ($i = 1; $i < $data->sheets[0]['numRows']; $i++) {
                $date = date('Y-m-d H:i:s');

                $key_no = trim($data->sheets[0]['cells'][$i + 1][1]);
                $Pr_No = trim($data->sheets[0]['cells'][$i + 1][2]);
                $line_item = trim($data->sheets[0]['cells'][$i + 1][3]);

                $dob = explode("/",trim($data->sheets[0]['cells'][$i + 1][4]));
                $finalDate = $dob[2].'-'.$dob[1].'-'.($dob[0]-1);
                //echo $finalDate;
                $Pr_date = $finalDate;
                $found_key_no = PrDetails::find_by_key_no($key_no);
                
                echo $Pr_date;
                //var_dump($found_key_no);
                if (!empty($found_key_no)) {
                    $pr_details = new PoDetails();
                    $pr_details->item_id = $found_key_no->item_id;
                    $pr_details->line_no = $line_item;
                    $pr_details->pr_no = $Pr_No;
                    $pr_details->pr_date = $Pr_date;

                    $found_existing_entry = PoDetails::find_by_item_id($pr_details->item_id);
                    if (!empty($found_existing_entry)) {
                        $pr_details->po_id = $found_existing_entry->po_id;
                        $pr_details->update();
                    } else {
                        $pr_details->create();
                    }
                } else {
                    
                }
            }
        } else {
            echo "failed";
        }
    } else {
        flashMessage("Invalid File type.", "Error");
    }
    redirect_to("PREuploadPrNo.php");
}
require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Upload PR Details
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> Upload PR Details
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>
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
                    <span class =" label label-danger">* Upload only .xls files.</span> 
                </div>
            </div>
        </form>
    </div>
</div>
<?php
require_once(dirname(__FILE__) . "/layouts/PRElayouts/footer.php");
