<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");   

    if(isset($_POST['save']) && isset($_GET['apr_id'])){

        if(isset($_POST['alloc_id'])){
        $newAllocation = new AllocationDetails();
        $newAllocation->item_id = $_POST['item_id'];
        $newAllocation->qty_per_person = $_POST['qty_per_person'];

        $length = count($_POST['depot_id']);
            for ($i=0; $i < $length; $i++) { 
                $newAllocation->alloc_id =$_POST['alloc_id'][$i];
                $newAllocation->depot_id =$_POST['depot_id'][$i];
                $newAllocation->region_id =$_POST['region_id'][$i];
                $newAllocation->no_of_persons =$_POST['no_of_persons'][$i];
                $newAllocation->total_quantity =$_POST['total'][$i];

                $newAllocation->update($newAllocation->alloc_id);
            }
        $_SESSION['Error'] ='Updated Successfully';
        redirect_to("Allocation.php?apr_id=".$_GET['apr_id']);

        }else{

        $newAllocation = new AllocationDetails();
        $newAllocation->item_id = $_POST['item_id'];
        $newAllocation->qty_per_person = $_POST['qty_per_person'];

        $length = count($_POST['depot_id']);
            for ($i=0; $i < $length; $i++) { 
                $newAllocation->alloc_id =$newAllocation->autoGenerate_id();
                $newAllocation->depot_id =$_POST['depot_id'][$i];
                $newAllocation->region_id =$_POST['region_id'][$i];
                $newAllocation->no_of_persons =$_POST['no_of_persons'][$i];
                $newAllocation->total_quantity =$_POST['total'][$i];
                $newAllocation->create(); 
            }
        $_SESSION['Error'] ='Success';
        redirect_to("Allocation.php?apr_id=".$_GET['apr_id']);
        }

    }
?>