import os, subprocess, sys, argparse, time

#Setup arguments
parser = argparse.ArgumentParser(description="Manages downloading of data on every item in WoW")
parser.add_argument("--force", help="If data download should be forced (false)", nargs=1)
args = parser.parse_args()

force = False
if args.force is not None: force = True

#Getting checkEvery from php config file
config = open("../php/config.php", "r")
config = config.readlines()
checkEvery = int(config[23].split(", ")[1].split(")")[0])

#Set up the data/checks directory if the system is just starting
justMade = False
if not os.path.exists("../data/checks/"):
  os.makedirs("../data/checks")
  for x in xrange(1,(60/checkEvery-1)*24):
    open("check" + str(x) + ".dat", "a").close()
  justMade = True

#Getting age of last check
lastMod = os.stat("../data/checks/check1.dat").st_mtime

#If the last check is old, the user is forcing this, or the system just started
if lastMod < time.time()-checkEvery*60 or force or justMade:
  #Start the checking script
  subprocess.Popen([sys.executable, "checkers.py"])