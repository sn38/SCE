# coding: UTF-8
"""
Script: Data_collector
Création: jhenriques, le 05/03/2021
"""

# Imports
import serial
from xml.etree.cElementTree import fromstring
import mysql.connector
import time
import datetime


# Functions
''' ------------------ Function to connect the USB Port ------------------ '''


def connect_usb():

    try:
        ser = serial.Serial('/dev/ttyUSB0', 57600)
        return ser
    except:
        print("\nThe system encountered a problem\n$$ PLEASE Check your wiring system $$\n")
        return 0


''' ---------------- Function that allows to stock the data in a database --------------- '''


def insert_bdd(watts, id_cc):
    donner = (watts, id_cc)
    cnx = mysql.connector.connect(user='root', password='password', host='localhost', database='sce_private')
    cursor = cnx.cursor()
    insert_val = ("""INSERT INTO table_stockage (watts, identification_cc) VALUES (%s, %s)""")
    cursor.execute(insert_val, donner)
    cnx.commit()
    cursor.close()
    cnx.close()
    


''' ---------------- Function that allows to delete the data in a database --------------- '''

def delete_bdd():
    while True:
        cnx = mysql.connector.connect(user='root', password='password', host='localhost', database='sce_private')
        cursor = cnx.cursor()
        delete_val = (""" DELETE FROM `table_stockage` """)
        cursor.execute(delete_val)
        cnx.commit()
        cursor.close()
        cnx.close()
        time.sleep(86400)


''' ------------------ Function that allows to read the data sent to the USB port ------------------- '''

def conso():
    ser = connect_usb()
    while ser == 0:
        ser = connect_usb()
        time.sleep(2)
    try:
        print("\nThe acquisition of data has started \n$$ DO NOT STOP THIS SCRIPT $$")
        while True:
            data = ser.readline()
            xml = fromstring(data)
            if xml.find('hist') is not None:
                fichier = open("logs.txt", "w")
                fichier.write("Big Data error\n")
                fichier.close()
            else:
                watts = int(xml.find('ch3').find('watts').text)
                id_cc = float(xml.find('id').text)
                insert_bdd(watts, id_cc)
                time.sleep(0.1)

    except:
        print("\nYOU'VE STOPPED THE SCRIPT !\n$$ Only staff are allowed to stop the script $$")



