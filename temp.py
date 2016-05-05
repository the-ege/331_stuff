#!/usr/bin/python

import sqlite3
import time 
from datetime import datetime
from dateutil.tz import tzlocal
from smbus import SMBus
import smbus
import logging
import atexit
def logConfig():
	logging.basicConfig(filename='/var/log/templog')

logConfig()
# create table

try:
	conn = sqlite3.connect('/var/log/temperature.db')
	conn.execute("CREATE TABLE IF NOT EXISTS temp (temp real, time text)")
except Exception, e:
	logging.error(e.args)

# configure I2C
addr = 0x48
bus = smbus.SMBus(1)

# read temp
try:
	data = bus.read_byte_data(addr, 0)
except Exception, e:
	logging.error(e.args)

degf = (data*1.8) + 32
tm = datetime.now(tzlocal()) 

# write to database
try:
	conn.execute("insert into temp(temp, time) values(?, ?)", (degf, tm))
	conn.commit()
	
	print("Wrote to db: " + str(degf))
except Exception, e:
	logging.error(e.args)

