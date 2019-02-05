
sudo raspi-config

5. Interfacing Options, puis dans P4. SPI et répondre Yes 

puis active I2C

sudo reboot

lsmod | grep spi



sudo apt-get update
sudo apt-get upgrade


sudo apt-get install python2.7-dev python-pip git


sudo pip install spidev

sudo pip install RPi.GPIO

sudo pip install pi-rc522


COPIER ET TESTER LE SCRIPT test_RFID.py



INSTALLATION DE NGINX PHP MYSQL

	NGINX

sudo apt install nginx php-fpm php5-mysql php5-curl

sudo nano /etc/nginx/sites-available/default

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

 sudo chown -R www-data:pi /var/www/html/

 sudo chmod -R 770 /var/www/html/

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

CREATE TABLE WORKER PointRFID(
    ID int NOT NULL AUTO_INCREMENT,
    NOM varchar(255),
    PRENOM varchar(255),
    GROUPE varchar(255),
    ENTREPRISE varchar(255),
    STATUT varchar(255),
    UNIQUE (ID),
    PRIMARY KEY (ID)
);

CREATE TABLE LOG PointRFID(
    ID int NOT NULL,
    NOM varchar(255),
    PRENOM varchar(255),
    GROUPE varchar(255),
    ENTREPRISE varchar(255),
    HEURE varchar(255),
    PRIMARY KEY (ID)
);



