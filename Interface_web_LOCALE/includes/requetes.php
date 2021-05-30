<!--Les requêtes SQL------------------->
<?php 
//Connexion à la BDD
$destinataire= 'mysql:host=localhost;dbname=sce_private;port=3306';
$utilisateur= 'root';
$motPasse= 'password';
/*on se connecte avec nos identifiants*/
$connexion=new PDO($destinataire,$utilisateur,$motPasse);


/*Compter combien il y a de valeurs dans chaque local*/
$sql_requette="SELECT identification_cc as local, COUNT(*) as total FROM table_stockage GROUP BY identification_cc"; 
/*on prépare puis éxecute la requête*/
$reponse =  $connexion->prepare($sql_requette);
$reponse->execute(array($sql_requette));
//Pour chaque valeurs
while ($valeur = $reponse->fetch(PDO::FETCH_ASSOC))
    {
    	?>
    	<!--Stocker le total de chaque local dans les attributs-->
    	<div id="total_<?= $valeur['local'];?>" data-total="<?= $valeur['total'];?>" ></div>
    	<?php
    	$total = $valeur['total'];
    	?><br/> <?php
    	
}
$reponse->closeCursor();



//Sélectionner les données de tous les locaux
$sql_requette="SELECT * FROM table_stockage ORDER BY id"; 
$reponse =  $connexion->prepare($sql_requette);
$reponse->execute(array($sql_requette));
$nombre = 0;
//Compter les valeurs de chaque local
$number = ["1"=>0,"2"=>0,"3"=>0,"4"=>0,"584"=>0,"2527"=>0,"7"=>0];
while ($valeur = $reponse->fetch(PDO::FETCH_ASSOC))
    {
        //utiliser que l'heure de la date
    	$dt = DateTime::createFromFormat("Y-m-d H:i:s", $valeur['heure']);
		$heure = $dt->format('H:i:s');
        //La valeur
    	$nombre = $number[$valeur['identification_cc']];
    	?>
    	<!--Ajouter dans les attributs-->
    	<div id="value_<?= $valeur['identification_cc'];?><?=$nombre;?>" data-watts="<?= $valeur['watts'];?>" data-heure="<?= $heure;?>"></div>
    	<?php
        //Numéro de la valeur de chaque local
    	$number[$valeur['identification_cc']]+=1;
}
$reponse->closeCursor();
;?>


<?php
//Somme des valeurs de tous les locaux par heure
$ma_requette="SELECT SUM(watts)as watts_avg, heure as heure_avg  FROM table_stockage  GROUP BY heure"; 
$rep =  $connexion->prepare($ma_requette);
$rep->execute(array($ma_requette));

$compteur = 0;
$matin = 0;
$midi = 0;
$soir = 0;
$total = 0;
while ($val = $rep->fetch())
    {
    	$dt = DateTime::createFromFormat("Y-m-d H:i:s", $val['heure_avg']);
		$heure = $dt->format('H:i:s'); 
    	?> 	
    	<!--Stockage dans les attributs pour graphe total-->
    	<div  class="val_heure" id = "heure_<?= $compteur;?>" data-watts="<?= $val['watts_avg'];?>" data-heure="<?= $heure;?>"></div>
    	<?php
    	/*Stockage par intervalle pour comparaison par heure*/
    	if($heure<="08:00:00"){
    		$matin+=$val['watts_avg'];
    	}
    	if(($heure<="16:00:00")&&($heure>"08:00:00")){
    		$midi+=$val['watts_avg'];
    	}
    	if(($heure>"16:00:00")&&($heure<"23:59:59")){
    		$soir+=$val['watts_avg'];
    	}
    	$total +=$val['watts_avg'];
    	$compteur+=1;	
}

$rep->closeCursor();
	/*Calculer en pourcentage (2 chiffres après la virgule)*/
	$matin = round(($matin/$total)*100,2);
    $midi = round(($midi/$total)*100,2);
    $soir = round(($soir/$total)*100,2);
;?>


<?php
//Combien il y a de valeurs dans la table
$ma_requette="SELECT COUNT(watts) as compte FROM table_stockage"; 
$rep =  $connexion->prepare($ma_requette);
$rep->execute(array($ma_requette));
while ($val = $rep->fetch())
    {
    	$total = $val['compte'];
    	?> 	
    	<!--Stockage dans les attributs-->
    	<div class="somme_total" data-total="<?= $total;?>" ></div>   	
    	<?php
}
$rep->closeCursor();



//Difference en secondes entre première et dernière valeur de chaque local
$ma_requette="SELECT UNIX_TIMESTAMP(Max(heure))  as max, UNIX_TIMESTAMP(Min(heure))  as min, identification_cc FROM table_stockage GROUP BY identification_cc "; 
$rep =  $connexion->prepare($ma_requette);
$rep->execute(array($ma_requette));
while ($val = $rep->fetch())
    {
        //Difference en secondes = Derniere heure moins Première Heure
    	$diff = $val['max']-$val['min'];
    	?> 	
    	<!--Stockage dans les attributs-->
    	<div id="difference_<?= $val['identification_cc'];?>" data-difference="<?= $diff;?>"></div>	
    	<?php
}
$rep->closeCursor();
;?>


<?php
//Moyenne de chaque local
$ma_requette="SELECT AVG(watts) as somme, identification_cc FROM table_stockage GROUP BY identification_cc"; 
$rep =  $connexion->prepare($ma_requette);
$rep->execute(array($ma_requette));
$boucle = 0;
while ($val = $rep->fetch())
    {
    	$somme = $val['somme'];
    	?> 	
    	<!--Stockage dans les attributs pour bâtons-->
    	<div id = "somme_local<?= $val['identification_cc'];?>" class="somme_locaux" data-total="<?= $somme;?>" ></div>  
    	<div id = "somme_id<?= $val['identification_cc'];?>" class="somme_locaux" data-id="<?= $val['identification_cc'];?>" ></div>   	
    	<?php
    	$boucle ++;

}
$rep->closeCursor();
;?>