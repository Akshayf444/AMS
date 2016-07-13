<?php session_start(); if(!isset($_SESSION['gpm'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
 $empid=$_SESSION['gpm'];
 $empName = GPM::find_by_empid($empid);
 $brands = Brand::find_by_division($empName->division);
 $Employees = Employee::find_by_gpm($empid);
 $n = 1;
$errors=array();
//Process Form data
 if(isset($_POST['submit'])){


    foreach ($Employees as $Employee) { 
            $newEmployee_Brand = new Employee_Brand();

            if(!empty($_POST[$Employee->empid])){
            $brandList=implode(",",$_POST[$Employee->empid]);
            $newEmployee_Brand->empid=$Employee->empid;
            $newEmployee_Brand->brand_name=$brandList;
            }else{
                array_push($errors, "Please Select Atleast one Brand For Each Employee");
            }

            if (empty($errors)) {
                $foundBrand=Employee_brand::exist($newEmployee_Brand->empid);
                if (empty($foundBrand)) {
                    $newEmployee_Brand->create(); 
                }else{
                    $newEmployee_Brand->update($newEmployee_Brand->empid);
                }

            }else{

            }


 	}

    if(empty($errors)){
         $_SESSION['Error'] ="Success";
         redirect_to("GPMaddBrand.php");
    }
 }

 require_once(dirname(__FILE__)."/layouts/gpmlayouts/header.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Assign Brands
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <a href="GPMindex.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                </li>
                <li class="active">
                    <i class="fa fa-list"></i>Assign Brands
                </li>
           </ol>
    </div>
    </div>
    <div id="errors" class="row">
    	<div class="col-lg-12">
			<?php if( isset($_SESSION['Error'])){
			        echo $_SESSION['Error'];
			        unset($_SESSION['Error']);
			}?>
            <ul>
            <?php foreach (array_unique($errors) as $value) {
                echo $value;
            }?>
            </ul>
		</div>
	</div>
    <div class="row">
        <div class="col-lg-5">
        	<form action="GPMaddBrand.php" method="post">

        		<div class="table-responsive" style="margin-top:2em">
	            	<table class="table table-bordered table-hover table-striped" >
	            	<thead>
                        <tr>
                            <th>PMT </th>
                            <th>Brands</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php if(!empty($Employees)){
                    	//For Uniquely identifying brands
                    	foreach ($Employees as $Employee) {?>
                    	<tr>
                    	<td><?php echo $Employee->name; ?>
                    	<input type="hidden" name="empid[]" value="<?php echo $Employee->empid; ?>">
                    	</td>	
                    	<td><?php if(!empty($brands)){ 

                    		foreach ($brands as $brand) {     ?>
                    		<p><input type="checkbox" name="<?php echo $Employee->empid."[]" ;?>" value ="<?php echo $brand->brand_name; ?>" class="<?php echo $brand->brand_name; ?>"> <?php echo $brand->brand_name; ?></p>

                    		<?php }//end of loop..

                    		 }//End of if?>
                    	</td>
                    	</tr>
                    	<?php

                    		}//End of loop..
                    	 }//End of if..?>
	                    <tr>
	                    	<?php if(!empty($Employees)){ ?>
	                        <td colspan="2">
	                        	<button type="submit" class="btn btn-primary" name="submit" >Save</button>
	                        </td>
	                        <?php } ?>
	                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    $(":checkbox").click(function(){
        var myClass = $(this).attr("class");
        if($(this).prop("checked")){  
            $('.'+ myClass).not(this).remove();  
        }else{
            $('.'+ myClass).not(this).remove();  
        }
    });
});
</script>
<?php require_once(dirname(__FILE__)."/layouts/gpmlayouts/footer.php");?>
