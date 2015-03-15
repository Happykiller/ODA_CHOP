import sys
import os
import json
import urllib.request
from pprint import pprint
import datetime
from datetime import timedelta
import mysql.connector
import csv
import codecs

#append the relative location you want to import from
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "../../API/python/")))

#import your module stored in '../common'
import MyLib
import MyLogger

#Déclaration
arguments = dict() #dict
i = 0 #Int
config_json = {} #Json

#####################################################################
#Les definitions

#Procedure
def lunch() :
    global config_json
    try:
        MyLogger.logger.info("Début lunch")

        #Connection
        cnx = mysql.connector.connect(host=config_json["db_origine"]["host"], user=config_json["db_origine"]["log"], password=config_json["db_origine"]["mdp"], database=config_json["db_origine"]["db"])
        cur = cnx.cursor(buffered=True)
        
        MyLogger.logger.info("Fin lunch")
    except Exception as e:
        MyLogger.logger.error("Erreur pendant lunch : ("+format(e)+")")

#Procedure Say More
def more() :
    MyLogger.logger.info("Les options disponible sont : 'more','lunch'.")
    MyLogger.logger.info("Exemple de syntax pour 'lunch' : 'python ...\script.py ...\exemple.config.script.json lunch'.")
    MyLogger.logger.info("Exemple de syntax pour 'more' : '...\script.py more'.")  

#####################################################################
#Le programme

#Message de bienvenu.
MyLogger.logger.info ("Bienvenue dans le script.")

#Récupération des arguments.
for x in sys.argv :
    i += 1
    if i == 2 :
        arguments["jsonFile"] = x
    elif i == 3 :
        arguments["action"] = x
        if x not in ["lunch","more"] :
            MyLogger.logger.warning("Votre premier argument ("+x+") est incorrect, seul 'more','lunch' sont aurorisés.")
            sys.exit("Erreur")
        else :
            MyLogger.logger.info("Mode d'action choisi : "+x+".")
            arguments["action"] = x
            
    if len(arguments) == 0 :
        arguments["action"] = "more"

#Initialisation
config_json = MyLib.charger_config(arguments["jsonFile"])

#Message de bienvenue.
MyLogger.logger.info ("Chargement du json pour "+config_json["parameters"]["labelInstance"]+" reussi.")

#Affichage        
if arguments["action"] == "lunch" :
    lunch()
elif arguments["action"] == "more" :
    more()

#Message de fin.
MyLogger.logger.info ("Fin du script.")
sys.exit(0)

    
