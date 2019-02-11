<?php 


// RECUPERATION CODE_CLASSE
if (isset($_GET['ID'])) {
  $id = $_GET['ID'];
}


exec("/usr/bin/python /home/pi/PointRFID/Script/ajout.py '$id'");


// sleep php process
sleep(3);
// redirect
header("location: index.php");




?>
