<?php 


// RECUPERATION CODE_CLASSE
if (isset($_GET['ID'])) {
  $id = $_GET['ID'];
}


exec("sudo /usr/bin/python /home/pi/RFID/ajout.py '$id'");


// sleep php process
sleep(5);
// redirect
header("location: afficher.php");




?>
