<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<style>
		table {
		  font-family: arial, sans-serif;
		  border-collapse: collapse;
		  width: 100%;
		}

		td, th {
		  border: 1px solid #dddddd;
		  text-align: left;
		  padding: 8px;
		}

		tr:nth-child(even) {
		  background-color: #dddddd;
		}

		/* Popup container - can be anything you want */
		.popup {
		  position: relative;
		  display: inline-block;
		  cursor: pointer;
		  -webkit-user-select: none;
		  -moz-user-select: none;
		  -ms-user-select: none;
		  user-select: none;
		}

		/* The actual popup */
		.popup .popuptext {
		  visibility: hidden;
		  width: 160px;
		  background-color: #555;
		  color: #fff;
		  text-align: center;
		  border-radius: 6px;
		  padding: 8px 0;
		  position: absolute;
		  z-index: 1;
		  bottom: 125%;
		  left: 50%;
		  margin-left: -80px;
		}

		/* Popup arrow */
		.popup .popuptext::after {
		  content: "";
		  position: absolute;
		  top: 100%;
		  left: 50%;
		  margin-left: -5px;
		  border-width: 5px;
		  border-style: solid;
		  border-color: #555 transparent transparent transparent;
		}

		/* Toggle this class - hide and show the popup */
		.popup .show {
		  visibility: visible;
		  -webkit-animation: fadeIn 1s;
		  animation: fadeIn 1s;
		}

		/* Add animation (fade in the popup) */
		@-webkit-keyframes fadeIn {
		  from {opacity: 0;} 
		  to {opacity: 1;}
		}

		@keyframes fadeIn {
		  from {opacity: 0;}
		  to {opacity:1 ;}
		}
		</style>
	</head>
	<body>
		<?php
				$mysqli = new mysqli('localhost', 'root', 'Melec', 'RFID');
				$mysqli->set_charset("utf8");
				$requete = 'SELECT * FROM utilisateur';
				$resultat = $mysqli->query($requete);
		 ?>
		<table class="footable">
		<caption>mon titre</caption>
		 
		            <thead>
		            <tr class="titre_horizon_classique">
		                <th colspan="11"><h3>Liste</h3></th>
		            </tr>
		            <tr class="titre_horizon_classique">
		            	<th>ID</th>
		                <th>Nom</th>
		                <th>Prenom</th>
		                <th>Email</th>
		                <th>Date de naissance</th>
		                <th>Entreprise</th>
		                <th>UID</th>
		                <th>Commande</th>

		            </tr>
		            </thead>
		<tbody>
		<?php
		while ($ligne = $resultat->fetch_assoc()) {
		?>
		 
		<tr>
		<td><?php echo $ligne['id']?></td>
		<td><?php echo $ligne['nom']?></td>
		<td><?php echo $ligne['prenom'];?></td>
		<td><?php echo $ligne['email']?></td>
		<td><?php echo $ligne['date_naissance']?></td>
		<td><?php echo $ligne['entreprise']?></td>
		<td><?php echo $ligne['uid']?></td>
		<td><div class="popup">
				<form enctype="multipart/form-data" onclick="myFunction()" action="ajout.php?ID=<?php echo $ligne['id']?>" method="POST">
			   	<p><input type="submit" value="Add Card" /></p>
			   	<span class="popuptext" id="myPopup">Passer la carte RFID</span>
				</form>
			</div>
		</td>

		</tr><?php
		}
		$mysqli->close();
				?>
		 
		</tbody></table>

			<script>
			// When the user clicks on div, open the popup
			function myFunction() {
			  var popup = document.getElementById("myPopup");
			  popup.classList.toggle("show");
			}
			</script>


	</body> 
</html>
