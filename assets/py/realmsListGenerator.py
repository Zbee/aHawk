import os
import time
import urllib.error
import urllib.parse
import urllib.request

import simplejson

# Variables for later
startTime = str(time.strftime("%Y-%m-%dT%H%M"))

# Getting Blizzard keys from php config file
config = open("/var/www/aHawk/assets/php/config.php", "r")
config = config.readlines()
blizzardID = config[11].split("'")[3]
blizzardSec = config[12].split("'")[3]
print("Battle.net API ID:   " + blizzardID)
print("Battle.net API SEC:  " + blizzardSec)

# Getting an OAuth token
data = {
    "client_id"    : blizzardID,
    "client_secret": blizzardSec,
    "grant_type"   : "client_credentials"
}
data = urllib.parse.urlencode(data).encode('ascii')
req = urllib.request.Request("https://us.battle.net/oauth/token", data)
response = urllib.request.urlopen(req)
json = simplejson.load(response)
token = json['access_token']

# Setting up the timestamped files (so stuff isn't overwritten whilst running)
with open("../data/realms" + startTime + ".dat", "a") as myfile:
    myfile.write("id,name")
    req = urllib.request.Request(
        "https://us.api.blizzard.com/wow/realm/status?locale=en_US"
        + "&access_token=" + token
    )
    # try reading the realms list
    try:
        response = urllib.request.urlopen(req)
        data = simplejson.load(response)
        realms = data["realms"]
        for id, realm in enumerate(realms):
            myfile.write("\n" + str(id) + "," + realm["name"])
    # fail for 404s
    except urllib.error.HTTPError as e:
        print(
            "Realm status API endpoint could not be reached, check API key "
            "used.")
        pass

# rename new realms list to replace the old one
os.rename("../data/realms" + startTime + ".dat", "../data/realms.dat")
print("Realms list has been updated.")
