import serial
import requests
import time

# config du port série (doit correspondre au port de l'Arduino)
PORT     = "COM4"
BAUDRATE = 9600

# ouverture de la connexion série avec l'Arduino
arduino = serial.Serial(PORT, BAUDRATE, timeout=1)
time.sleep(2)  # on attent 2s le temps que l'Arduino démarre

print("Bridge lance...")

# boucle infinie : on écoute en permanence l'Arduino
while True:
    # lecture d'une ligne envoyée par l'Arduino
    ligne = arduino.readline().decode("utf-8", errors="ignore").strip()

    # si la ligne commence par "BADGE:" c'est un scan de badge RFID
    if ligne.startswith("BADGE:"):
        badge_uid = ligne.replace("BADGE:", "")
        print("Badge lu :", badge_uid)

        # URL du script PHP qui vérifie si le badge est autorisé
        url = "http://localhost/SiteprojetEnzoV2/BadgeRFID.php"

        try:
            # requête GET vers PHP avec le badge_uid en paramètre
            response = requests.get(url, params={"badge_uid": badge_uid})
            resultat = response.text.strip()

            print("Serveur :", resultat)

            # selon la réponse du serveur, on dit OK ou REFUS à l'Arduino
            if resultat.startswith("OK"):
                print("Envoi Arduino : OK")
                arduino.write(b"OK\n")      # badge reconnu, accès accordé
            else:
                print("Envoi Arduino : REFUS")
                arduino.write(b"REFUS\n")   # badge inconnu, accès refusé

        except Exception as e:
            # erreur réseau ou serveur PHP injoignable
            print("Erreur :", e)
            arduino.write(b"ERREUR\n")
