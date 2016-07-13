<?php 
$myarray1 = array(1,2,3,4,-1,-2);
$myarray2 = array(-1,-2);
$filtered_array = array_diff($myarray1,$myarray2);
print_r($filtered_array);  
?>