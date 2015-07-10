<?php
require(__DIR__ . '/../assets/php/config.php');
$itemID = '<u>Item ID:</u> (int) The ID of the item';
$time = '<u>Time:</u> (str or int) Accepts "now" (in the most recent check),
            "today" (results for all checks today), or minutes ago (rounds to
            check closest to the number of minutes ago) (int)';
$token = '<u>Token:</u> (str) The token given to you after subscribing,
            emailed to you, and available on your IP managing page';
$realm = '<u>Realm Name:</u> (str) The name of the realm, urlencoded, case 
            sensitive (Shu\'halo is still
            <a href=\'https://duckduckgo.com/?q=Shu%27halo+urlencode&ia=answer\'>
            Shu\'halo</a>, but Steamweedle Cartel is 
            <a href=\'https://duckduckgo.com/?q=Steamwheedle+Cartel+urlencode&ia=answer\'>
            Steamwheedle%20Cartel</a>)';
?>

<!DOCTYPE html>
<html>
  <header>
    <title>aHawk :: API</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />

    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <link rel='stylesheet' href='/assets/css/style.css' />
    <script src='/assets/js/jquery.js'></script>
  </header>

  <body>

    <div id='main'>
      <h1><a href="/">aHawk</a> :: API</h1>
      The API is several different endpoints which returns the data requested
      -or errors- in JSON.
      <br>
      Tokens are required for most endpoints so that API subscriptions can be
      cleaned.
      <h2>Public Endpoints</h2>
      These endpoints are publicly available, and require no authentication
      and will be accessible as long as the item has any subscribers.
      <br>
      It is not recommended that an application uses this, as it's possible for
      the item to lose it's subscribers and the application would break.
      <br><br>
      <div style='text-align:left'>
        <span class='endpoint' toggle='pubAvail'>
          GET <b>Availability JSON</b>
          <i>&lt;Realm Name>/&lt;Item ID>.JSON</i>
        </span> (works)
        <div class='info' id='pubAvail'>
          <div class='left'>Parameters</div>
          <div class='right'>
            <?=$realm?>
            <br>
            <?=$itemID?>
          </div>
          <br>
          <div class='left'>Requires</div>
          Nothing
          <br>
          <div class='left'>Example URL</div>
          <a href='<?=website?>/api/Eitrigg/21877.JSON'>
            /api/Eitrigg/21877.JSON
          </a>
          <br>
          <div class='left'>Example Response</div>
          <pre><?=file_get_contents(website . '/api/Eitrigg/21877.JSON')?></pre>
        </div>
        <br>
        <span class='endpoint' toggle='pubAvail1'>
          GET <b>Availability Feed</b>
          <i>&lt;Realm Name>/&lt;Item ID>.RSS</i>
        </span>
        <div class='info' id='pubAvail1'>
          <div class='left'>Parameters</div>
          <div class='right'>
            <?=$realm?>
            <br>
            <?=$itemID?>
          </div>
          <br>
          <div class='left'>Requires</div>
          Nothing
          <br>
          <div class='left'>Example URL</div>
          <a href='<?=website?>/api/Eitrigg/21877.RSS'>
            /api/Eitrigg/21877.RSS
          </a>
          <br>
          <div class='left'>Example Response</div>
          <pre><?=@file_get_contents(website . '/api/Eitrigg/21877.RSS')?></pre>
        </div>
      </div>
      <br>
      <hr>
      <h2 id="requiresSub">Subscription-based Endpoints</h2>
      These endpoints are not publicly available, and instead require
      authentication gained from subscribing to the item.
      <br>
      It is recommended that applications use these endpoints because even
      if the item loses all other subscribers, as long as the application is
      being used it will still work.
      <br><br>
      <div style='text-align:left'>
        <span class='endpoint' toggle='subAvail'>
          GET <b>/availabilityOf/</b>
          <i>availabilityOf/&lt;Realm Name>/&lt;Item ID>/&lt;Time></i>
        </span>
        <div class='info' id='subAvail'>
          <div class='left'>Parameters</div>
          <div class='right'>
            <?=$realm?>
            <br>
            <?=$itemID?>
            <br>
            <?=$time?>
          </div>
          <br>
          <div class='left'>Requires</div>
          <div class='right'>
            <?=$token?>
          </div>
          <br>
          <div class='left'>Example URL</div>
          <div class='right'>
            <a href='#requiresSub'>
              /api/availabilityOf/Eitrigg/21877/now?token=19mc849uas
            </a>
          </div>
          <br>
          <div class='left'>Example Response</div>
          <div class='right'>
            <pre>{'time': 120586, 'available': true}</pre>
          </div>
        </div>
        <br>
        <span class='endpoint' toggle='subLowest'>
          GET <b>/lowestPricePer/</b>
          <i>lowestPricePer/&lt;Realm Name>/&lt;Item ID>/&lt;Time></i>
        </span>
        <div class='info' id='subLowest'>
          <div class='left'>Parameters</div>
          <div class='right'>
            <?=$realm?>
            <br>
            <?=$itemID?>
            <br>
            <?=$time?>
          </div>
          <br>
          <div class='left'>Requires</div>
          <div class='right'>
            <?=$token?>
          </div>
          <br>
          <div class='left'>Example URL</div>
          <div class='right'>
            <a href='#requiresSub'>
              /api/lowestPricePer/Eitrigg/21877/now?token=19mc849uas
            </a>
          </div>
          <br>
          <div class='left'>Example Response</div>
          <div class='right'>
            <pre>{'time': 120586, 'available': true}</pre>
          </div>
        </div>
        <br>
        <span class='endpoint' toggle='subQuantity'>
          GET <b>/quantityOf/</b>
          <i>quantityOf/&lt;Realm Name>/&lt;Item ID>/&lt;Time></i>
        </span>
        <div class='info' id='subQuantity'>
          <div class='left'>Parameters</div>
          <div class='right'>
            <?=$realm?>
            <br>
            <?=$itemID?>
            <br>
            <?=$time?>
          </div>
          <br>
          <div class='left'>Requires</div>
          <div class='right'>
            <?=$token?>
          </div>
          <br>
          <div class='left'>Example URL</div>
          <div class='right'>
            <a href='#requiresSub'>
              /api/quantityOf/Eitrigg/21877/now?token=19mc849uas
            </a>
          </div>
          <br>
          <div class='left'>Example Response</div>
          <div class='right'>
            <pre>{'time': 120586, 'quantity': [18, "1x10+2x4"]}</pre>
          </div>
        </div>
      </div>
      <br><br>
      <span class='muted'>
        Made with &lt;3 by <a href='https://keybase.io/zbee'>Zbee</a>,
        open source <a href='https://github.com/Zbee/aHawk'>GitHub</a>,
        no affiliation whatsoever with
        <a href='https://blizzard.com'>Blizzard</a>
      </span>
    </div>

    <script>

    $(".endpoint").click(function() {
      console.log($(this).attr("toggle"))
      $("#" + $(this).attr("toggle")).toggle()
    })

    </script>

  </body>
</html>