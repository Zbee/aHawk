<?php
require(__DIR__ . '/../assets/php/config.php');

$explodes = count(explode('/', website));
$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = $url = explode('/', $url);

for ($x = 0; $x < $explodes+1; $x++)
  array_shift($url);

$urlO = $url = implode('/', $url);

if (strpos($url,'.JSON') !== false) {} else {
  die(
    json_encode(
      [
        'status' => [
          404,
          'rss and subscription-based endpoints not yet supported ('
            . $urlO . ')'
        ]
      ]
    )
  );
}

$url = explode('.JSON', $url)[0];
$url = explode('/', $url);

$realm = preg_replace('/[^\w\'\- ]+/i', '', urldecode($url[0]));
if ($realm == '')
  die(
    json_encode(
      [
        'status' => [404, 'realm not found in url (' . $urlO . ')']
      ]
    )
  );

$item = intval($url[1]);
if ($item == 0)
  die(
    json_encode(
      [
        'status' => [404, 'item not found in url (' . $urlO . ')']
      ]
    )
  );

$realmExist = $controller->select('realms', ['name' => $realm]);
$realmExist = @count($realmExist) === 1 ? true : false;
if (!$realmExist)
  die(
    json_encode(
      [
        'status' => [404, 'realm not found (' . $realm . ')']
      ]
    )
  );

$itemExist = file_exists('../assets/data/items/' . $item . '.dat');
if (!$itemExist)
  die(
    json_encode(
      [
        'status' => [404, 'item not found (' . $item . ')']
      ]
    )
  );

$availability = $controller->availabilityOf($item, $realm);
if (!is_object($availability))
  die(
    json_encode(
      [
        'status' => [404, 'item not being tracked (' . $item . ')']
      ]
    )
  );

http_response_code(200);
die(
  json_encode(
    [
      'available' => $availability->available,
      'status' => [200, 'item availability found']
    ]
  )
);