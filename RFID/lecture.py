#!/usr/bin/env python
# -*- coding: utf8 -*-
# Version modifiee de la librairie https://github.com/mxgxw/MFRC522-python

import RPi.GPIO as GPIO
import MFRC522
import signal
import MySQLdb
import sys
import os
import time


# Variables for MySQL
db = MySQLdb.connect(host="localhost", user="root",passwd="Melec", db="RFID")
cur = db.cursor()

continue_reading = True

# Fonction qui arrete la lecture proprement
def end_read(signal,frame):
    global continue_reading
    print ("Lecture terminée")
    continue_reading = False
    GPIO.cleanup()

signal.signal(signal.SIGINT, end_read)
MIFAREReader = MFRC522.MFRC522()


while continue_reading:



    os.system('sudo pkill -f /home/pi/RFID-RC522/MFRC522.py')

    # Detecter les tags
    (status,TagType) = MIFAREReader.MFRC522_Request(MIFAREReader.PICC_REQIDL)

    # Une carte est detectee
    if status == MIFAREReader.MI_OK:
        print ("Carte detectee")

    # Recuperation UID
    (status,uid) = MIFAREReader.MFRC522_Anticoll()

    if status == MIFAREReader.MI_OK:

        uid2 = (str(uid[0])+str(uid[1])+str(uid[2])+str(uid[3]))

        print uid2
        
        if uid2 is None:
            GPIO.cleanup()
        else:
            print "Lecture de la base..."

            # Execute the SQL command
            sql = "SELECT * FROM ELEVE WHERE UID = (%s)"

            cur.execute(sql, [uid2])

            rows = cur.fetchall()
     
            print "ID = ",rows[0][0]
            print "Nom = ",rows[0][1]
            print "Prenom = ",rows[0][2]
            print "Email = ",rows[0][3]
            print "Groupe = ",rows[0][4]
            print "Entreprise = ",rows[0][5]
            print "UID = ",rows[0][6]
            print "Heure = ",time.strftime("%Y-%m-%d %H:%M")

        if status == MIFAREReader.MI_OK:
            #MIFAREReader.MFRC522_Read(8)
            MIFAREReader.MFRC522_StopCrypto1()
            GPIO.cleanup()

        else:
            GPIO.cleanup()
