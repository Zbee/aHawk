import argparse
import os
import shutil
import sys
import time
import urllib.error
import urllib.parse
import urllib.request

import simplejson

# Variables for later
startTime = str(time.strftime("%Y-%m-%dT%H%M"))
notFoundInRow = ""
totalNFs = 0
totalNewNFs = 0
debug = False
notFoundLimit = 250
doFrom = 1
doTo = 150000

# Setup arguments
parser = argparse.ArgumentParser(
    description="Downloads data on every item in WoW"
)
parser.add_argument(
    "--debug",
    help="If debug info should be shown (false)",
    nargs=1
)
parser.add_argument(
    "--not-found-limit",
    help="The number of 404's you can get in a row before quitting (250)",
    nargs=1
)
parser.add_argument(
    "--do-from",
    help="The WoW item ID to start at (1)",
    nargs=1
)
parser.add_argument(
    "--do-to",
    help="The WoW item ID to stop at (150000)",
    nargs=1
)

args = parser.parse_args()

if args.debug is not None: debug = True
if args.not_found_limit is not None: notFoundLimit = args.not_found_limit
if args.do_from is not None: doFrom = args.do_from
if args.do_to is not None: doTo = args.do_to


# Function to create and manage a progress bar
def update_progress(progress, status, notFounds, newNotFounds, notFoundInRow):
    barLength = 20
    if debug:
        status = status.split(":")[0]
        status = str(status)[:7].ljust(7, " ")
        notFounds = str(notFounds)[:6].ljust(6, " ")
        newNotFounds = str(newNotFounds)[:6].ljust(6, " ")
        notFoundInRow = str(notFoundInRow - 1)[:5].ljust(5, " ")
    else:
        status = str(status)[:30].ljust(30, " ")
    if isinstance(progress, int):
        progress = float(progress)
    if not isinstance(progress, float):
        progress = 0
        status = "error: progress var must be float\r\n"
    if progress < 0:
        progress = 0
        status = "Halt...\r\n"
    if progress >= 1:
        progress = 1
        status = "Done...\r\n"
    block = int(round(barLength * progress))
    bar = "#" * block + "-" * (barLength - block)
    percentage = "%.1f" % (progress * 100)
    if not debug:
        text = "\rPercent: [{0}] {1}% Doing: {2}".format(bar, percentage,
                                                         status)
    else:
        text = "\rPercent: [{0}] {1}% Doing: {2}, 404s: {3}, new 404s: {4}, "
        text += "404s in row: {5}"
        text = text.format(
            bar, percentage, status, notFounds, newNotFounds, notFoundInRow
        )
    sys.stdout.write(text)
    sys.stdout.flush()


# Getting Blizzard keys from php config file
config = open("/var/www/aHawk/assets/php/config.php", "r")
config = config.readlines()
blizzardID = config[11].split("'")[3]
blizzardSec = config[12].split("'")[3]

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

# Echoing settings for debug
if debug:
    print("Using Battle.net API ID:    " + blizzardID)
    print("Using Battle.net API Sec:   " + blizzardSec)
    print("Using Battle.net API Token: " + token)
    print("Using not found limit of:   " + str(notFoundLimit))
    print("Starting from:              " + str(doFrom))
    print("Ending at:                  " + str(doTo))
    print("Starting at:                " + startTime)
    print("\n")

# Setting up the timestamped files (so stuff isn't overwritten whilst running)
os.mkdir("../data/items" + startTime + "/")

# Going through each item
for x in range(doFrom, doTo + 1):
    # Make sure the 404 file exists
    if not os.path.isfile("404s.dat"):
        open("404s.dat", "a").close()
    status = str(x)
    # Check if the current item ID is marked as a 404
    notFounds = open("404s.dat", "r")
    notFounds = notFounds.read().split(",")
    # Skip this item ID if it has 404'd before
    if str(x) in notFounds:
        totalNFs += 1
        pass
    # If the item ID hasn't 404'd before
    else:
        # If the number of 404's in a row exceeds the limit
        if len(notFoundInRow[:-2].split(",")) >= notFoundLimit:
            # Stop the loop, because that means we're at the end of WoW items
            print(
                "\nReached the end of valid WoW items. (stopped at " +
                status + ")"
            )
            break
        # If we haven't exceeded that limit
        else:
            # Make the item ID request
            req = urllib.request.Request(
                "https://us.api.blizzard.com/wow/item/" + str(x)
                + "?locale=en_US&access_token=" + token
            )
            # open the data received from the website and read the json (data
            # worked)
            try:
                response = urllib.request.urlopen(req)
                data = simplejson.load(response)
                # Write the 404's in a row to a hard list, and reset them
                with open("404s.dat", "a") as nfFile:
                    if notFoundInRow[:-2].split(",")[0] not in notFounds:
                        nfFile.write(notFoundInRow)
                    notFoundInRow = ""
                # Note the name of the item
                status += ": " + data["name"]
                # Record the name and item ID
                with open(
                    "../data/items" + startTime + "/" + str(x) + ".dat", "w"
                ) as indFile:
                    indFile.write(data["name"])
            # If the data from the website cannot be read (404, primarily)
            except urllib.error.HTTPError as e:
                # If the problem was a 404 (meaning the item doesn't exist)
                if e.code == 404:
                    # Keep track of how many 404's have been found and how
                    # many are new
                    totalNFs += 1
                    totalNewNFs += 1
                    # If this item hasn't 404'd before
                    if str(x) not in notFounds:
                        # Record that this item 404'd in a soft list
                        notFoundInRow += status + ","
                pass
    # Always update the progress bar
    update_progress(
        float(x) / doTo,
        status,
        totalNFs,
        totalNewNFs,
        len(notFoundInRow[:-2].split(","))
    )

# If an old items directory and list exist
if os.path.isdir("../data/items"):
    oldSize = sum(
        os.path.getsize(f) for f in os.listdir("../data/items") if
        os.path.isfile(f)
    )
    newSize = sum(
        os.path.getsize(f) for f in os.listdir("../data/items" + startTime)
        if os.path.isfile(f)
    )
    # If the new list is bigger than the old one
    if newSize > oldSize - 1024:
        # Remove the old directory
        shutil.rmtree("../data/items")
        # Rename the new directory to replace the old one
        os.rename("../data/items" + startTime, "../data/items")
        print("\nNew item list has been created and replaced the old one.")
        print("You can find the new data in and ../data/items/")
    # If the new list is smaller
    else:
        print("\nNew item list was smaller than old list; both files retained.")
        print(
            "You can find the new data in and ../data/items" + startTime + "/")
# If both the old items directory and list did not exist
else:
    # Delete the old directory if it exists
    if os.path.isdir("../data/items"):
        shutil.rmtree("../data/items")
    # Rename the new directory to replace the old ONE
    os.rename("../data/items" + startTime, "../data/items")
    print("\nNew item list has been created.")
    print("You can find the new data in ../data/items/")
print("\nDone.")
