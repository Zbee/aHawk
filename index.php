<?php require(__DIR__ . '/assets/php/config.php'); ?>

<!DOCTYPE html>
<html>
  <header>
    <meta charset="UTF-8">

    <meta name="author" content="Zbee">
    <meta name="description"
      content="A World of Warcraft Auction House availability checker">

    <title>aHawk</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />

    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <link rel='stylesheet' href='/assets/css/style.css' />
  </header>

  <body>

    <div id='main'>
      <h1>aHawk</h1>
      A World of Warcraft Auction House availability checker with an API and
      notifications that works off of a dynamic list of items and realms.
      <br><br>
      This only checks on items on realms that users have specifically requested
      and  only keeps a day's worth of checks.
      <br>
      So, if you add a new item then there will not immediately be data
      available.
      <br><br>
      <b>
        [
        <a href='/items/'>Items being checked on</a>,
        <a href='/add/'>Add an item</a>,
        <a href='/api/'>API</a>
        ]
      </b>
      <br>
      Last check
      <?=$l=intval((time()-filemtime('assets/data/checks/check1.dat'))/60)?>
      min ago; Next check: <?=checkEvery-$l?> min from now.
      <br><br>
      <span class='muted'>
        Made with &lt;3 by <a href='https://keybase.io/zbee'>Zbee</a>,
        open source <a href='https://github.com/Zbee/aHawk'>GitHub</a>,
        no affiliation whatsoever with
        <a href='https://blizzard.com'>Blizzard</a>
      </span>
    </div>

    <img src='/assets/img/side.png' id='hawk'>

  </body>
</html>