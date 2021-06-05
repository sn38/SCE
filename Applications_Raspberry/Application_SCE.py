from Data_collector import *
from Transmission import *
from threading import Thread


def main():
    #Lancement des trois programmes en parallèles
    programme_Jordan = Thread(target = conso)
    programme_Jordan_delete = Thread(target = delete_bdd)
    programme_Alexis = Thread(target = envoie)
    
    try:
        print("J")
        programme_Jordan.start()
    except:
        print("Jordan impossible")
    try:
        #print("JD")
        programme_Jordan_delete.start()
    except:
        print("Jordan delete impossible")
    try:
        print("AAAA")
        programme_Alexis.start()
    except:
        print("Alexis impossible")


# Lancement du programme principal
if __name__ == '__main__':
    main()
# Fin
