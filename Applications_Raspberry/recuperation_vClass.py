# coding: UTF-8
"""
Script: pythonProjectOK/recuperation_vClass
Création: user, le 12/03/2021
IDE : PyCharm
"""

# Import
import mysql.connector
import threading
import requests
import zipfile, io
import csv, re
import statistics
from time import strftime, sleep, time
from datetime import datetime, timedelta

# Class

# Concerne les informations du local ainsi que les actions possibles
class ParcInformatique:
    # Constructeur
    def __init__(self, bdd):
        self.bdd = bdd
        #[identifiant, nom salle]
        #self.liste_locaux = [["local1", "LTP"], ["584", "LTS1"], ["local3", "LTS2"], ["local4", "LTS3"], ["local5", "LTS4"],
                   #["local6", "LTS5"], ["local7", "LTS6"]]
        self.liste_locaux = [["1", "LTP"], ["2", "LTS1"], ["3", "LTS2"], ["4", "LTS3"],
                             ["584", "LTS4"],
                             ["2527", "LTS5"], ["7", "LTS6"]]
        self.attente = None

    #se connecter a la bdd
    def se_connecter(self, mdp):
        self.bdd.connexion(mdp)

    #recuperer moyenne puissance des 5 dernieres min
    def lire_donnee(self,local,intervalle):
        self.attente = intervalle
        nb_val = self.attente/6 #combien de valeurs à récupérer
        data = self.bdd.read_data(local,int(nb_val))
            
        #si c'est Null
        if(data[0][0]==None):
            data = [(0,0,'0000-00-00 00:00:00',0,0)]
        else:
            #si conso sur 1 jour
            if(intervalle<=86400):
                data = self.bdd.read_data(local,int(nb_val))
            #sur + d'1 jour
            if(intervalle>86400):
                #nbre de jour à répéter
                repetition = int(round(intervalle/86400,0))
                donnees = 0
                #Lire chaque jour
                for k in range (repetition):
                    data = self.bdd.read_data(local,int(nb_val))
                    donnees += data[0][0]
                    #attendre 1 jour
                    sleep(86400)
                #on attribut la nouvuelle data
                data[0][0] = donnees
        
        return data

    #transmettre donnee par post
    def transferer_donnee(self, nom, data, url, taux_co2):
        Transport_data(data, url, nom,self.attente, taux_co2)

    def get_local_name(self):
        return self.liste_locaux

    #recupere fichier RTE avec taux de co2
    def extraction_RTE(self):
        # extraction du zip
        zip_file_url = "https://eco2mix.rte-france.com/download/eco2mix/eCO2mix_RTE_En-cours-TR.zip"
        r = requests.get(zip_file_url)
        z = zipfile.ZipFile(io.BytesIO(r.content))
        z.extractall("/home/pi/Documents/SCE")

    #Retourne les valeurs du fichier
    def lecture_RTE(self):
        liste_val = []
        with open('/home/pi/Documents/SCE/eCO2mix_RTE_En-cours-TR.xls',encoding = "ISO-8859-1") as File:
            reader = csv.reader(File)
            for line in reader:
                liste_val.append(re.split(r'\\t+', str(line)))  # split avec regex
        return liste_val

    def get_taux_co2(self):
        try:
            self.extraction_RTE()
            fichier = self.lecture_RTE()
            #Date de hier
            yesterday = datetime.now() - timedelta(1)
            jour = datetime.strftime(yesterday, '%Y-%m-%d')
            #Je recupere le taux de cO2 de hier
            taux_co2 = []
            for ligne in fichier:
                if (ligne[0] == "['France") and (ligne[2] == jour):
                    taux_co2.append(int(ligne[17])) #ajout du taux de co2 de chaque periode (1 valeur/15minutes)
            taux_co2 = statistics.mean(taux_co2)  # moyenne du cO2 sur la journée
            return taux_co2
        except:
            return 70


# Ce qui concerne la base de données
class Base_donnees:
    # Constructeur
    def __init__(self, user, host, bdd):
        self.user = user
        self.host = host
        self.base = bdd
        self.connecteur = None

    # test la connexion a la bdd
    def connexion(self, password):
        while True:
            try:
                self.connecteur = mysql.connector.connect(user=self.user, password=password, host=self.host,
                                                          database=self.base)
            except :
                continue
            break

    # on récupère les données
    def read_data(self, nom,nbre_val):
        try:
            # Permet d'éxecuter des instrcutions SQL
            cursor = self.connecteur.cursor()
            # moyenne des 5 dernieres minutes
            read_data= ("SELECT AVG(watts),identification_cc,heure FROM table_stockage WHERE identification_cc='"+nom+"' ""ORDER BY heure desc limit " + str(nbre_val))
            # Execution de la requête
            cursor.execute(read_data)
            return cursor.fetchall()
        #erreur requête retourner 0
        except:
            liste_vide = [(0,0,'0000-00-00 00:00:00',0,0)]
            return liste_vide



# Ce qui concerne le transport des données entre les serveurs
class Transport_data:
    def __init__(self, donnees, url, nom, attente,taux_co2):
        self.donnees = donnees
        self.url = url
        self.nom = nom
        self.attente = attente
        self.valeur = None
        self.cle = ["valeur","nom","heure","table","unite","prix"]
        self.liste_stockage = {"300": "table_reception", "30": "table_consommation_30s", "3600": "table_consommation_heure", "86400": "table_consommation_jour", "604800": "table_consommation_semaine"}
        self.taux_co2 = taux_co2
        self.prix_kwh = 0.1582
        # Execution automatique des fonctions
        self.calcul_conso()
        self.convertir_json()
        self.envoyer_donnee()

    def calcul_conso(self):
        if self.attente!=300: #si ce n'est pas 5 min il s'agit d'une conso
            self.valeur = (self.donnees[0][0] * self.attente/3600)/1000 #calcul conso en kwh
            self.cle[4]="consommation" # l'unité de la valeur
        else:
            self.valeur = self.donnees[0][0]
            self.cle[4] = "puissance"

    #recuperer emission cO2
    def get_emission_co2(self):
        emission = float(self.taux_co2)*float(self.valeur) #nbre de co2/kwh fois nbre kwh
        return emission

    def get_prix(self): #retourne prix de la consommation
        prix = self.prix_kwh*float(self.valeur)
        return prix

    # Converti les données en JSON
    def convertir_json(self):
        table=self.liste_stockage[str(self.attente)] #table dans laquelle les données seront stockées
        # converti en json etiquette + valeur
        mydata = {self.cle[0]: str(self.valeur), self.cle[1]: self.nom, self.cle[2]: str(self.donnees[0][2]),
                  self.cle[3]: str(table), "unite": self.cle[4]}
        #si c'est une conso j'ajoute son emission de co2 et son prix
        if(self.cle[4]=="consommation"):   
            mydata["emission"] = self.get_emission_co2() #ajouter l'émission cO2
            mydata["prix"] = self.get_prix()    #ajouter le prix
        # On attribue les données convertis
        self.donnees = mydata

    # envoie les données au serveur WEB
    def envoyer_donnee(self):
        requette = requests.post(self.url, json=self.donnees)
        print(requette.status_code)
        #Si réponse HTTP pas OK on averti
        if(requette.status_code!=200):
            print("impossible d'envoyer les données")


#Programme principal
def programmeTransfert(url_maison,attente):
    # bae de données locale
    ma_bdd = Base_donnees('root', '127.0.0.1', 'sce_private')
    parc_informatique = ParcInformatique(ma_bdd)    #creation du parc info
    parc_informatique.se_connecter('password')  #connexion à sa bdd
    liste_locaux = parc_informatique.get_local_name() #recupere le nom de tous les locaux
    taux_co2 = parc_informatique.get_taux_co2()  #recuperation du taux de co2 par kWh
    while True: #tourner en boucle
        for local in liste_locaux:  #pour chaque locaux du parc info
            donnees = parc_informatique.lire_donnee(local[0],attente) #on lit ses données (local[0]->id)
            parc_informatique.transferer_donnee(local[1],donnees, url_maison, taux_co2)  # on les envoie à l'url (local[1]->nom)
            #si une semaine s'est écoulée j'actualise le taux de co2
            lancement = round(time(),0)
            if(lancement%604800==0):
                taux_co2 = parc_informatique.get_taux_co2()
        sleep(attente)  # répéter toutes les x secondes


# Programme principal
def envoie():
    print("Lancement du programme Alexis")
    # URL de réception des données
    url_sebastien = 'https://sce.lycee-lgm.fr/accueil/receptionMoyenne'
    #tester fonctionnement du programme
    try:
        #Envoie des données en dur toutes les 5 minutes
        donne_classic = threading.Thread(target = programmeTransfert, args= (url_sebastien,300))
        # Envoie de la consommation toutes les 30 secondes
        donne_conso_sec = threading.Thread(target=programmeTransfert, args=(url_sebastien, 30))
        #Envoie de la consommation toutes les 1 heures
        donne_conso_heure = threading.Thread(target=programmeTransfert, args=(url_sebastien, 3600))
        #Envoie de la consommation tout les 1 jours
        donne_conso_jour = threading.Thread(target=programmeTransfert, args=(url_sebastien, 86400))
        #Envoie de la consommation toutes les 1 semaines
        donne_conso_semaine = threading.Thread(target=programmeTransfert, args=(url_sebastien, 604800))

        donne_classic.start()
        donne_conso_sec.start()
        donne_conso_heure.start()
        donne_conso_jour.start()
        donne_conso_semaine.start()

    #Si erreur l'arrête
    except:
        print("arrêt du programme")



