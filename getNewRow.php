<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
    $empid=$_SESSION['employee'];
    $empName =Employee::find_by_empid($empid);
       // $brandlist=Employee_Brand::find_by_empid($empid);
    $division =GPM::find_division1($empName->gpm_empid);
    $brands =Brand::find_by_division($division);
?>
<tr class="targetfields">
    <td>
                            <div class="form-group">
                                <select class="form-control multiselect"  multiple="multiple" >
                                <?php foreach ($brands as $brand) {?>
                                    <option  value="<?php echo $brand->brand_id; ?>"><?php echo $brand->brand_name; ?></option>
                                <?php }?>
                                </select>
                            </div>
                            </td>
                            <td>
                                <div class="form-group">
                                <select class="form-control" name="item_category[]">
                                    <option>Print</option>                            
                                    <option>Gift</option>
                                    <option>E-Input</option>
                                    <option>Publisher</option>
                                    <option>Promo Services</option>
                                    <option>Miscellaneous</option>
                                </select>
                                </div>
                            </td>
                            <td><div class="form-group"><input class="form-control itemDescription"  name="description[]" type="text"  required></div></td>
                            <td><input class="form-control quantity common" name="quantity[]" type="text" ></td>
                            <td><input class="form-control rate common" name="value[]"  type="text" ></td>
                            <td><input class="form-control subtotal" name="amount[]" type="text"  readonly></td>
                            <td><button type="button" class="btn btn-xs btn-info delete" ><span class="glyphicon glyphicon-trash"></span></button></td>

</tr>