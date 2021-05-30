<!-- Réception des données côté serveur (Avant intégration OVH)-->
<!-- Réalisée par Alexis REVOL -->
<?php
$destinataire= 'mysql:host=localhost;dbname=reception;port=3306';
$utilisateur= 'root';
$motPasse= '';
/*on se connecte avec nos identifiants*/
$connexion=new PDO($destinataire,$utilisateur,$motPasse);

//Reception des donnees JSON
$data = json_decode(file_get_contents('php://input'), true);
/*Recuperation des donnees passées en python*/
$donnee=$data['valeur'];
$nom=$data['nom'];
$heure=$data['heure'];
$table = $data['table'];
$unite = $data['unite'];

//Si on reçoit une consommation
if($unite=="consommation"){
	$emission = $data['emission'];
	$prix = $data['prix'];
	//Stockage
	$sql_requette="INSERT INTO $table ($unite, nom, heure_mesuree, emission, prix) 
					VALUES ('$donnee','$nom','$heure','$emission','$prix')";

}
//Si on reçoit une puissance
if($unite=="puissance"){
	//Stockage
	$sql_requette="INSERT INTO $table ($unite, nom, heure_mesuree) VALUES ('$donnee','$nom','$heure')";
}

 
/*on prépare la requête*/
$reponse =  $connexion->prepare($sql_requette);
//je l'éxecute
$reponse->execute(array($sql_requette));

$reponse->closeCursor(); /*termine le traitement de la requête*/
?>