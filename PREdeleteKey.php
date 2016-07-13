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
            $status = '';
            $Po_No = '';
            $Po_date = '';

            for ($i = 1; $i < $data->sheets[0]['numRows']; $i++) {
                $date = date('Y-m-d H:i:s');

                $key_no = trim($data->sheets[0]['cells'][$i + 1][1]);
                $status = trim($data->sheets[0]['cells'][$i + 1][2]);
                ///$line_item = trim($data->sheets[0]['cells'][$i + 1][3]);

                //$dob = trim($data->sheets[0]['cells'][$i + 1][4]);
                //$Pr_date =  date('Y-m-d', strtotime(str_replace('/', '-', $dob)));
                
                //echo $Pr_date;
                
                $found_key_no = PrDetails::find_by_key_no($key_no);
                //var_dump($found_key_no);
                if (!empty($found_key_no) && strtoupper($status)== 'X') {
                    $updateKeyNo = new PrDetails();
                    $updateKeyNo->key_no = $found_key_no->key_no;
                    $updateKeyNo->delete();
                    
                } 
            }
        } else {
            flashMessage("Failed.", "Error");
        }
    } else {
        flashMessage("Invalid File type.", "Error");
    }
    redirect_to("PREdeleteKey.php");
}
require_once(dirname(__FILE__) . "/layouts/PRElayouts/header.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Delete Key No
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-hand-o-down"></i> Delete Key No

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
