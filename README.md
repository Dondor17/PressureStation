#Pressure Station

- Pressure Station is a project that easily allows you to measure, display and keep track of atmospheric pressure.

##Installation and requirements
**Requirements:**

- BMP180 Barometric Pressure/Temperature/Altitude Sensor
- Any model of Raspberry Pi
- Breadboard
- Set of jumper wires


**Project has been tested with the following configuration:**
-  Raspberry Pi 3 model B
- Raspbian Stretch Lite (minimal distro)
- Nginx 1.10.3
- PHP 7.0
- MySQL 5.6

**Setting up BMP180:**

![](https://i.imgur.com/WE52kQO.png)


**Installation:**
- Once in Raspbian, enable I2C via `raspi-config`
- Test your BMP180 via `i2cdetect -y 1` (if you are using Rpi3)
- Install the following packages 
`sudo apt update`
`sudo apt install python-smbus`
`sudo apt install i2c-tools`
- Install Nginx, PHP and MySQL
`sudo apt install nginx, php7.0, mysql-server`
- After configuration, move Pressure Station files to root folder of your webserver and change details in `config.php`
- Change `apiKey` variable in `main.py` corresponding to the one in `config.php`
- Run `python main.py` and enjoy!

