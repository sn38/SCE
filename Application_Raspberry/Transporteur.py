"""
Programme : Transporteur.py
Proprietaire : Alexis REVOL
Création: le 01/03/2021
Derniere Modification : le 30/05/2021
IDE : PyCharm
Objectif :  Envoyer donnees au serveur
"""


# Ce qui concerne le transport des données entre les serveurs
class Transporteur:
    def __init__(self, donnees, url, nom, attente,taux_co2):
        self.donnees = donnees  #Liste des donnees lues
        self.url = url
        self.nom = nom  #Nom du local concerne
        self.attente = attente  #Attente entre chaque envoie
        self.valeur = None
        self.cle = ["valeur","nom","heure","table","unite","prix"]  #Cles JSON
        self.liste_stockage = {"300": "table_reception", "30": "table_consommation_30s", "3600": "table_consommation_heure", "86400": "table_consommation_jour", "604800": "table_consommation_semaine"}
        self.taux_co2 = taux_co2
        self.prix_kwh = 0.1582
        # Execution automatique des fonctions
        self.calcul_conso()
        self.convertir_json()
        self.envoyer_donnee()

    # Calculer la consommation a partir des donnees
    def calcul_conso(self):
        if self.attente!=300: # Si ce n'est pas 5 min il s'agit d'une consommation
            self.valeur = (self.donnees[0][0] * self.attente/3600)/1000 # Calcul conso en kwh
            self.cle[4]="consommation" # L'unité de la valeur
        else:   #Si l'attente est de 5 min j'envoie uniquement la puissance
            self.valeur = self.donnees[0][0]
            self.cle[4] = "puissance"

    # Recuperer emission cO2 pour la consommation
    def get_emission_co2(self):
        emission = float(self.taux_co2)*float(self.valeur) # Nbre de co2/kwh fois nbre kwh
        return emission

    #Renvoyer le prix de la consommation
    def get_prix(self):
        prix = self.prix_kwh*float(self.valeur)
        return prix

    # Converti les données en JSON
    def convertir_json(self):
        table=self.liste_stockage[str(self.attente)] #table dans laquelle les données seront stockées
        # Convertir en json etiquette + valeur
        mydata = {self.cle[0]: str(self.valeur), self.cle[1]: self.nom, self.cle[2]: str(self.donnees[0][2]),
                  self.cle[3]: str(table), "unite": self.cle[4]}
        #si c'est une consommation j'ajoute son emission de co2 et son prix dans la chaîne
        if(self.cle[4]=="consommation"):
            mydata["emission"] = self.get_emission_co2() # Ajouter l'émission cO2
            mydata["prix"] = self.get_prix()    # Ajouter le prix
        # J'attribue les données converties
        self.donnees = mydata

    # Envoyer les données au serveur WEB
    def envoyer_donnee(self):
        requette = requests.post(self.url, json=self.donnees)
        sleep(0.1)
        print(requette.status_code)
        #Si réponse HTTP pas OK on averti
        if(requette.status_code!=200):
            print("impossible d'envoyer les données")