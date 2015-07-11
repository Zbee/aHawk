<?php

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
header("Content-Type: application/json");

$echo = [
  'available' => $availability->available,
];

if (!is_array($echo['available'])) $echo['time'] = $availability->time;

echo json_encode($echo);