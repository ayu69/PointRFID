
# PointRFID

PointRFID est un projet qui est issu d'un cahier des charges simple :

Comment remplacer les feuilles de présence dans notre centre de formation.
**Après réflexions, l'idée est simple :**

 - L'élève arrive en cours
 - Il passe son badge devant le lecteur
 - Le lecteur lit le badge 
 - Il enregistre l'heure à laquelle l'élève est arrivé
 - A la fin du cours, le formateur passe son badge afin de générer un fichier excel listant les élève ainsi que leurs heures d'arrivée

**A cette idée simple se pose plusieurs problème :**

 - L'import des élève et de leurs UID de carte RFID doivent ce faire simplement
 - Les élèves ne doivent pas pouvoir badger plusieurs fois
 - Un retour visuel et sonore pour être compris par tous

**Le matériel nécessaire :**

 - 1 x raspberry pi zero w
 - 1 x RC522
 - 1 x LCD_I2C 2 lignes
 - 1 x Buzzer
 - 1 x Led Verte
 - 1 x Led Orange
 - 2 x resitance (à calculer selon vos leds)

 **optionel :**
 

 - 1 x imprimante 3D

## Améliorations envisageable :

 - Ajout d'un bouton d'import CSV
 - Ajout d'un bouton export CSV
 - Envoi de Mail avec les fichiers Excel.
 


Maintenant que l'on à les différentes données, nous allons pouvoir commencer à travailler !

# Câblage :

![wiring](https://zupimages.net/up/19/50/ia3p.jpg)
Interface de Gestion :
![PointRFID](https://image.noelshack.com/fichiers/2019/10/2/1551789698-capture.jpg)
# Programmation :

Nous partirons du principe que vous avez une installation fraiche de raspbian lite, et que vous savez vous connecter en ssh à votre raspberry.
Si vous êtes déja perdu :
[Installer Raspbian](https://www.framboise314.fr/installation-de-raspbian-pour-le-raspberry-pi-sur-carte-micro-sd-avec-etcher/)



De plus, l'idéal sera de paramétrer votre raspberry pi en "headless", et activé le SSH avant de mettre votre carte sd fraichement formatée.
Si vous ne savez pas comment faire :

[Raspberry pi Headless](https://core-electronics.com.au/tutorials/raspberry-pi-zerow-headless-wifi-setup.html)


Allez c'est partit !

**Depuis votre client ssh :**
On va commencer par configurer notre raspberry :

    sudo raspi-config

aller sur 4. Internationalization option, puis dans timezone et changer pour votre pays.

aller sur 5. Interfacing Options, puis dans P4. SPI et répondre Yes

activer au passage l'I2C.

et on redemarre :

    sudo reboot

on s'assure que le SPI fonctionne :

    lsmod | grep spi

Mise à jour :

    sudo apt-get update
    sudo apt-get upgrade

installation de python et des dependances :

    sudo apt-get install python2.7-dev python-pip git i2c-tools python-smbus 
    sudo apt-get install python-mysqldb
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

on va maintenant copier les fichiers nécessaire 

    mkdir /home/pi/PointRFID
    git clone https://github.com/ayu69/PointRFID.git /home/pi/PointRFID/
    chmod -R 7777 /home/pi/PointRFID


## Installation de NGINX, PHP, MySQL

## NGINX

    sudo apt install nginx php-fpm php7.0-mysql php7.0-curl
    
    sudo nano /etc/nginx/sites-available/default
Chercher et changer :

	index index.html index.htm index.nginx-debian.html;
en :

	index index.html index.htm index.php;

modifier également :

	 #location ~ \.php$ {
	 # include snippets/fastcgi-php.conf;
	 #
	 # # With php5-cgi alone:
	 # fastcgi_pass 127.0.0.1:9000;
	 # # With php5-fpm:
	 # fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
	 #}
en :

	 location ~ \.php$ {
 	include snippets/fastcgi-php.conf;
 	fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
 	}

On donne les droits :

    sudo chown -R pi:www-data /var/www/html/
    sudo chmod -R 770 /var/www/html/
 Et on test php :
 

    nano /var/www/html/index.php
    echo "<?php phpinfo(); ?>" > /var/www/html/index.php
    sudo /etc/init.d/nginx restart
 en allant sur `<VOTREADRESSEIP>/index.php` vous devriez voir la page d'info de php.

## MYSQL

on installe mysql server :

    sudo apt install mysql-server

on va se connecter à la base de données :

    sudo mysql --user=root
Executer chaque commande une par une :
on supprime l'utilisateur root éxistant :

    DROP USER 'root'@'localhost';
  puis on créer un nouvel utilisateur root avec votre mot de passe :

    CREATE USER 'root'@'localhost' IDENTIFIED BY 'VOTRESUPERMOTDEPASSE';

Par défaut le mot de passe que j'utilise pour les scripts est : MELEC
Si vous souhaiter le changer, il faudrait le mettre à jour dans tout les scripts fournis.

Et on donne tout les privilège à notre nouvel utilisateur :

    GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';


## Installation de Adminer:
on se place dans le dossier html :

    cd /var/www/html/
 on supprime le fichier de base de nginx
 
    rm index.nginx-debian.html
 on télécharge adminer
 
    wget https://github.com/vrana/adminer/releases/download/v4.7.1/adminer-4.7.1-mysql.php
on renomme adminer

    mv adminer-4.7.1-mysql.php adminer.php
et on redemarre nginx

    sudo /etc/init.d/nginx restart

## Installation de notre interface de gestion :

on va copier les fichiers :

    cp /home/pi/PointRFID/html/* /var/www/html/
puis on donne les droits :

    sudo chown -R pi:www-data /var/www/html/
    sudo chmod -R 770 /var/www/html/

On ajoute également l'utilisateur www-data à sudoers (afin de pouvoir éxecuter le script d'ajout en sudo)

    sudo visudo

et on ajoute sous l'user root :

    www-data ALL=(ALL) NOPASSWD:ALL

Dans ce cas précis, vous autorisez **toutes les commandes du systèmes** à être exécutées par votre utilisateur Nginx, bien évidemment c'est fortement déconseillé !

**Attention : ceci n'est pas du tout sécuritaire, mais notre système RFID état dans un réseaux local et non ouvert sur l’extérieur, cela pose moins de problèmes.**

## Creation de la base de données :

depuis `<VOTREADRESSEIP>/adminer.php`

![Adminer](https://image.noelshack.com/fichiers/2019/10/2/1551789698-capture2.jpg)

connectez-vous, puis allez dans "requete sql "

puis rentrer cette commande et éxécutez la :

    CREATE DATABASE PointRFID;
Nous avons ainsi créer la base de données PointRFID.
Nous allons maintenant créer la table worker, pour cela retourner à l'acceuil d'Adminer, et selectionnez la base PointRFID, puis aller sur requetes SQL, et rentrer ces deux commandes séparément :


Création de la table élève :

    CREATE TABLE WORKER (
        ID int NOT NULL AUTO_INCREMENT,
        NOM varchar(255),
        PRENOM varchar(255),
        GROUPE varchar(255),
        ENTREPRISE varchar(255),
        STATUT varchar(255),
	RFID_UID varchar(255),
        UNIQUE (ID),
        PRIMARY KEY (ID)
    );
Puis création de la table des Log :

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
![BDD Crée](https://image.noelshack.com/fichiers/2019/10/2/1551789698-capture3.jpg)
## Creation du service PointRFID

afin que noter pointeuse se lance automatiquement à chaque redémarrage, nous allons créer un service afin que le script python démarre automatiquement.

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


on donne les droits :

    sudo chmod 644 /lib/systemd/system/PointRFID.service
on rend notre script executable :

    chmod +x /home/pi/PointRFID/Script/lecture.py 
on met à jour systemctl :

    sudo systemctl daemon-reload 
on l'active :

    sudo systemctl enable PointRFID.service
et on le démarre :

    sudo systemctl start PointRFID.service


## Ajout dans la base de donnée WORKER :

Pour ajouter les personnes dans la base de donnée, il suffit de se rendre sur la page de gestion Adminer, puis d'aller dans la base PointRFID et enfin dans la table Worker.

Deux solutions s'offre à vous :

 - Ajout manuel des personnes via la page de la tables :
![Ajout BDD](https://image.noelshack.com/fichiers/2019/10/2/1551790688-capture.jpg)
 - Ajout via un CSV par le biais du bouton importer :
![enter image description here](https://image.noelshack.com/fichiers/2019/10/2/1551790628-capture.jpg)

Votre CSV devras alors être sous cette forme :

    ID;NOM;PRENOM;GROUPE;ENTREPRISE;STATUT;RFID_UID
    (laissez vide);votrenom;votreprenom;votregroupe;votreentreprise;votrestatut;(laissez vide);
    etc....
