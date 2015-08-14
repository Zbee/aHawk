import urllib2, simplejson, sys, time, argparse, os, collections, re, operator
import subprocess, sys

#Variables for later
startTime = str(time.strftime("%Y-%m-%dT%H%M"))
debug = False
output = {}
byRealm = {}

#Setup arguments
parser = argparse.ArgumentParser(description="Downloads data on every item in WoW")
parser.add_argument("--debug", help="If debug info should be shown (false)", nargs=1)
args = parser.parse_args()

if args.debug is not None: debug = True

def letters(input):
  return re.sub(r"[^A-Za-z]+", '', input)

def stackDenotation (item, byStack):
  total = 0
  denotation = ""
  if item in byStack:
    ordered = collections.OrderedDict(
      sorted(
        byStack[item].items()
      )
    )
    for stack, amount in ordered.iteritems():
      total += int(stack)*int(amount)
      denotation += str(amount) + "x" + str(stack) + "+"
    return [total, denotation[:-1]]
  else:
    return [0, "0x0"]

#Getting blizzKey from php config file
config = open("../php/config.php", "r")
config = config.readlines()
blizzKey = config[11].split("'")[3]
checkEvery = int(config[23].split(", ")[1].split(")")[0])

#Echoing settings for debug
if debug:
  print "Using Battle.net API key: " + blizzKey
  print "Starting at:              " + startTime

checks = open("../data/checks.dat", "r")
checks = checks.readlines()
checks.pop(0)

for check in checks:
  check = check.split(",")
  if check[1] not in output:
    output[letters(check[1])] = {}
  if check[1] not in byRealm:
    byRealm[check[1]] = []
  byRealm[check[1]].append(int(check[2].split("\n")[0]))

for realm, items in byRealm.iteritems():
  req = urllib2.Request(
    "https://us.api.battle.net/wow/auction/data/" + realm.lower()
    + "?locale=en_US&apikey=" + blizzKey
  )
  opener = urllib2.build_opener()
  try:
    json = opener.open(req)
    data = simplejson.load(json)
    lMod = int(data["files"][0]["lastModified"]/1000)
    if debug:
      print "Doing realm:              " + realm
    req = urllib2.Request(data["files"][0]["url"])
    opener = urllib2.build_opener()
    try:
      json = opener.open(req)
      data = simplejson.load(json)
      auctions = data["auctions"]
      byStack = {}
      lowestPricePer = {}
      owner = {}
      ownerRealms = {}
      for auction in auctions:
        if int(auction["item"]) not in owner:
          owner[int(auction["item"])] = {}
        if auction["owner"] in owner[int(auction["item"])]:
          owner[int(auction["item"])][auction["owner"]] += int(auction["quantity"])
        else:
          owner[int(auction["item"])][auction["owner"]] = int(auction["quantity"])
        ownerRealms[auction["owner"]] = str(auction["ownerRealm"]).lower()
        ownerRealms[auction["owner"]] = ownerRealms[auction["owner"]].replace("'", "")
        ownerRealms[auction["owner"]] = ownerRealms[auction["owner"]].replace("-", "")
        ownerRealms[auction["owner"]] = ownerRealms[auction["owner"]].replace(" ", "-")
        if int(auction["item"]) in items:
          if int(auction["item"]) not in lowestPricePer:
            lowestPricePer[int(auction["item"])] = 1000000000000000000000000000
          if auction["buyout"] == 0:
            lpp = int(auction["bid"])
          else:
            lpp = int(auction["buyout"])
          lpp /= int(auction["quantity"])
          if lpp < lowestPricePer[int(auction["item"])]:
            lowestPricePer[int(auction["item"])] = lpp
          if int(auction["item"]) not in byStack:
            byStack[int(auction["item"])] = {}
          if int(auction["quantity"]) not in byStack[int(auction["item"])]:
            byStack[int(auction["item"])][int(auction["quantity"])] = 0
          byStack[int(auction["item"])][int(auction["quantity"])] += 1
      for item in items:
        qDenote = stackDenotation(int(item), byStack)
        available = False
        if qDenote[0] > 0:
          available = True
        if item not in lowestPricePer:
          lowestPricePer[item] = 0
        if item in owner:
          ownerO = sorted(owner[item].items(), key=operator.itemgetter(1))[-1]
        else:
          ownerO = ("", 0)
        output[realm][int(item)] = {
          "available": available,
          "lowestPricePer": lowestPricePer[item],
          "quantity": qDenote,
          "owner": ownerO[0],
          "ownerRealm": ownerRealms[ownerO[0]],
          "owns": ownerO[1]
        }
      output[realm]["time"] = lMod
    except urllib2.HTTPError as e:
      print e
  except urllib2.HTTPError as e:
    print e

for x in xrange((60/checkEvery-1)*24, 0, -1):
  if x == 1:
    if not os.path.isfile("../data/checks/check1.dat"):
      open("../data/checks/check1.dat", "a").close()
    with open("../data/checks/check1.dat", "w") as file:
      file.write(simplejson.dumps(output, separators=(',', ':'), sort_keys=True))
  if not os.path.isfile("../data/checks/check" + str(int(x)-1) + ".dat"):
    pass
  else:
    if not os.path.isfile("../data/checks/check" + str(x) + ".dat"):
      open("../data/checks/check" + str(x) + ".dat", "a").close()
    with open("../data/checks/check" + str(x) + ".dat", "w") as file:
      curFile = open("../data/checks/check" + str(int(x)-1) + ".dat", "r")
      file.write(curFile.read())

#Start the notification script
subprocess.Popen([sys.executable, "notify.py"])

if debug:
  print "Finished at:              " + str(time.strftime("%Y-%m-%dT%H%M"))