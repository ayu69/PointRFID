![enter image description here](https://image.noelshack.com/fichiers/2019/07/2/1550010186-wiring.jpg)

    sudo raspi-config

aller sur 5. Interfacing Options, puis dans P4. SPI et répondre Yes 

changer aussi la timezone


puis active I2C

    sudo reboot
    
    lsmod | grep spi

Mise à jour 

    sudo apt-get update
    sudo apt-get upgrade

installation de python et des dependances :

    sudo apt-get install python2.7-dev python-pip git i2c-tools python-smbus 
    sudo pip install spidev
    sudo pip install RPi.GPIO
    sudo pip install pi-rc522
    sudo pip install mysql-connector-python
    sudo pip install openpyxl
On verifie l'ajout de l'I2C

    sudo nano /etc/modules
On devrait trouver ces deux lignes 

    i2c-bcm2708
    i2c-dev

COPIER ET TESTER LE SCRIPT test_RFID.py

    chmod -R 7777 /home/pi/PointRFID


INSTALLATION DE NGINX PHP MYSQL

NGINX

    sudo apt install nginx php-fpm php5-mysql php5-curl
    
    sudo nano /etc/nginx/sites-available/default
Chercher et changer :

	index index.html index.htm index.nginx-debian.html;
to

	index index.html index.htm index.php;

modifier 

	 #location ~ \.php$ {
	 # include snippets/fastcgi-php.conf;
	 #
	 # # With php5-cgi alone:
	 # fastcgi_pass 127.0.0.1:9000;
	 # # With php5-fpm:
	 # fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
	 #}
to

	 location ~ \.php$ {
 	include snippets/fastcgi-php.conf;
 	fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
 	}

On donne les droits :

    sudo chown -R pi:www-data /var/www/html/
    sudo chmod -R 770 /var/www/html/
 test de php :
 

    nano /var/www/html/index.php
    echo "<?php phpinfo(); ?>" > /var/www/html/index.php
    sudo /etc/init.d/nginx restart

MYSQL

    sudo apt install mysql-server
    
    sudo mysql --user=root

    DROP USER 'root'@'localhost';
    CREATE USER 'root'@'localhost' IDENTIFIED BY '*********';
    GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';


COPIER ADMINER

    cd /var/www/html/
    
    rm index.nginx-debian.html
    
    wget https://github.com/vrana/adminer/releases/download/v4.7.1/adminer-4.7.1-mysql.php
    
    mv adminer-4.7.1-mysql.php adminer.php
    
    sudo /etc/init.d/nginx restart


CREATION de LA BDD

depuis 

ip/adminer.php

requete sql 

    CREATE DATABASE PointRFID;
    
    CREATE TABLE WORKER (
        ID int NOT NULL AUTO_INCREMENT,
        NOM varchar(255),
        PRENOM varchar(255),
        GROUPE varchar(255),
        ENTREPRISE varchar(255),
        STATUT varchar(255),
        UNIQUE (ID),
        PRIMARY KEY (ID)
    );
    
    CREATE TABLE LOG (
        ID int NOT NULL AUTO_INCREMENT,
        NOM varchar(255),
        PRENOM varchar(255),
        GROUPE varchar(255),
        ENTREPRISE varchar(255),
        RFID_UID varchar(255),
        HEURE varchar(255),
        EXPORT varchar(32),
        UNIQUE (ID),
        PRIMARY KEY (ID)
    );

Creation du service 

    sudo nano /lib/systemd/system/PointRFID.service
puis on ecrit :

    [Unit] 
    Description=PointRFID
    After=multi-user.target 
    
    [Service] 
    Type=simple 
    ExecStart=/usr/bin/python /home/pi/PointRFID/Script/lecture.py 
    Restart=on-abort 
    
    [Install] 
    WantedBy=multi-user.target



    sudo chmod 644 /lib/systemd/system/PointRFID.service
    chmod +x /home/pi/PointRFID/Script/lecture.py 
    sudo systemctl daemon-reload 
    sudo systemctl enable PointRFID.service
    sudo systemctl start PointRFID.service


