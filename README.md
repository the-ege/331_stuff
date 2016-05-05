---------------------------------------
 			Configuration
---------------------------------------
steps to connect to temperature sensor

	$ sudo apt-get install i2c-tools
	$ sudo apt-get install python-smbus
	$ sudo raspi-config
		-Advanced Options
		-I2C
		-enable ARM I2C interface
		-load I2C module by default
	$ sudo i2cdetect -y 1
		( take note of address )
	$ sudo apt-get install python-dateutil
	$ sudo apt-get install python-smbus
		
With sensor address script could be written.
Data was read from byte 0 from address 0x48

--------------------------------------
			Cron-Setup	
--------------------------------------

Temperature must be logged every minute,
and cron was used to periodically call the
program.

	$ sudo crontab -e
		-insert
			* * * * * /home/pi/Desktop/templogger/temp.py
		or generally
			* * * * * <path to script>

To run task every minute, the period of the cron
job must be set to all *'s. Important to note
that script requires root privileges and 
had to be placed in root crontab, hence the 
sudo.

---------------------------------------
			  PHP-Setup
---------------------------------------
To prepare pi for running a web server,
run the following commands:

	$ sudo apt-get install lighttpd php5 php5-cgi php5-sqlite php5-gd
	$ sudo systemctl stop apache
	$ sudo systemctl disable apache
	$ sudo lighty-enable-mod fastcgi
	$ sudo lighty-enable-mod fastcgi-php
	$ sudo systemctl restart lighttpd

In /var/www/html a PHP file can be created,
but root privilege is required.
	$ sudo vim graph.php
	$ sudo chmod 644 graph.php

Now the pi server(as designed in graph.php) is 
constantly running and can be viewed
at dystort.net:6069/graph.php
