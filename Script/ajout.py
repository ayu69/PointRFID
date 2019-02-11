#!/usr/bin/env python
# -*- coding: utf8 -*-

from pirc522 import RFID
import RPi.GPIO as GPIO    # Import Raspberry Pi GPIO library
import MySQLdb
import sys
import os

GPIO.setwarnings(False)

rdr = RFID()


out = open('/home/pi/PointRFID/Log/ajout.log','w')

sys.stdout = out
sys.stderr = out

ID=sys.argv[1]

# Variables for MySQL
db = MySQLdb.connect(host="localhost", user="root",passwd="MELEC", db="PointRFID")
cur = db.cursor()


while True:

	# Detecter les tags
	rdr.wait_for_tag()
	(error, tag_type) = rdr.request()
	if not error:
		print("Tag detected")
		(error, uid) = rdr.anticoll()
		if not error:
			print ID

			print("UID Carte : "+str(uid[0])+""+str(uid[1])+""+str(uid[2])+""+str(uid[3]))

			RFID_UID = (str(uid[0])+""+str(uid[1])+""+str(uid[2])+""+str(uid[3]))

			print "Ecriture dans la base..."

			# Execute the SQL command
			sql = "UPDATE WORKER SET RFID_UID = %s WHERE ID = %s"
			val = (RFID_UID, ID)

			cur.execute(sql, val)

			# Commit your changes in the database
			db.commit()
			print "Ecriture termine"
			rdr.cleanup()
			sys.exit()

