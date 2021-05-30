"""
Programme : BaseDonnees.py
Proprietaire : Alexis REVOL
Création: le 01/03/2021
Derniere Modification : le 30/05/2021
IDE : PyCharm
Objectif : Lire BDD en python
"""

# Ce qui concerne la base de données
class Base_donnees:
    # Constructeur
    def __init__(self, user, host, bdd):
        self.user = user
        self.host = host
        self.base = bdd
        self.connecteur = None

    # Creer la connexion a la bdd
    def connexion(self, password):
        while True:
            try:
                self.connecteur = mysql.connector.connect(user=self.user, password=password, host=self.host,
                                                          database=self.base)
            except :
                continue
            break

    # on lit les données
    def read_data(self, nom,nbre_val):
        try:
            # Permet d'éxecuter des instrcutions SQL
            cursor = self.connecteur.cursor()
            # Moyenne sur la periode choisie
            read_data= ("SELECT AVG(watts),identification_cc,heure FROM table_stockage WHERE identification_cc='"+nom+"' ""ORDER BY heure desc limit " + str(nbre_val))
            # Execution de la requête
            cursor.execute(read_data)
            return cursor.fetchall()
        # Erreur requête retourner 0
        except:
            liste_vide = [(0,0,'0000-00-00 00:00:00',0,0)]
            return liste_vide