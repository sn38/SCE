<script>
//Creer un graphique en ligne
function createChart(donnees,heure,emplacement,local){

	var ctx = document.getElementById('chart_'+emplacement).getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'line',
	    data: {
	        labels: heure,
	        datasets: [{
	            label: 'Watts',
	            data: donnees,
	            backgroundColor: [
	                'rgba(255, 99, 132, 0.2)'
	            ],
	            borderColor: [
	                'rgba(255, 99, 132, 1)'
	            ],
	            borderWidth: 0.8,
	            pointRadius: 0
	        }]
	    },
	    options: {
	    	responsive: true,
	    	scales: {
	       		xAxes: [{
	       			type: 'time',
		    		ticks: {
		        		maxRotation: 100,
          				minRotation: 50
		    		}
				}]
	    	}


		}

	});
	
	//Update du graphique toutes les 10 secondes
	var interValid = window.setInterval(function(){
  			getData(myChart,local);
			}, 5000);
};


//Creation du graphique total des valeurs
function createChartTotal(donnees,heure){

	var ctx = document.getElementById('chart_total').getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'line',
	    data: {
	        labels: heure,
	        datasets: [{
	            label: 'Watts',
	            data: donnees,
	            backgroundColor: [
	                'rgba(255, 99, 132, 0.2)'
	            ],
	            borderColor: [
	                'rgba(255, 99, 132, 1)'
	            ],
	            borderWidth: 0.8,
	            pointRadius: 0
	        }]
	    },
	    options: {
	    	responsive: true,
	    	scales: {
	       		xAxes: [{
	       			type: 'time',
		    		ticks: {
		        		maxRotation: 100,
          				minRotation: 50
		    		}
				}]
	    	}


		}

	});
	
	//Update du graphique toutes les 10 secondes
	var interValid = window.setInterval(function(){
  			getDataTotal(myChart,);
			}, 5000);
};


</script>


<!-- Mise à jour des graphiques avec AJAX-->
<script>
	function getData(mychart,local)
	{	
		console.log("lancement get data");
		//call ajax
		var ajax = new XMLHttpRequest();
		var method = "GET";
		var url = "data.php?id="+local;
		var asynchronous = true;
		
		ajax.open(method,url,asynchronous);
		//sending ajax requests
		ajax.send();

		//receinving response from data.php
		ajax.onreadystatechange= function()
		{
			if(this.readyState == 4 && this.status == 200){

				//Convert JSON back to array
				var data = this.responseText;
				//console.log(data); //for debbuging

				var html="";
				//Mettre a jour le graphique avec les données recupérée
				chartUpdate(mychart,data);

			}
		}
};


	//Mise à jour du graphique du total
	function getDataTotal(mychart)
	{	
		//Appeler Ajax
		var ajax = new XMLHttpRequest();
		var method = "GET";
		var url = "data_total.php";
		var asynchronous = true;
		
		ajax.open(method,url,asynchronous);
		//Envoyer requête AJAX
		ajax.send();

		//Recevoir données de data.php
		ajax.onreadystatechange= function()
		{	//Si le chargement des données est terminé et que c'est un succés
			if(this.readyState == 4 && this.status == 200){
				//Convertir données JSON
				var data = this.responseText;
				var html="";
				//Mettre a jour le graphique avec les données recupérée
				chartUpdate(mychart,data);

			}
		}
};


//Ajouter des données supplémentaires dans un graphe
function chartUpdate(chart, data) {
	//Obtenir date actuelle au format H:i:s
	var today = new Date();
	var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
	//Ajouter les valeurs à la suite du graphique
    chart.data.labels.push(time);
    chart.data.datasets.forEach((dataset) => {
        dataset.data.push(data);
    });
    chart.update();
};
</script>

<!--Créer les graphique lors d'un clic sur un local-->
<script>
	$(".bouton").click(function(){
		//recuperer numero du local cliquer
		let numero = $(this).data('compteur');
		// Savoir si le graphique a déjà été créé
		let existe = $("#chart_"+numero).data('existe');

		//Liste des id des locaux pour savoir l'ID du local cliqué
		var locaux = [1,2,3,4,584,2527,7];
		var local = locaux[numero];

		//Savoir le nombre de valeurs totales à récupérer
		let total = $("#total_"+local).data('total');
		//Les données à récupérer seront placées dans des listes
		const donnees = [];
		const noms = [];
		const heure = [];
		//Je récupère chaque données une par une
		for (let i = 0; i < total; i++) {
			//recupérer une donnée du local cliqué (i = numéro de la donnée)
			donnees.push($("#value_"+local+i).data("watts"));
			//Eviter d'avoir trop d'info sur l'axe des abscisses du graphique
			if (i % 4 == 0){
				heure.push($("#value_"+local+i).data("heure"));
				//console.log("heure : "+$("#value_"+local+i).data("heure"));
			}
			else{
				heure.push("");
			}
		}
		
		//Si le graphique n'est pas déroulé je le montre
		if(document.getElementById("chart_"+numero).style.display == "none"){
			//Si le graphique n'a pas été créé
			if(existe == 0){
				//supprimer ce qu'il y a dans l'emplacement du graphique
				$("#chart_"+numero).remove();
				//Ajouter le container du graphique
				$('<canvas style="display:none" class="remplacer" id=chart_'+numero+' data-existe = "1">'+'<?= "Tu as cliqué sur le numéro ";?> '+numero+'</canvas>').appendTo($("#container"+numero)).hide().slideDown(350);
				//Creation du graphique
				createChart(donnees,heure,numero,local);
			}
			//Si il existe déjà
			else{
				//Je déroule le graphique
				$("#chart_"+numero).hide().slideDown(350);
			}
			//colorer en bleu le local quand on clique
			document.getElementById('titre'+numero).className = 'text-primary appui curseur';
			//mettre l'icone cliqué  en bas
			document.getElementById('icon'+numero).className = 'fas fa-caret-up text-primary icone_carret';
		}
		//Si l'élément est déjà déroulé on le cache
		else{
			$("#chart_"+numero).slideUp();
			//Colorer en noir pour montrer que l'on a fermé le local
			document.getElementById('titre'+numero).className = 'text-dark appui curseur';
			document.getElementById('icon'+numero).className = 'fas fa-caret-down icone_carret';
		}

				

   }
   ); 
</script>


<!--Choix de l'affichage des parties-->
<script>
	//Clic sur la partie 2
	$("#title2").click(function(){
		//Montrer la partie et cacher l'autre
		document.getElementById("contenu2").style.display = "block";
		document.getElementById("contenu1").style.display = "none";
		//Changer classes pour montrer sur quelle partie on a cliqué
		document.getElementById('title2').className = 'bouton_title_total  deco_title curseur_title titre title_example';
		document.getElementById('title1').className = 'text-dark col-sm-3  bouton_title curseur_title titre title_example';

		console.log("ok2");
	
});
	//Clic sur la partie 1
	$("#title1").click(function(){
		//Montrer la partie et cacher l'autre
		document.getElementById("contenu1").style.display = "block";
		document.getElementById("contenu2").style.display = "none";
		//Changer classes pour montrer sur quelle partie on a cliqué
		document.getElementById('title2').className = 'bouton_title_total curseur_title titre title_example';
		document.getElementById('title1').className = 'text-dark col-sm-3  deco_title bouton_title  curseur_title titre title_example';
		console.log("ok1");		
});
</script>



<!--Affichager graphe total -->
<script>
//Lors d'un clic sur la partie 2 je créer les élements qu'elle doit contenir
$("#title2").click(function(){
		//Savoir combien de valeurs à récupérer
		let total = $(".somme_total").data('total');
		//Savoir si le graphique a déjà été créé
		let existe = $("#chart_total").data('existe');
		//Si le graphique n'a pas été créé je le créer
		if(existe == 0){
			//Les données seront mises dans une liste
			const donnees = [];
			const heure = [];
			//Ajout chaque données (somme par heure)
			for (let i = 0; i < total; i++) {
				//Ajouter la valeurs des watts
				donnees.push($("#heure_"+i).data("watts"));
				//Eviter le surplus de données sur l'axe des absisses
				if (i % 4 == 0)
					heure.push($("#heure_"+i).data("heure"));
				else
					heure.push("");
			}

			//Somme pour bar chart
			//Les id des locaux
			var locaux = [1,2,3,4,584,2527,7];
			//Les noms des locaux
			var name_locaux = {1:"LTP",2:"LTS1",3:"LTS2",4:"LTS3",584:"LTS4",2527:"LTS5",7:"LTS6"};
			//Valeurs contenu dans des listes
			const val_locaux = [];
			const id_locaux  =[];
			const time_diff = [];
			let conso = 0;
			//Pour chaque local
			locaux.forEach(function(local){
				//Récupérer le total des valeurs du local
				let somme = $("#somme_local"+local).data("total");
				//Récupérer la durée correspondante au total
				let diff = $("#difference_"+local).data("difference");
				//Calcul consommation et ajout dans tableau valeur
				conso = somme * diff/3600;
				// Ajout dans tableau valeur si c'est pas nul
				if(conso>0){
					//Les valeurs
					val_locaux.push(conso);
					//Les noms
					id_locaux.push(name_locaux[local]);
				}
			});

			//Vider la zone des élements indésirable
			$("#chart_total").remove();
			$("#bar_chart").remove();
				
			//Ajouter le container des graphiques
			$('<canvas id = "chart_total" class="remplacer_total" data-existe="1" ></canvas>').appendTo($("#container_total"));
			$('<canvas id = "bar_chart" class="remplacer_total" data-existe="1" ></canvas>').appendTo($("#container_bar"));

			//Creation des graphiques
			createChartTotal(donnees,heure);
			createBarChart(val_locaux,id_locaux);

	}
   }
   );
</script>



<script>
	//Creer graphique en barres
	function createBarChart(donnees,label){
		var ctx = document.getElementById('bar_chart').getContext('2d');
		var myChart = new Chart(ctx, {
		  type: 'bar',
		  data: {
	        labels: label,
	        datasets: [{
	            label: 'Watts-heures',
	            data: donnees,
	            backgroundColor: [
	                'rgba(0,123,255,0.8)'
	            ],
	            borderColor: [
	                'rgba(0,123,255, 1)'
	            ],
	            borderWidth: 0.8
	        }]
	    },
		  options: {
		    indexAxis: 'y',
		    elements: {
		      bar: {
		      }
		    },
		    responsive: true,
		    plugins: {
		      legend: {
		        position: 'right',
		      }
		    },
		    barPercentage : 0.3

		  },
		}
		)
};

</script>