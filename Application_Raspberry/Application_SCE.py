from Data_collector import *
from Transmission import *
from threading import Thread


def main():
    #Lancement des trois programmes en parallèles
    # Programme de lecture de la station et stockage local
    programme_Jordan = Thread(target = conso)
    programme_Jordan_delete = Thread(target = delete_bdd)
    # Programme lecture des donnees local et envoi des donnees
    programme_transmission = Thread(target = envoie)

    #Tester les fonctionnement de chaque application
    try:
        programme_Jordan.start()
    except:
        print("Jordan impossible")
    try:
        programme_Jordan_delete.start()
    except:
        print("Jordan delete impossible")
    try:
        programme_transmission.start()
    except:
        print("Transmission impossible")


# Lancement du programme principal
if __name__ == '__main__':
    main()
# Fin
