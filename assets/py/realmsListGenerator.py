import urllib2, simplejson, sys, time, codecs, argparse, os

#Variables for later
startTime = str(time.strftime("%Y-%m-%dT%H%M"))

#Getting blizzKey from php config file
blizzKey = open("../php/config.php", "r")
blizzKey = blizzKey.readlines()
blizzKey = blizzKey[11].split("'")[3]
print "Using Battle.net API key: " + blizzKey

#Setting up the timestamped files (so stuff isn't overwritten whilst running)
with open("../data/realms" + startTime + ".dat", "a") as myfile:
  myfile.write("id,name")
  req = urllib2.Request(
    "https://us.api.battle.net/wow/realm/status?locale=en_US&apikey="
    + blizzKey
  )
  opener = urllib2.build_opener()
  #try reading the realms list
  try:
    json = opener.open(req)
    data = simplejson.load(json)
    realms = data["realms"]
    for id, realm in enumerate(realms):
      myfile.write("\n" + str(id) + "," + realm["name"])
  #fail for 404s
  except urllib2.HTTPError as e:
    print "Realm status API endpoint could not be reached, check API key used."
    pass

#rename new realms list to replace the old one
os.rename("../data/realms" + startTime + ".dat", "../data/realms.dat")
print "Realms list has been updated."