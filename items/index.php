<?php require(__DIR__ . '/../assets/php/config.php'); ?>

<!DOCTYPE html>
<html>
  <header>
    <title>aHawk</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />

    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <link rel='stylesheet' href='/assets/css/style.css' />
    <script src='/assets/js/jquery.js'></script>
    <script src="/assets/js/highcharts.js"></script>
    <script src="/assets/js/exporting.js"></script>
  </header>

  <body>

    <div id='main'>
      <h1><a href='/'>aHawk</a> :: Tracked Items</h1>
      This is a list of all of the items being tracked, their availability,
      quantity, and lowest price per item.
      <br>
      Last check:
      <?=$l=intval((time()-filemtime('../assets/data/checks/check1.dat'))/60)?>
      min ago; Next check:
      <?=checkEvery-$l?> min from now.
      <br>
      <b>
        [
        <a href='/add/'>Add an item</a>
        ]
      </b>
      <br>
      <div style='text-align:left'>
        <?php
        $checks = $controller->select('checks', '*');
        foreach ($checks as $check) {
          $check = explode(',', $check);
          $realm = $check[1];
          $itemID = $item = intval($check[2]);
          $item = file_get_contents('../assets/data/items/' . $item . '.dat');
          echo '<span class="endpoint" toggle="' . $itemID . '" item="' . $item
            . '" realm="' . $realm . '">' . $realm . ' / <b>' . $item
            . '</b></span><br>';

          $availability = $controller->availabilityOf($itemID, $realm);
          echo '<div class="info" id="' . $itemID . '">Loading...</div>';
        }
        ?>
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

    </script>

  </body>
</html>