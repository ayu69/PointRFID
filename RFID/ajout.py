#!/usr/bin/env python
# -*- coding: utf8 -*-
# Version modifiee de la librairie https://github.com/mxgxw/MFRC522-python

import RPi.GPIO as GPIO
import MFRC522
import signal
import MySQLdb
import sys
import os

out = open('/home/pi/RFID/ajout.log','w')
sys.stdout = out
sys.stderr = out

id=sys.argv[1]

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



	os.system('sudo systemctl stop RFID')

	# Detecter les tags
	(status,TagType) = MIFAREReader.MFRC522_Request(MIFAREReader.PICC_REQIDL)

	# Une carte est detectee
	if status == MIFAREReader.MI_OK:
		print ("Carte detectee")

	# Recuperation UID
	(status,uid) = MIFAREReader.MFRC522_Anticoll()

	if status == MIFAREReader.MI_OK:
		print 'ID eleve =', id

		uid2 = (str(uid[0])+""+str(uid[1])+""+str(uid[2])+""+str(uid[3]))

		print "Ecriture dans la base..."

		# Execute the SQL command
		sql = "UPDATE ELEVE SET UID = %s WHERE ID = %s"
		val = (uid2, id)

		cur.execute(sql, val)

		# Commit your changes in the database
		db.commit()
		print "Ecriture termine"

		# Clee d authentification par defaut
		key = [0xFF,0xFF,0xFF,0xFF,0xFF,0xFF]

		# Selection du tag
		MIFAREReader.MFRC522_SelectTag(uid)

		# Authentification
		status = MIFAREReader.MFRC522_Auth(MIFAREReader.PICC_AUTHENT1A, 8, key, uid)

		if status == MIFAREReader.MI_OK:
			#MIFAREReader.MFRC522_Read(8)
			MIFAREReader.MFRC522_StopCrypto1()
			os.system('sudo systemctl start RFID')
			sys.exit()

		else:
			os.system('sudo systemctl start RFID')
			sys.exit()

