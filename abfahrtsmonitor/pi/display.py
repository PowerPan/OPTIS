#!/usr/bin/python

import fcntl
import socket
import struct
import urllib
import json
import MySQLdb
from optparse import OptionParser


parser = OptionParser ("display.py [Optionen] Op")
parser.add_option("-d" , "--departures", action="store_true",dest="optDepartures", help="Abfahrten auslesen")
parser.add_option("-s" , "--settings", action="store_true",dest="optSettings", help="Einstellungen auslesen")
parser.add_option("-n" , "--notifications", action="store_true",dest="optNotifications", help="Notifications auslesen")
parser.add_option("-r" , "--rows", dest="optRows", help="Rows auf dem Server schreiben",nargs=1,type="int")

options, arguments = parser.parse_args()
		

#Function zum auslesen der MAC Adresse
def getMac(ifname):
	s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
	info = fcntl.ioctl(s.fileno(), 0x8927, struct.pack('256s', ifname[:15]))
	return ''.join(['%02x:' % ord(char) for char in info[18:24]])[:-1]

#URL zusammenbauen
def getActionUrl(action):
	mac = getMac('eth0')
	url = "http://itract.hs-woe.de/displays/?mac=%s&action=%s" % (mac,action)
	url = "http://192.168.250.16/swwv/displays/?mac=%s&action=%s" % (mac,action)
	return url

def getDBCursor():
	dbconn = MySQLdb.connect(host="localhost",user="root",passwd="i76triku",db="display")
	return dbconn.cursor()

def getData(url):
	f = urllib.urlopen(url)	
	content = f.read()
	f.close()
	return json.loads(content)

def setForceUpdateDone():
    print "Force Update Done"
    url = getActionUrl('forceupdatedone')
    getData(url)

def getDepartureData():
	print "Departures"
	url = getActionUrl('departures')
	data = getData(url)
	db = getDBCursor()
	db.execute("truncate departures")
	for departure in data['departures']:
		if departure['infotext'] == None:
			departure['infotext'] = " "
		if departure['trip_id'] == None:
			departure['trip_id'] = " "
		sql = "insert into departures (date,destination,route,infotext,trip_id) values ('%s','%s','%s','%s','%s');" % (departure['date'],departure['destination'],departure['route'],departure['infotext'],departure['trip_id'])
		db.execute(sql)
	db.execute("update departures set infotext = NULL where infotext = ' ';")
	db.execute("update departures set trip_id = NULL where trip_id = ' ';")

def getNotificationData():
	print "Notificstions"
	url = getActionUrl('notifications')
	data = getData(url)
	db = getDBCursor()
	db.execute("truncate notification")
	for notification in data['notifications']:
		sql = "insert into notification (notification_id,text,valid_from,valid_to) values ('%s','%s','%s','%s');" % (notification['notification_id'],notification['text'],notification['valid_from'],notification['valid_to'])
		db.execute(sql)
	
def getSettingsData():
	print "Settings"
	url = getActionUrl('settings')
	data = getData(url)
	db = getDBCursor()
	sql = "update settings set stopname = '%s', textsize = '%s',start = '%s',scrollamount = '%s',stop_ids = '%s' " % (data['stopname'],data['textsize'],data['start'],data['scrollamount'],data['stop_ids'])
	db.execute(sql)
        if data['forceupdate'] == 1:
            print "Forceupdate"
            getDepartureData()
            getNotificationData()
            setForceUpdateDone()

def writeDisplayRowsToServer():
    url = getActionUrl('writerows')
    url = "%s&rows=%s" % (url,options.optRows)
    data = getData(url)

help = 1

if options.optSettings:
    help = 0
    getSettingsData()

if options.optDepartures:
    help = 0
    getDepartureData()

if options.optNotifications:
    help = 0
    getNotificationData()

if options.optRows:
    help = 0
    writeDisplayRowsToServer()

if help == 1:
    parser.print_help()







