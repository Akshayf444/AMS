<?php //require_once("./includes/initialize.php"); 

require_once('./includes/Encryption.php');

$Encrypt = new Encryption();
$data = "1";
echo $data.'</br>';

$Encoded = $Encrypt->encode($data);

echo $Encoded.'</br>';

echo $Encrypt->decode($Encoded);

?>