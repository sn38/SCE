"""
Programme : ParcInformatique.py
Proprietaire : Alexis REVOL
Création: le 01/03/2021
Derniere Modification : le 30/05/2021
IDE : PyCharm
Objectif : Lire, analyser et envoyer donnees de chaque local
"""

import mysql.connector
import requests
import zipfile, io
import csv, re
import statistics
from time import strftime, sleep, time
from datetime import datetime, timedelta


from .Transportor import *

# Concerne le parc informatique et les actions possibles
class ParcInformatique:
    # Constructeur
    def __init__(self, bdd):
        self.bdd = bdd  #BDD du parc informatique
        # Liste des locaux du parc informatique (id , nom)
        self.liste_locaux = [["1", "LTP"], ["2", "LTS1"], ["3", "LTS2"], ["4", "LTS3"],
                             ["584", "LTS4"],
                             ["2527", "LTS5"], ["7", "LTS6"]]
        self.attente = None #Attente entre chaque envoie


    # Se connecter a la bdd
    def se_connecter(self, mdp):
        self.bdd.connexion(mdp)

    # Lire la moyenne des donnees sur un interval
    def lire_donnee(self, local, intervalle):
        self.attente = intervalle
        nb_val = self.attente / 6  # combien de valeurs à lire selon l'interval
        data = self.bdd.lire_bdd(local, int(nb_val))   #Les donnees lues
        # si periode sur + d'1 jour repeter lecture chaque jour jusqu'a atteindre periode
        # Car la BDD s'efface toutes les 24h
        if((intervalle>86400) and (data[0][0]!= None)):
            # nbre de jour à répéter
            repetition = int(round(intervalle / 86400, 0))
            donnees = 0
            # Lire chaque jour
            for k in range(repetition):
                data = self.bdd.lire_bdd(local, int(nb_val))   #Je lis
                donnees += data[0][0]   # J'incremente
                sleep(86400)    #Attendre 1 jour
            # Quand c'est fini j'attribus les donnees
            data[0][0] = donnees
        # si c'est Null on attribut des donnees manuellement
        if (data[0][0] == None):
            data = [(0, 0, '0000-00-00 00:00:00', 0, 0)]
        return data

    # analyse puis transmettre donnee par methode post
    def transferer_donnee(self, nom, data, url, taux_co2):
        Transporteur(data, url, nom, self.attente, taux_co2)

    # Renvoyer liste des locaux
    def get_local_name(self):
        return self.liste_locaux

    # recupere fichier RTE avec taux de co2
    def extraction_RTE(self):
        # extraction du zip
        zip_file_url = "https://eco2mix.rte-france.com/download/eco2mix/eCO2mix_RTE_En-cours-TR.zip"
        r = requests.get(zip_file_url)
        z = zipfile.ZipFile(io.BytesIO(r.content))
        z.extractall("/home/pi/Documents/SCE")

    # Retourne les valeurs de chaque cellule du fichier
    def lecture_RTE(self):
        liste_val = []
        with open('/home/pi/Documents/SCE/eCO2mix_RTE_En-cours-TR.xls', encoding="ISO-8859-1") as File:
            reader = csv.reader(File)
            for line in reader:
                liste_val.append(re.split(r'\\t+', str(line)))  # split avec regex
        return liste_val

    # Lis le taux de cO2 du fichier pour hier
    def get_taux_co2(self):
        try:
            self.extraction_RTE()   # Recupere le fichier
            fichier = self.lecture_RTE()    #Separe chaque cellule
            # Date de hier
            yesterday = datetime.now() - timedelta(1)
            jour = datetime.strftime(yesterday, '%Y-%m-%d')
            # Je recupere le taux de cO2 de hier
            taux_co2 = []
            for ligne in fichier:
                #Pour chaque ligne du fichier si on est en France et que la date est celle d'hier
                if (ligne[0] == "['France") and (ligne[2] == jour):
                    taux_co2.append(int(ligne[17]))  # ajout du taux de co2 dans le total (1 valeur/15minutes)
            taux_co2 = statistics.mean(taux_co2)  # moyenne du total de cO2 sur la journée
            return taux_co2
        # Si erreur je genere une valeur approximative
        except:
            return 70
