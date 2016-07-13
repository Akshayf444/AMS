<?php ob_start();

echo phpinfo();
include_once './layouts/gpmlayouts/header.php';  
include_once'./includes/initialize.php';

if (isset($_GET['empid'])) {
    $empid=$_GET['empid'];
}else{
    header("Location:GPMbrandList.php");
}

    $empName = GPM::find_by_empid($empid);
    $errors = array();

    if (isset($_POST['submit'])) {

        for ($i=0; $i < 5; $i++)  {
            $brand = new Brand();
            $brand->brand_id = $brand->autoGenerate_id();
            $brand->div_id = $empName->division;
                $brand->status = 1;
            if (!empty(trim($_POST['brand'][$i]))) {
                $brand->brand_name = $_POST['brand'][$i]; 
            }else{
                array_push($errors, "Brand Name Cannot be emp0ty");
            }
            
            if (empty($errors)) {
                $brand->create();
            }
        }
        redirect_to("GPMaddNewBrand.php");
    }
    
    include_once './layouts/gpmlayouts/header.php';    
//require_once(dirname(__FILE__) . "/layouts/gpmlayouts/header.php");
  ?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Add Brand
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li class="active">
                    <i class="fa fa-desktop"></i> Add Brand
                </li>
           </ol>
        </div>
    </div>
  <div class="row">
        <div class="col-lg-12">
            <form action="GPMaddNewBrand.php" method="post" >
                
                    <div class="col-lg-4" style="margin-bottom: 0.5em">
                <input type="text" class="form-control" name="brand[]" value="" >
                    </div>
                <div class="col-lg-4" style="margin-bottom: 0.5em">
                <input type="text" class="form-control" name="brand[]" value="" >
                    </div>
                <div class="col-lg-4" style="margin-bottom: 0.5em">
                <input type="text" class="form-control" name="brand[]" value="" >
                    </div>
                <div class="col-lg-4" style="margin-bottom: 0.5em">
                <input type="text" class="form-control" name="brand[]" value="" >
                    </div>
                <div class="col-lg-4" style="margin-bottom: 0.5em">
                <input type="text" class="form-control" name="brand[]" value="" >
                    </div>
                
                
                <div class="col-lg-12" style="margin-top:1em">
                    <input class="btn btn-primary" type="submit" name="submit" value="Save" />
                </div>
            </form>
        </div>
    </div>

<?php include_once'./layouts/gpmlayouts/footer.php'; ?>