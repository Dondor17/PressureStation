# Pressure Station
 **Autor: Ondřej Dušek**
 **Třída: C3B**
 **Školní rok: 2017/2018**

##Obsah 

[TOC]
 
 ##Cíl projektu
 
- Cílem tohoto projektu bylo v daném časovém úseku vytvořit funkční stanici na měření atmosférického tlaku s následnou prezentací na web.

##Popis funkce
**Rozvržení**
- **Hardwarová část** se skládá z Raspberry Pi a senzoru BMP180 připojeného přes rozhraní I2C.
- **Softwarová část** se skládá z Python a PHP skriptů které mezi sebou spolupracují a umožňují získaná data uložit do databáze, která jsou nakonec zobrazena na webu.

**Funkčnost**

**1.** Python skript běžící na pozadí sbírá data ze senzoru BMP180 pomocí Python knihovny Adafruit_BMP085/BMP180. Funkce pro zachycení hodnot se nazývá `getPressure()`.
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
**2.**  Zachycené hodnoty jsou poté odeslány na `api.php` pomocí **POST** requestu společně s **API Klíčem**, který byl dříve nastavený uživatelem. Tento opakující se proces běží v loopu s 10 minutovým odstupem.
```python
while True:
    pressure = getPressure()

    request = requests.post('http://127.0.0.1/iot/api.php?api_key=' + apiKey + "&hodnota=" + pressure)
    response = request.text
    print("\nServer response: " + response + "\nPressure sent: " + pressure +  " hPa")

time.sleep(10*60) #sleep for 10 minutes
```
**3.** Dále skript `api.php` zpracuje náš požadavek a uloží data do naší databáze pouze pokud se odeslaný **API Klíč** shoduje s uživatelem definovaným **API Klíčem**, který se nachází souboru `config.php`.
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
**4.** Vše je nakonec zobrazeno a uspořádáno do grafu na hlavní stránce pomocí PHP.



## Instalace a požadavky
**Požadavky:**

- Senzor BMP180
- Raspberry Pi model B+ a novější
- Nepájivé pole
- Sada propojovacích drátků


**Projekt byl testován a vyvýjen v následující konfiguraci:**
-  Raspberry Pi 3 model B
- Raspbian Stretch Lite (minimal distro)
- Nginx 1.10.3
- PHP 7.0
- MySQL 5.6

**Instalace operačního systému Raspbian:**
- Stáhněte si image operačního systému Raspbian Stretch Lite (kde stretch je aktuální jméno verze) na https://www.raspberrypi.org/download/raspbian
- Stáhněte a nainstalujte **Win32 Disk Imager** (Windows) http://sourceforge.net/projects/win32diskimager
- Připojte zformátovanou micro SD kartu, zvolte stáhnutou image a klikňete na tlačítko **write**.
![](https://i.imgur.com/CtL5P3K.png)
- Vložte micro SD kartu zpět do vašeho Raspberry Pi a zařízení zapněte.

**Propojení senzoru BMP180 :**

![](https://i.imgur.com/WE52kQO.png)

**Instalace a konfigurace:**
- Po zapojení a instalaci Raspbianu zapněte rozhraní I2C pomocí `raspi-config`
- Otestujte svůj BMP180 senzor pomocí `i2cdetect -y 1`
- Nainstalujte následující balíčky
`sudo apt update`
`sudo apt install python-smbus`
`sudo apt install i2c-tools`
- Nainstalujte NGINX společně s PHP a MySQL pomocí 
`sudo apt install nginx, php7.0, mysql-server`
- Po konfiguraci přesuňtě projektové soubory do kořenového adresáře vašeho webserveru a změňte příslušné údaje v souboru `config.php`
- Změňte proměnnou `apiKey` v `main.py` tak, aby se shodovala s proměnnou `apiKey` v `config.php`
- Spusťte `python main.py`