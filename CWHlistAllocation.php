<?php session_start(); if(!isset($_SESSION['CWH'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");

$Approvals = Approval::find_by_status2("Approved");
  require_once(dirname(__FILE__)."/layouts/CWHlayouts/header.php");?>
    <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            List Of Approvals
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-list"></i> List Of Approvals
                            </li>
                        </ol>
    </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Approval Id</th>
                            <th>Title Of Approval</th>
                            <th>Brand/Division</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($Approvals as $Approval) {?>
                        <tr>
                            <td><?php echo $Approval->apr_id; ?></td>
                            <td><?php echo $Approval->title; ?></td>
                            <td><?php $brands = ItemDetails::find_by_apr_id($Approval->apr_id);
                                $allBrands=array();
                                $finalBrands=array();
                                foreach ($brands as $brand) {
                                   $allBrands = preg_split('/,/', $brand->brand_id);
                                   foreach ($allBrands as $value) {
                                      array_push($finalBrands, $value);
                                   }
                                }
                                $finalBrandList = join(", ", array_unique($finalBrands)); 
                            echo $finalBrandList;  

                            $Allocated = AllocationDetails::allocated($Approval->apr_id); 

                            ?></td>
                            <td><?php echo $Approval->date; ?></td>

                            <?php ?>

                            <td><a href="CWHallocation.php?apr_id=<?php echo $Approval->apr_id; ?>">
                                <button type="button" class="btn btn-xs <?php if($Allocated ==='Allocated'){ 
                                    echo 'btn btn-danger'; 
                                }else{ 
                                    echo 'btn-info'; 
                                } ?>"><?php if($Allocated ==='Allocated'){ 
                                    echo 'Allocated'; 
                                }else{ echo 'Allocate'; 
                                } ?></button>
                            </a>
                            </td>
                            
                       </tr>
                       <?php }//End Of For Loop?>
                    </tbody>
                    </table>
                </div>
                    
            </div>
        </div>
    </div>


<?php require_once(dirname(__FILE__)."/layouts/CWHlayouts/footer.php");?>
