<?php
if (!isset($_GET['to']) || !isset($_GET['realm'])) {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: /');
}

require('../assets/php/config.php');

$realm = preg_replace('/[^\w\'\- ]+/i', '', $_GET['realm']);

$realmExists = $controller->select('realms', ['name' => $realm]);

$realmExists = count($realmExists) === 1 ? true : false;

if (!$realmExists) {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: /');
}

$item = intval($_GET['to']);

$itemExists = file_exists(
  __DIR__ . '/../assets/data/items/' . $item . '.dat'
);

if (!$itemExists) {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: /');
}

$itemName = file_get_contents(
  __DIR__ . '/../assets/data/items/' . $item . '.dat'
);

$error = '';

if (isset($_POST['subEmail'])) {
  try {
    require('processForm.php');
  } catch (Exception $e) {
    $error = '<div class="alert">' . $e->getMessage() .  ' </div><br>';
  }
}
?>

<!DOCTYPE html>
<html>
  <header>
    <title>aHawk :: Subscribe</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />

    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <link rel='stylesheet' href='/assets/css/style.css' />
    <script src='/assets/js/jquery.js'></script>
  </header>

  <body>

    <div id='main'>
      <h1><a href='/'>aHawk</a> :: Subscribe :: <?=$itemName?></h1>
      <?=$error?>
      Once you submit this form, you will be subscribed to a tracked item.
      <br>
      There are also 2 other methods to subscriptions to tracked items on the
      <a href='/api'>API page</a> (<a href='/api/<?=$realm?>/<?=$item?>.JSON'>JSON</a>,
      and <a href='/api/<?=$realm?>/<?=$item?>.RSS'>RSS</a>).
      <br><br>
      <form action='?to=<?=$item?>&realm=<?=$realm?>' method='post'>
        <abbr title='Must subscribe in some way'>Notification Subscription</abbr>
        <b id='subGood'></b>
        <br>
        You can choose one or several different ways to subscribe.
        <br>
        Any subscriptions that are not being used will be cancelled.
        <br>
        <label id='subEmail'>
          <input type='checkbox' name='subEmailYes'
            <?=isset($repForm['subEmailYes']) ? 'checked' : ''?>>
          <abbr title='Each email will have link to unsubscribe'>Email</abbr>
          <br>
          <abbr title='The email address you would like to receive emails at'>Address</abbr>
          <input type='email' name='subEmail'
            value='<?=isset($repForm) && isset($repForm['subEmailYes']) ? $repForm['subEmail'] : ''?>'>
          <b id='subEmailGood'></b>
          <div id='subEmailAlert'></div>
        </label>
        <br>
        <label id='subIFTTT'>
          <input type='checkbox' name='subIFTTTYes'
            <?=isset($repForm['subIFTTTYes']) ? 'checked' : ''?>>
          <abbr title='The values passed to IFTTT will be the name of the item, the quantity available, and then a link to the item on the auction house on battle.net, in that order. This will trigger the recipe to ensure it works.'>IFTTT</abbr>
          <br>
          <abbr title='You chose this when creating the IFTTT recipe you want us to trigger'>Maker Event Name</abbr>
          <input type='text' name='subIFTTTName'
            value='<?=isset($repForm) && isset($repForm['subIFTTTYes']) ? $repForm['subIFTTTName'] : ''?>'>
          <b id='subIFTTTNameGood'></b>
          <div id='subIFTTTNameAlert'></div>
          <br>
          <abbr title='Can be found at ifttt.com/maker'>Maker Secret Key</abbr>
          <input type='text' name='subIFTTTKey'
            value='<?=isset($repForm) && isset($repForm['subIFTTTYes']) ? $repForm['subIFTTTKey'] : ''?>'>
          <b id='subIFTTTKeyGood'></b>
          <div id='subIFTTTKeyAlert'></div>
        </label>
        <br>
        <label id='subAPI'>
          <input type='checkbox' name='subAPIYes'
            <?=isset($repForm['subAPIYes']) ? 'checked' : ''?>>
          <abbr title='You will be provided with a token'>API</abbr>
          <br>
          <abbr title='The email address you would like to receive a single email at with a link to add IPs that can use your API token; this will not be stored'>Address</abbr>
          <input type='email' name='subAPIEmail'
            value='<?=isset($repForm) && isset($repForm['subAPIYes']) ? $repForm['subAPIEmail'] : ''?>'>
          <b id='subAPIEmailGood'></b>
          <div id='subAPIEmailAlert'></div>
        </label>
        <div id='subAlert'></div>

        <br>

        <b id='submit'>
          [
          <input type='submit' value='Begin Subscription'>
          ]
        </b>
      </form>
      <br><br>
      <span class='muted'>
        Made with &lt;3 by <a href='https://keybase.io/zbee'>Zbee</a>,
        open source <a href='https://github.com/Zbee/aHawk'>GitHub</a>,
        no affiliation whatsoever with
        <a href='https://blizzard.com'>Blizzard</a>
      </span>
    </div>

    <script src='checkForm.js'></script>

  </body>
</html>