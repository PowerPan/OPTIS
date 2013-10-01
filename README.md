Open Public Transport Information System
=====

Open Public Transport Information System - OPTIS is a Information System for Public Transport Agencys and everyone how is interested in Public Transport

With OPTIS and GTFS you can create a awesome Inforamtion System for your Users.

At the Moment there are some great Modules
  - A Web Backend where you can make your Settings
  - A Map with your Stops and Routes
  - A Departuremonitor as a Website for everyone
  - A Departure Display Raspberry PI Software. Use your Raspberry Pi with a Display to create low-budget Departure Indoor Displays.

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
