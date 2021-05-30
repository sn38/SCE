 <!--Interface WEB présentant l'évolution des mesures de chaque local-->
 <!--Réalisée par Alexis REVOL-->

 <head>
	 <!--Utiliser les fonctionnalités supplémentaires (Boostrap, Chart.js, Jquery)-->
	<?php include("includes/liens.php"); ?>
</head>


<body class="total_background">
	<div class="entete_couleur">
		<img class="logo_lgm" src="image/logo_du_lgm-ConvertImage.png" alt="logo du lycee"/>
		<p class="marge_bienvenue text-light h4"> <strong>Bienvenue sur le site SCE local du LGM !</strong> </p>
	</div>
	<h5 class="text-primary trait welcome_example">Suivre ma consommation électrique en temps réel</h5>

	<!--Requêtes PHP-->
	<?php include("includes/requetes.php"); ?>

	<p id="data" class=""></p>
	<!--Choisir quelle partie afficher-->
	<h4 class="text-dark col-sm-3 bouton_title curseur_title titre deco_title title_example" data-id="1" id="title1">Tableau de bord</h4> 
	<h4 class="bouton_title_total  curseur_title titre title_example" data-id="2" id="title2">Total</h4>


	<!--Partie par local-->
	<div class=" w-75 p-3 mx-auto tableau_background tableau_bordure" id="contenu1"> 
		<!--Contenu parti local -->
		<h5 class="text-primary border-bottom border-primary" style="padding-bottom:5 ;"><i class="fas fa-bolt"></i>  Tableau de bord
			<?php //Lister les locaux
			$locaux = array("Local principal","Local secondaire 1","Local secondaire 2","Local secondaire 3","Local secondaire 4","Local secondaire 5","Local secondaire 6","Local secondaire 7");
			$compteur=0;
			//Afficher chaque bouton "local"
			foreach ($locaux as $local){
				?>
				<!--Contenu de chaque local-->
				<h6 class="font-weight-bold pt-4 bouton"  data-compteur="<?= $compteur; ?>" >
					<!--Nom du local-->
					<p  class="text-dark appui curseur" id="titre<?= $compteur;?>">
					<?= $local ?>
						<!--Icone triangle-->
						<span id="boite_icone<?= $compteur;?>">
							<i class="fas fa-caret-down icone_carret" id="icon<?= $compteur;?>" ></i>
						</span>
					</p>
					<div id="identifiant<?= $compteur?>"></div>
					</br>

					<!--Contenu des graphiques-->
					<div id="container<?= $compteur;?>">
						<canvas style="display:none" class="remplacer" id="chart_<?= $compteur;?>" data-existe = "0"></canvas>
					</div>
				</h6>

				<?php //Compter les nombres de boucles
				$compteur+=1;
			}
			?>

		</h5>

	</div>
	</br></br></br></br>

	<!---La partie total-->
	<div style="display:none"  class="w-75 p-3 mx-auto tableau_bordure tableau_background container_2" id="contenu2">
			<!--Legende de la partie-->
			<h5 class="text-primary border-bottom border-primary " style="padding-bottom:5 ;"><i class="fas fa-bolt"></i>   Total de ma consommation
			</h5>

			<!--Pourcentage de conso par Heure-->
			<div class="responsive_circle">
				<h7 class="matin hour_example">0h à 8h</h7>
				<h7 class="aprem hour_example">8h à 16h</h7>
				<h7 class="soir hour_example">16h à 24h</h7>
				<!--Pour le matin-->
				<div id="matin" class="circle"><i class="fas fa-cloud-sun h3 text-primary text_example day"></i>
					<!--Affichage du pourcentage-->
					<p class="h2 chiffre_matin  chiffre_pos text_example">
					<?= $matin." %" ;?>
					</p>
				</div>
				<!--Pour le midi-->
				<div id="midi" class="circle"><i class="far fa-sun h3 text-primary text_example day"></i>
					<!--Affichage du pourcentage--> 
					<p class="h2 chiffre_midi chiffre_pos text_example">
					<?= $midi." %" ;?>
					</p>
				</div>
				<!--Pour le soir-->
				<div id="soir" class="circle"> <i class="far fa-moon h3 text-primary text_example day"></i>
					<!--Affichage du pourcentage-->
					<p class="h2 chiffre_soir  chiffre_pos text_example">
					<?= $soir." %" ;?>
					</p>
				</div>
			</div>

		</br>
			<!--Graphique consommation totale -->
			<div class="border_under border_marge1">
				<h6 class="text-dark font-weight-bold pt-4 under_circle "> Évolution de la consommation</h6>
				<!--Contenu du graphique-->
				<div id="container_total"> 
					<canvas id="chart_total" data-existe="0"></canvas>
				</div>
			</div>

			<!--Graphique en barres-->
			<div class="border_under border_marge2">
				<h6 class="text-dark font-weight-bold pt-4 under_total border_unde"> Comparaison des différents locaux</h6>
				<!--Contenu du graphique-->
				<div id="container_bar">
					<canvas id="bar_chart"> </canvas>
				</div>
			</div>
	</div>
</body>

<!-- Les fonctions JavaScript-->
<?php include("includes/fonctionsJS.php"); ?>

 