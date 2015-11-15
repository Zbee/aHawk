<?php
require(__DIR__ . '/../../assets/php/config.php');

#Require identifier in url
if (!isset($_GET['ident']))
  throw new Exception('You must follow the link in your email ' . __LINE__);

#Check provided identifier
$id = str_replace('api sub', '', base64_decode($_GET['ident']));
$search = $controller->select('api_subscriptions', ['id' => $id]);
var_dump(!is_array($search));
var_dump(count($search) !== 1);
var_dump(!is_array($search) && count($search) === 1);
if (!is_array($search) && count($search) === 1)
  throw new Exception('Not a valid link for IP Management ' . __LINE__);
?>

<!DOCTYPE html>
<html>
  <header>
    <meta charset="UTF-8">

    <meta name="author" content="Zbee">
    <meta name="description"
      content="A World of Warcraft Auction House availability checker">

    <title>aHawk :: API</title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />

    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <link rel='stylesheet' href='/assets/css/style.css' />
    <script src='/assets/js/jquery.js'></script>
  </header>

  <body>

    <div id='main'>
      <h1><a href="/">aHawk</a> :: <a href="/api/">API</a> :: IP Management</h1>
      API IP Management is for subscribers to items via the the API notification
      option to control access to the API using their API hash.
      <br><br>
      Please enter your your API hash to gain access to your management screen.

      <br><br>

      <form action='' method='post'>
        <input type='text' name='hash'>

        <br>

        <b id='submit'>
          [
          <input type='submit' value='Manage IPs'>
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
    
    <img src='/assets/img/side.png' id='hawk'>

    <script>

    $(".endpoint").click(function() {
      console.log($(this).attr("toggle"))
      $("#" + $(this).attr("toggle")).toggle()
    })

    </script>

    <script src='checkForm.js'></script>

  </body>
</html>