<?php

$url = explode('availabilityOf/', $url)[1];
$url = explode('/', $url);

$args = explode('?', end($url));
if (count($args) === 1 || strtolower(explode('=', $args[1])[0]) !== 'token')
  throw new Exception(
    'this endpoint requires a token (' . $urlO . ')'
  );

$token = preg_replace('/[^a-z0-9]+/i', '', explode('=', $args[1])[1]);
if (strlen($token) !== 20)
  throw new Exception(
    'token provided is not valid (' . $token . ')'
  );

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

$time = strtolower(preg_replace('/[^0-9a-zA-Z]+/i', '', explode('?', $url[2])[0]));
if ($time == '')
  throw new Exception(
    'time not found in url (' . $urlO . ')'
  );

$tokenSearch = $tokenExist = $controller->select('api_subscriptions', ['token' => $token]);
$tokenExist = @count($tokenExist) === 1 ? true : false;
if (!$tokenExist)
  throw new Exception(
    'token not found (' . $token . ')'
  );

$fineIPs = explode('&', explode(',', $tokenSearch[0])[3]);
if (!in_array($_SERVER['REMOTE_ADDR'], $fineIPs) && !in_array('*', $fineIPs))
  throw new Exception(
    'this IP does not match this token (' . $_SERVER['REMOTE_ADDR'] . ')'
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

$timeAllowed = $time == 'now' || $time == 'today'  || is_numeric($time)
  ? true
  : false;
if (!$timeAllowed)
  throw new Exception(
    'time only accepts "now", "today", and integers (' . $time . ')'
  );

$availability = $controller->availabilityOf($item, $realm, $time, 'available');
if (!is_object($availability))
  throw new Exception(
    'item not being tracked (' . $item . ')'
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