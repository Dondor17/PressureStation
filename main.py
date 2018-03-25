#!/usr/bin/env python
# -*- coding: utf-8 -*-

import Adafruit_BMP.BMP085 as BMP085
import requests
import time
import random

sensor = BMP085.BMP085()
apiKey = ""

def getPressure():
        value = "{0:0.2f}".format(sensor.read_pressure()/100) # Pa to hPa
		
        return value

while True:
    pressure = getPressure()

    request = requests.post('http://127.0.0.1/iot/api.php?api_key=' + apiKey + "&hodnota=" + pressure)
    response = request.text
    print("\nServer response: " + response + "\nPressure sent: " + pressure +  " hPa")

    time.sleep(10*60) #sleep for 10 minutes
