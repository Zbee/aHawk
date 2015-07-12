<?php

$url = explode('.JSON', $url)[0];
$url = explode('/', $url);

$realm = preg_replace('/[^\w\'\- ]+/i', '', urldecode($url[0]));
if ($realm == '')
  throw new Exception(
    'realm not found in url (' . $urlO . ')'
  );

$item = intval($url[1]);
if ($item == 0)
  throw new Exception(
    'item not found in url (' . $urlO . ')'
  );

$realmExist = $controller->select('realms', ['name' => $realm]);
$realmExist = @count($realmExist) === 1 ? true : false;
if (!$realmExist)
  throw new Exception(
    'realm not found (' . $realm . ')'
  );

$itemExist = file_exists('../assets/data/items/' . $item . '.dat');
if (!$itemExist)
  throw new Exception(
    'item not found (' . $item . ')'
  );

$availability = $controller->availabilityOf($item, $realm);
if (!is_object($availability))
  throw new Exception(
    'item not being tracked (' . $item . ')'
  );

http_response_code(200);
header("Content-Type: application/json");

$echo = [
  'available' => $availability->available,
];

if (!is_array($echo['available'])) $echo['time'] = $availability->time;

echo json_encode($echo);