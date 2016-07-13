<?php session_start(); if(!isset($_SESSION['CWH'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $Approvals = Approval::find_by_status2('Approved');

 require_once(dirname(__FILE__)."/layouts/CWHlayouts/header.php");?>
     <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                List Of Approvals
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <a href="PREindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
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
                            <th>Brand</th>
                            <th>Division</th>

                            <th>Requision By</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($Approvals)){
                        foreach ($Approvals as $Approval) { 
                        ?>
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
                                      $division =Brand::find_division1($value);
                                   }
                                }
                                $finalBrandList = join(", ", array_unique($finalBrands)); 
                            echo $finalBrandList;  
                            ?></td>

                            <td><?php $Empid = Approval::find_by_apr_id2($Approval->apr_id);
                                $empName = Employee::find_by_empid($Empid->empid);
                                $division  = GPM::find_division1($empName->gpm_empid);
                                $divisionName = Division::find_by_div_id($division);  
                                echo $divisionName->div_name; ?>
                            </td>

                            <td><?php $PMT=Employee::find_by_empid($Approval->empid); echo $PMT->name; ?></td>
                            <td><?php echo $Approval->date; ?></td>


                            <?php $POdetails=PoDetails::proceed($Approval->apr_id); 
                            if ($Approval->receive == "received") { ?>
                            <td>Received</td>
                                
                            <?php }elseif ($POdetails == true || $Approval->process_for_po == "processed" ){ ?>
                            <td>Process For Po</td>

                            <?php } else{ ?>
                            <td>Approved</td>

                            <?php } ?>
                        </tr>

                    <?php }  }?>
                    </tbody>
                    </table>

                </div>
                    
            </div>
        </div>
<?php require_once(dirname(__FILE__)."/layouts/CWHlayouts/footer.php");?>