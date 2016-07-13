<?php
require_once(dirname(__FILE__) . "/MMheader.php");
//$empName = GPM::find_by_empid($empid);
$GPMs = GPM::find_by_mm_id($empid);
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            List Of GPM
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="MMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="active">
                <i class="fa fa-desktop"></i> List Of GPM
            </li>
        </ol>
    </div>
</div>
<div class="row" style="margin-bottom:2em;">
    <div class="col-lg-1 pull-center">
        <a href="MMaddGPM.php"><button type="button" class="btn btn-default" >Add New</button></a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Emp Id</th>
                        <th>GPM Name</th>
                        <th>Division</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($GPMs as $GPM) {
                        ?>
                        <tr>
                            <td><?php echo $GPM->gpm_empid; ?></td>
                            <td><?php echo $GPM->name; ?></td>
                            <td>
                                <?php
                                $divisionName = Division::find_by_div_id($GPM->division);
                                echo $divisionName->div_name;
                                ?>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once(dirname(__FILE__) . "/MMfooter.php"); ?>