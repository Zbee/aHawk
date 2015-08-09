<?php require(__DIR__ . '/../assets/php/config.php'); ?>

<!DOCTYPE html>
<html>
  <header>
    <title>aHawk :: Tracked items</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />

    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <link rel='stylesheet' href='/assets/css/style.css' />
    <script src='/assets/js/jquery.js'></script>
    <script src="/assets/js/highcharts.js"></script>
  </header>

  <body>

    <div id='main'>
      <h1><a href='/'>aHawk</a> :: Tracked Items</h1>
      This is a list of all of the realms with tracked items and information
      about those items.
      <br><br>
      <b>
        [
        <a href='/add/'>Add an item</a>
        ]
      </b>
      <br>
      Last check:
      <?=$l=intval((time()-filemtime('../assets/data/checks/check1.dat'))/60)?>
      min ago; Next check:
      <?=checkEvery-$l?> min from now.
      <br><br>
      <div style='text-align:left'>
        <?php
        $checks = $controller->select('checks', '*');
        $aRealms = [];
        $realms = [];
        $items = [];
        foreach ($checks as $check) {
          $check = explode(',', $check);
          $realm = preg_replace('/[^\w\'\- ]+/i', '', $check[1]);
          $realmS = preg_replace('/[^a-zA-Z0-9]+/i', '', $check[1]);
          $aRealms[$realmS] = $realm;
          $item = intval($check[2]);
          $itemName = file_get_contents(
            '../assets/data/items/' . $item . '.dat'
          );
          $items[$item] = $itemName;
          $itemName = preg_replace('/[^a-zA-Z0-9]+/i', '', $itemName);
          if (!isset($realms[$realm]))
            $realms[$realm] = [];
          $realms[$realm][$itemName] = $item;
        }
        ksort($realms);
        foreach ($realms as $realm => $checks) {
          ksort($checks);
          $realmS = $realm;
          $realm = $aRealms[$realm];
          $firstOfRealm = true;
          foreach ($checks as $key => $itemID) {
            if ($firstOfRealm) {
              $availability = $controller->availabilityOf($itemID, $realm);
              if (is_string($availability)) {
                $availability = new stdClass;
                $availability->time = 0;
              }
              $realmAgo = intval((time()-$availability->time)/60);
              $realmDiff = true;
              if ($realmAgo >= checkEvery) {
                $oldAvailability = $controller->availabilityOf(
                  $itemID, $realm, checkEvery
                );
                if (is_string($oldAvailability)) {
                  $oldAvailability = new stdClass;
                  $oldAvailability->time = 0;
                }
                if ($availability->time <= $oldAvailability->time)
                  $realmDiff = false;
              }
              echo '<a class="endpoint" toggle="' . $realmS . '"> ' . $realm
                . ' </a> Realm snapshot: <abbr title="This was the most '
                . 'recent snapshot available, new ones aren\'t always availabe '
                . 'every ' . checkEvery . ' minutes">' . $realmAgo
                . ' minutes old</abbr> ('
                . ($realmDiff ? 'different from' : 'same as')
                . ' last check)<div style="display:none;margin-left:15px" id="' . $realmS . '">';
              $firstOfRealm = false;
            }
            $item = $items[$itemID];
            echo '<span class="endpoint" toggle="' . $itemID . '" item="'
              . $item . '" realm="' . $realm . '" realmS="' . $realmS . '"> '
              . $item . ' </span><br>';

            $availability = $controller->availabilityOf($itemID, $realm);
            echo '<div class="info" id="' . $itemID . '">Loading...</div>';
            if (end($checks) === $itemID) echo '</div><br>';
          }
        }
        ?>
      </div>
      <br>
      <span class='muted'>
        Made with &lt;3 by <a href='https://keybase.io/zbee'>Zbee</a>,
        open source <a href='https://github.com/Zbee/aHawk'>GitHub</a>,
        no affiliation whatsoever with
        <a href='https://blizzard.com'>Blizzard</a>
      </span>
    </div>
    
    <img src='/assets/img/side.png' id='hawk'>

    <script>

    $(".endpoint").click(function() {
      $("#" + $(this).attr("toggle")).toggle()
      if ($("#" + $(this).attr("toggle")).text() == "Loading...") {
        var item = $(this).attr("toggle")
        var itemName = $(this).attr("item")
        var realm = $(this).attr("realm")
        $.ajax({
          type: "POST",
          url: "display.php",
          data: {item: item, itemName: itemName, realm: realm},
          dataType: "json",
          context: document.body,
          async: true,
          complete: function(res, stato) {
            $("#" + item).html(res.responseText)
          }
        })
      }
    })

    var url = window.location.href
    if (url.indexOf("realm=") > -1 && url.indexOf("item=") > -1) {
      var realm = url.split("realm=")[1]
      if (realm.indexOf("&") > -1) realm = realm.split("&")[0]
      if (realm.indexOf("/") > -1) realm = realm.split("/")[0]
      realm = realm.replace(/[^\w\'\- ]+/i, "")
      $("#" + realm).show()
      var item = url.split("item=")[1]
      if (item.indexOf("&") > -1) item = item.split("&")[0]
      if (item.indexOf("/") > -1) item = item.split("/")[0]
      item = parseInt(item)
      $("span[toggle='" + item + "'][realmS='" + realm + "']").click()
      $('html, body').animate({
        scrollTop: $("span[toggle='" + item + "'][realmS='" + realm + "']").offset().top
      }, 1500);
    }

    </script>

  </body>
</html>