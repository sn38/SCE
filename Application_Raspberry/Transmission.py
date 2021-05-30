# coding: UTF-8
"""
Programme : transmission.py
Proprietaire : Alexis REVOL
Création: le 01/03/2021
Derniere Modification : le 30/05/2021
IDE : PyCharm
Objectif : Lire les donnees locale et les transmettre à un serveur
"""

# Importer les fonctionnalités supplementaires
import mysql.connector
import threading
import requests
import zipfile, io
import csv, re
import statistics
from time import strftime, sleep, time
from datetime import datetime, timedelta

#Utiliser les classes
from BaseDonnees import *
from ParcInformatique import *

# Programme principal
def programmeTransfert(url_maison,attente):
    # base de données locale
    ma_bdd = Base_donnees('root', '127.0.0.1', 'sce_private')
    parc_informatique = ParcInformatique(ma_bdd)    # Creation du parc info
    parc_informatique.se_connecter('password')  # Connexion à sa bdd
    liste_locaux = parc_informatique.get_local_name() # Recupere le nom de tous les locaux
    taux_co2 = parc_informatique.get_taux_co2()  # Recuperation du taux de co2 par kWh
    while True: #tourner en boucle
        for local in liste_locaux:  #pour chaque locaux du parc info
            donnees = parc_informatique.lire_donnee(local[0],attente) # On lit ses données (local[0]->id)
            # on les envoie à l'url (local[1]->nom)
            parc_informatique.transferer_donnee(local[1],donnees, url_maison, taux_co2)
            # si une semaine s'est écoulée j'actualise le taux de co2
            lancement = round(time(),0)
            if(lancement%604800==0):
                # Recuperer nouveau taux
                taux_co2 = parc_informatique.get_taux_co2()
        sleep(attente)  # Patienter x secondes avant de repeter


# Lancement du programme principal avec differentes periodes
def envoie():
    # URL de réception des données
    url = 'https://sce.lycee-lgm.fr/accueil/receptionMoyenne'
    # Tester fonctionnement du programme
    try:
        #Envoie des données en dur toutes les 5 minutes
        donne_classic = threading.Thread(target = programmeTransfert, args= (url,300))
        # Envoie de la consommation toutes les 30 secondes
        donne_conso_sec = threading.Thread(target=programmeTransfert, args=(url, 30))
        #Envoie de la consommation toutes les 1 heures
        donne_conso_heure = threading.Thread(target=programmeTransfert, args=(url, 3600))
        #Envoie de la consommation tout les 1 jours
        donne_conso_jour = threading.Thread(target=programmeTransfert, args=(url, 86400))
        #Envoie de la consommation toutes les 1 semaines
        donne_conso_semaine = threading.Thread(target=programmeTransfert, args=(url, 604800))
        # lancement des differents envoies
        donne_classic.start()
        donne_conso_sec.start()
        donne_conso_heure.start()
        donne_conso_jour.start()
        donne_conso_semaine.start()

    #Si erreur l'arrête
    except:
        print("arrêt du programme")



