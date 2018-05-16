# Pressure Station
 
- The aim of this project was to create a small and affordable station that easily allows one to measure, display and keep track of atmospheric pressure.

##Project description
**Layout**
- The **hardware part** consists of a Raspberry Pi and BMP180 sensor connected together via I2C
- The **software part** consists of Python and PHP scripts which are working together to store captured data in the Database.

**Execution**

**1.** Python script running in the background collects the values from the BMP180 sensor via Adafruit_BMP085/BMP180 python library. This is made possible via `getPressure()` function
```python
import Adafruit_BMP.BMP085 as BMP085
import requests
import time
import random

sensor = BMP085.BMP085()
apiKey = ""

def getPressure():
        value = "{0:0.2f}".format(sensor.read_pressure()/100) # Pa to hPa
		
        return value


```
**2.**  The captured values are then sent to `api.php` script via a **POST** request along with a user-defined **API Key**. This process continuously repeats itself with a 10 minute delay inside of a loop.
```python
while True:
    pressure = getPressure()

    request = requests.post('http://127.0.0.1/iot/api.php?api_key=' + apiKey + "&hodnota=" + pressure)
    response = request.text
    print("\nServer response: " + response + "\nPressure sent: " + pressure +  " hPa")

time.sleep(10*60) #sleep for 10 minutes
```
**3.** Next, the `api.php` processes the request and proceeds to store the data in the database if the **API Key** matches.
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_REQUEST['api_key'] == $API_KEY) {
        if (isset($_REQUEST['hodnota'])) {
            $statement = $connect->prepare("INSERT INTO mereni (hodnota,time) VALUES (?, ?)");
            $statement->bind_param("ii", $_REQUEST['hodnota'], time());
            if ($statement->execute()) {
                die("OK");
            } else {
                die("Failed");
            }
```
**4.** Everything is then processed via PHP and displayed on our fronted page.



## Installation and requirements
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

**Setting up the BMP180 sensor:**

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

##Credits
- fritzing.com for the connection scheme
- http://github.com/hrubymar10 for getting me the BMP180 sensors


