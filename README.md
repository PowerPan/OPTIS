Open Public Transport Information System
=====

Open Public Transport Information System - OPTIS is a Information System for Public Transport Agencys and everyone how is interested in Public Transport

With OPTIS and GTFS you can create a awesome Inforamtion System for your Users.

At the Moment there are some great Modules
  - A Web Backend where you can make your Settings
  - A Map with your Stops and Routes
  - A Departuremonitor as a Website for everyone
  - A Departure Display Raspberry PI Software. Use your Raspberry Pi with a Display to create low-budget Departure Indoor Displays.

Setting up the Server
-----
For OPTIS you only need a simple default Installation of a Webserver with PHP and MySQL.

The Server should work fast. So we must collect Inforamtions from some MySQL Tables and put them into a new Table where all Information are stored togehther. This improve the speed of the Server.
The Best Way is, to Create a Cronjob that runs every night, after your PI's get there Data.

Create a Cronjob lik this. Be carefull the Path to your Script can distinguish.
In this Example the Pi gets the Data automaticly every day at 1 and 13 o'clock. Ok let us run the cronjob every night at 2 o'clock.
```
0 2 * * * /usr/bin/php /srv/www/htdocs/gtfs_import/departures.php
```


Install Raspberry PI Departuredisplays
-----
The First is the hardest one ;) When the First Display is runnig you can copy the MemoryCard for more Departuredisplays

Download the actuallay Debian/Raspian for Raspberry. 
You will find it here: http://www.raspberrypi.org/downloads

After the First Boot we must Install some packages. Open a command Prompt and enter the following commands:
```
sudo apt-get update
sudo apt-get install fbi chromium x11-xserver-utils unclutter mysql-server mysql-client python-mysqldb php5 php5-mysql libapache2-mod-php5
```

During the Installtion you are ask for a MySQL root Password. There you can enter whatever you want, you need this later for some further settings.

Now we bring some nessary Files to our Raspberry
```
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/display.py
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/createdatabase.sql
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/classMySQL.php
wget https://raw.github.com/PowerPan/OPTIS/master/abfahrtsmonitor/pi/index.php
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/jquery-2.0.3.min.js
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/jquery-2.0.3.min.map
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/read.php
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/send.php
wget https://github.com/PowerPan/OPTIS/raw/master/abfahrtsmonitor/pi/swwvvej.png
```

We must edit the display.py and the classMySQL.php to set the MySQL Passwort
In the display.py you must also set the right Path to your Server
Open each File with your favorite Editor. I like the vi-Editor

Now move some Files into the Webserver Directory
```
sudo chown pi.pi /var/www/
mv classMySQL.php index.php jquery-2.0.3.min.js jquery-2.0.3.min.map read.php swwvvej.png /var/www/
rm /var/www/index.html -f
``` 

In the next Step we create the Database
```
mysql -u root -p < createdatabase.sql
```

Test the Connection form the py to the Server
```
chmod +x display.py
./display.py -s
```

No we add the Cronjobs to the crontab
```
crontab -e
```

Add the following lines zo the crontab
```
*/1 * * * * /home/pi/display.py -s
0 1,13 * * * /home/pi/display.py -d
*/5 * * * * /home/pi/display.py -n
```

Edit the autostart file
sudo vi /etc/xdg/lxsession/LXDE/autostart
```
@lxpanel --profile LXDE
@pcmanfm --desktop --profile LXDE
@unclutter
@chromium --kiosk --incognito "http://localhost"
@xset s off
@xset -dpms
@xset s noblank
#@xscreensaver -no-splash

```
