import serial
import requests
import time

PORT = "COM4"   # ton port Arduino
BAUDRATE = 9600

arduino = serial.Serial(PORT, BAUDRATE, timeout=1)
time.sleep(2)

print("Bridge lance...")

while True:
    ligne = arduino.readline().decode("utf-8", errors="ignore").strip()

    if ligne.startswith("BADGE:"):
        badge_uid = ligne.replace("BADGE:", "")
        print("Badge lu :", badge_uid)

        url = "http://localhost/SiteprojetEnzoV2/BadgeRFID.php"

        try:
            response = requests.get(url, params={"badge_uid": badge_uid})
            resultat = response.text.strip()

            print("Serveur :", resultat)

            if resultat.startswith("OK"):
                print("Envoi Arduino : OK")
                arduino.write(b"OK\n")
            else:
                print("Envoi Arduino : REFUS")
                arduino.write(b"REFUS\n")

        except Exception as e:
            print("Erreur :", e)
            arduino.write(b"ERREUR\n")