<?php
require_once 'config.php';

 if(isset($_POST["Import"])){
		
		$filename=$_FILES["file"]["tmp_name"];		


		 if($_FILES["file"]["size"] > 0)
		 {
		  	$file = fopen($filename, "r");
	        while (($getData = fgetcsv($file, 10000, ";")) !== FALSE)
	         {


	           $sql = "INSERT into ELEVE (NOM_APPRENANT,PRENOM_APPRENANT,EMAIL,NOM_GROUPE,NOM_ENTREPRISE) 
                   values ('".$getData[0]."','".$getData[1]."','".$getData[2]."','".$getData[3]."','".$getData[4]."')";
                   $result = mysqli_query($con, $sql);
				if(!isset($result))
				{
					echo "<script type=\"text/javascript\">
							alert(\"Invalid File:Please Upload CSV File.\");
							window.location = \"index.php\"
						  </script>";		
				}
				else {
					  echo "<script type=\"text/javascript\">
						alert(\"CSV File has been successfully Imported.\");
						window.location = \"index.php\"
					</script>";
				}
	         }
			
	         fclose($file);	
		 }
	}	 

function get_all_records(){
    $con = getdb();
    $Sql = "SELECT * FROM ELEVE";
    $result = mysqli_query($con, $Sql);  


    if (mysqli_num_rows($result) > 0) {
     echo "<div class='table-responsive'>
            <table id='myTable' class='table table-striped table-bordered'>
             <thead><tr><th>ID</th>
                          <th>Nom</th>
                          <th>Prenom</th>
                          <th>Email</th>
                          <th>Groupe</th>
                          <th>Entreprise</th>
                          <th>RFID UID</th>
                          <th>Commandes</th>
                        </tr></thead><tbody>";


     while($row = mysqli_fetch_assoc($result)) {

         echo "<tr><td>" . $row['ID']."</td>
                   <td>" . $row['NOM_APPRENANT']."</td>
                   <td>" . $row['PRENOM_APPRENANT']."</td>
                   <td>" . $row['EMAIL']."</td>
                   <td>" . $row['NOM_GROUPE']."</td>
                   <td>" . $row['NOM_ENTREPRISE']."</td>
                   <td>" . $row['UID']."</td>
                   <td>"
                   ?>
                   <div class="popup">
                    <form enctype="multipart/form-data" onclick="myFunction()" action="ajout.php?ID=<?php echo $row['ID']?>" method="POST">
                      <p><input type="submit" value="Add Card" /></p>
                      <span class="popuptext" id="myPopup">Passer la carte RFID</span>
                    </form>
                  </div>
                   <?php
         echo "</td></tr>";        
     }
    
     echo "</tbody></table></div>";
     
	} else {
	     echo "you have no records";
	}
}
if(isset($_POST["Export"])){
		 
      header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename=data.csv');  
      $output = fopen("php://output", "w");  
      fputcsv($output, array('NOM_APPRENANT', 'PRENOM_APPRENANT', 'EMAIL', 'NOM_GROUPE', 'NOM_ENTREPRISE', 'UID'));  
      $query = "SELECT * from ELEVE ORDER BY ID DESC";  
      $result = mysqli_query($con, $query);  
      while($row = mysqli_fetch_assoc($result))  
      {  
           fputcsv($output, $row);  
      }  
      fclose($output);  
}  

?>