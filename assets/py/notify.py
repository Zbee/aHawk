import simplejson, urllib2, smtplib, os, sys
from email.mime.text import MIMEText

if (not os.path.isfile("/var/www/ahawk/assets/data/checks/check1.dat") or
  not os.path.isfile("/var/www/ahawk/assets/data/checks/check2.dat") or
  not os.path.isfile("/var/www/ahawk/assets/data/checks/check3.dat")):
  print "Not enough checks are present to notify anyone"
  sys.exit()

checks = open("/var/www/ahawk/assets/data/checks.dat", "r")
checks = checks.readlines()
checks.pop(0)

realms = {}

for check in checks:
  vals = check.split(",")
  realm = vals[1]
  item = int(vals[2])
  if realm not in realms:
    realms[realm] = {item: [False, False, False]}
  else:
    if item not in realms[realm]:
      realms[realm][item] = [False, False, False]

check1 = open("/var/www/ahawk/assets/data/checks/check1.dat", "r")
check1 = simplejson.load(check1)
check2 = open("/var/www/ahawk/assets/data/checks/check2.dat", "r")
check2 = simplejson.load(check2)
check3 = open("/var/www/ahawk/assets/data/checks/check3.dat", "r")
check3 = simplejson.load(check3)

for realm, items in realms.iteritems():
  for item in items:
    c1a = check1[realm][str(item)]["available"]
    c2a = check2[realm][str(item)]["available"]
    c3a = check3[realm][str(item)]["available"]

    prevAvail = True
    if c2a == False and c3a == False:
      prevAvail = False

    if c1a == True and prevAvail == False:
      realms[realm][item] = [
        True, False, check1[realm][str(item)]["quantity"][0]
      ]

subscriptions = open("/var/www/ahawk/assets/data/subscriptions.dat", "r")
subscriptions = subscriptions.readlines()
subscriptions.pop(0)

emailS = open("/var/www/ahawk/assets/data/email_subscriptions.dat", "r")
emailS = emailS.readlines()
emailS.pop(0)

emails = {}
for email in emailS:
  vals = email.split(",")
  emails[int(vals[0])] = [vals[1], vals[2].split("\r")[0].split("\n")[0]]

iftttS = open("/var/www/ahawk/assets/data/ifttt_subscriptions.dat", "r")
iftttS = iftttS.readlines()
iftttS.pop(0)

ifttts = {}
for ifttt in iftttS:
  vals = ifttt.split(",")
  ifttts[int(vals[0])] = [vals[1], vals[2].split("\r")[0].split("\n")[0]]

for check in checks:
  vals = check.split(",")
  checkId = int(vals[0])
  realm = vals[1]
  item = int(vals[2])
  if realms[realm][item][0] == True:
    for sub in subscriptions:
      vals = sub.split(",")
      method = int(vals[2])
      if method != 3:
        check = int(vals[1])
        if checkId == check:
          link = int(vals[3])
          itemName = open("/var/www/ahawk/assets/data/items/" + str(item) + ".dat", "r")
          itemName = itemName.readlines()[0]
          if method == 1:
            email = emails[link][0]
            msg = MIMEText(
              "Hello,\nCurrently there are " + str(realms[realm][item][2])
                + " " + itemName + " available on " + realm + ".\nYou can get "
                + "more information about this item here: https://ahawk.zbee.me"
                + "/items?item=" + str(item) + "&realm=" + realm + "\n\nYou "
                + "have received this email because you, or someone pretending "
                + "to be you, signed this email address up for notifications "
                + "on aHawk.\nIf this was not you, then you can unsubscribe "
                + "using this link: https://ahawk.zbee.me/sub/unsubscribe.php?"
                + "token=" + emails[link][1] + "\nWe apologize for any "
                + "inconvenience."
            )
            msg['Subject'] = itemName + " is now available on WoW"
            msg['From'] = "noreply@ahawk.zbee.me"
            msg['To'] = email
            s = smtplib.SMTP('localhost')
            s.sendmail("noreply@ahawk.zbee.me", [email], msg.as_string())
            s.quit()
          elif method == 2:
            data = {
              "value1": itemName,
              "value2": realms[realm][item][2],
              "value3": "https://us.battle.net/wow/en/vault/character/auction"
                + "/browse?itemId=" + str(item)
            }

            req = urllib2.Request(
              "https://maker.ifttt.com/trigger/" + ifttts[link][0]
                + "/with/key/" + ifttts[link][1]
            )
            req.add_header('Content-Type', 'application/json')
            response = urllib2.urlopen(req, simplejson.dumps(data))