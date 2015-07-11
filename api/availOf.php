<?php

$url = explode('availabilityOf/', $url)[1];
$url = explode('/', $url);

$args = explode('?', end($url));
if (count($args) === 1 || strtolower(explode('=', $args[1])[0]) !== 'token')
  die(
    json_encode(
      [
        'status' => [404, 'this endpoint requires a token (' . $urlO . ')']
      ]
    )
  );

$token = preg_replace('/[^a-z0-9]+/i', '', explode('=', $args[1])[1]);
if (strlen($token) !== 20)
  die(
    json_encode(
      [
        'status' => [404, 'token provided is not valid (' . $token . ')']
      ]
    )
  );

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

$time = strtolower(preg_replace('/[^0-9a-zA-Z]+/i', '', explode('?', $url[2])[0]));
if ($time == '')
  die(
    json_encode(
      [
        'status' => [404, 'time not found in url (' . $urlO . ')']
      ]
    )
  );

$tokenSearch = $tokenExist = $controller->select('apis', ['token' => $token]);
$tokenExist = @count($tokenExist) === 1 ? true : false;
if (!$tokenExist)
  die(
    json_encode(
      [
        'status' => [404, 'token not found (' . $token . ')']
      ]
    )
  );

$fineIPs = explode('&', explode(',', $tokenSearch[0])[3]);
if (!in_array($_SERVER['REMOTE_ADDR'], $fineIPs) && !in_array('*', $fineIPs))
  die(
    json_encode(
      [
        'status' => [
          404,
          'this IP does not match this token (' . $_SERVER['REMOTE_ADDR'] . ')'
        ]
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

$timeAllowed = $time == 'now' || $time == 'today'  || is_numeric($time)
  ? true
  : false;
if (!$timeAllowed)
  die(
    json_encode(
      [
        'status' => [
          404,
          'time only accepts "now", "today", and integers (' . $time . ')'
        ]
      ]
    )
  );

$availability = $controller->availabilityOf($item, $realm, $time, 'available');
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
  'available' => $time == 'today'
    ? $availability->return
    : $availability->available
];

if (!is_array($echo['available'])) $echo['time'] = $availability->time;

echo json_encode($echo);