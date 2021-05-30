<?php

$utilisateur= 'user_bts';

$destinataire= 'mysql:host=localhost;dbname=sce_private;port=3306';

$motPasse= 'eclipse';
/*on se connecte avec nos identifiants*/
$connexion=new PDO($destinataire,$utilisateur,$motPasse);

//recupérer le nom du local choisi
$local = $_GET["id"];
//Lire toute la BDD
$sql_requette="SELECT SUM(watts) as watts_avg  FROM table_stockage  GROUP BY heure desc LIMIT 1"; 


/*on prépare la requête*/
$reponse =  $connexion->prepare($sql_requette);
$reponse->execute(array($sql_requette));


while ($row = $reponse->fetch(PDO::FETCH_ASSOC))
    {
    	echo json_decode($row["watts_avg"]);
    	//echo json_decode($row["identification_cc"]);
	};
$reponse->closeCursor(); /*-termine le traitement de la requête*/
	

?>