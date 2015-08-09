#aHawk
A World of Warcraft auction house availability checker with an API and 
notifications that works off of a dynamic list of items and realms.

aHawk only checks on items on realms that users have specifically requested and 
only keeps a day's worth of checks.

Additionally, aHawk doesn't rely on a make-once-distribute-forever type of idea 
for the list of realms and items; instead it periodically checks for updates to 
these lists and, because of this it supports WoW patches with little delay and 
zero involvement.

##Why
aHawk (should always be typed `aHawk`, should always be said `a hawk`, derives 
from `Auction Hawker`) was built when I was playing WoW and had to buy some 
profession reagents but I could only get other such sites to update me on the 
reagents' availability hourly (by writing a little program, they have no 
notification options), so I needed something that could update me more often 
and that would be easier to add additional item monitors too.

##Notifications
Notifications are issued when an item is available in a check and it wasn't in 
the last two checks.

- **Email**
  - Emails are sent when the item is available
- **IFTTT**
  - Triggers [IFTTT Maker](http://ifttt.com/maker) recipes when the item is 
  available
- **API**
  - Gives user token and link to add permitted IP addresses, using the token 
  they can use the API; triggers nothing
- **RSS**
  - RSS feed with the current availability of the item
  - *note* /api/`realm name`/`item id`.rss
  - *note* not a subscription option; return the same as /availabilityOf/ but 
  only allows now; can be used for RSS displays or triggering IFTTT RSS recipes
- **JSON**
  - JSON encoded current availability of the item
  - *note* /api/`realm name`/`item id`.json
  - *note* not a subscription option; return the same as /availabilityOf/ but 
  only allows now; can be used in a custom application

##Setup
1. Have Python (2.7 tested), PHP (5.5 tested), the ability to make cron jobs, 
and a way to compile [Stylus](https://learnboost.github.io/stylus).
2. Get a [Battle.net API key](https://dev.battle.net/member/register).
3. Configure `aHawk/assets/php/config.php`.
4. Put the aHawk source onto your server.
5. Add `aHawk/assets/php/cron.php` to your cron jobs, running it between every 
1min and `checkEvery`min (cron.php will only perform checks as often as 
`checkEvery` minutes anyways).
6. Run the list generators in `aHawk/assets/py` (realms and items).

##Attribution
aHawk was made in 2015 by Ethan Henderson (Zbee) &lt;ethan@zbee.me>

[Blizzard](https://blizzard.com) owns everything to do with the 
[Battle.net API](https://dev.battle.net) and 
[World of Warcraft (WoW)](https://battle.net/wow), and this is in no way 
affiliated with them.

[Hawk image](assets/img/side.png) created by [/wg/](https://4chan.org/wg/) in 
[an IMT](https://archive.nyafuu.org/wg/thread/6244564/#6245756).

This is public domain, I don't care what you do with it (though if you use it, 
it'd be great if you'd link to this repo); but, I do have an instance running at 
[ahawk.zbee.me](https://ahawk.zbee.me).

##Current Progress
- [X] Tracking items
  - [X] Add items
  - [X] Check on items
  - [X] Indicate coverage of an item (100%=tracking all day or longer, less%=
  started tracking sometime in last 24 hours)
  - [X] Determine player with largest share of an item
- [ ] Notifications
  - [X] Subscribe to items
  - [ ] Notifications from subscriptions
  - [ ] Cancel old subscriptions
  - [ ] Cancel checks with no subscriptions
- [X] See tracked items
- [ ] API
  - [X] API JSON endpoint
  - [ ] API RSS endpoint
  - [X] API tokens
  - [ ] API IP management
  - [X] API /availabilityOf/ endpoint
  - [X] API /quantityOf/ endpoint
  - [X] API /lowestPricePer/ endpoint
  - [ ] API /everythingAbout/ endpoint
  - [ ] API rate-limiting
  - [ ] API endpoint for entire realm's items
  - [ ] API endpoint for item across all realms
  - [ ] API endpoint for owner of item
- [ ] Security
  - [X] Require HTTPS
  - [ ] Access to page support scripts
  - [ ] Access to Python
  - [ ] Access to PHP
  - [ ] Access to Data
  - [ ] Access to Logs
  - [ ] Bots on non-api pages
  - [ ] reCAPTCHA on forms
  - [ ] Backups (hourly, kept for 2 days?)
  - [ ] Determine bare-minimum permissions for entire thing to work, minimize
- [ ] Optimization
  - [ ] Caching
  - [ ] Server generating static files
  - [ ] Compressing static files
  - [ ] Storing old static check files
- [ ] Configuration
  - [ ] Make system care about config
  - [ ] Make as much as possible into options
- [ ] Statistics
  - [ ] Items added
  - [ ] Notifications given
  - [ ] API usage
  - [ ] Subscription cancelling
    - [ ] Manual
    - [ ] Auto
  - [ ] Page loads
  - [ ] Data transfer
- [ ] Other
  - [ ] Help pages on the site
  - [ ] Wiki documenting system
  - [ ] Configuration for monetization
  - [ ] Configuration for user accounts instead of anonymous subscriptions
  - [ ] WoW Addon that notifies you in-game when an item is available
  - [ ] Recreate style that capitalizes on the hawk, is night-friendly, and 
  minimzes clicks and searching
- [ ] Final
  - [ ] Make sure everything is standardized
  - [ ] Write tests
