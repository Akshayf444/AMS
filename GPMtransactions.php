<?php session_start(); if(!isset($_SESSION['gpm'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['gpm'];
    $empName = GPM::find_by_empid($empid);
    $Employees = Employee::find_by_gpm($empid);
    $finalItems =array();
    foreach ($Employees as $Employee) {

        $Items =ItemDetails::find_by_empid($Employee->empid);
        foreach ($Items as $value) {
            array_push($finalItems, $value);
        }
    }

 require_once(dirname(__FILE__)."/layouts/gpmlayouts/header.php");?>
     <div class="row">
        <div class="col-lg-12">
            
            <h1 class="page-header">Item Status</h1>
            
                <ol class="breadcrumb">
                    <li class="active">
                        <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-hand-o-down"></i> Item Status
                    </li>
               </ol>
	</div>
</div>
                        <div class="col-lg-6">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Key No</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($finalItems as $Item) { 
           
                                            ?>
                                            <tr>
                                                <td><?php $PRdetail=PrDetails::find_by_item_id($Item->item_id); 
                                                if(!empty($PRdetail)){
                                                    echo $PRdetail->key_no;
                                                    }else{ 
                                                        echo '-'; 
                                                    } ?>
                                                </td>

                                                <td><?php echo $Item->description; ?></td>
                                                <td><?php $status =GRN::isDelivered($Item->item_id);
                                                        if(!empty($status)){
                                                            echo $status;
                                                        }

                                                 ?>
                                                </td>
                                                
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                    </div>
 <?php require_once(dirname(__FILE__)."/layouts/gpmlayouts/footer.php");?>