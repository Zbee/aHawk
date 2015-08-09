<?php

if ($requires["subscription"]) {
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
}

if ($requires['realm']) {
  $realm = preg_replace('/[^\w\'\- ]+/i', '', urldecode($url[0]));
  if ($realm == '')
    throw new Exception(
      'realm not found in url (' . $urlO . ')'
    );
}

if ($requires['item']) {
  $item = intval($url[1]);
  if ($item == 0)
    throw new Exception(
      'item not found in url (' . $urlO . ')'
    );
}

if ($requires['time']) {
  $time = strtolower(preg_replace('/[^0-9a-zA-Z]+/i', '', explode('?', $url[2])[0]));
  if ($time == '')
    throw new Exception(
      'time not found in url (' . $urlO . ')'
    );
}

if ($requires["subscription"]) {
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
}

if ($requires['realm']) {
  $realmExist = $controller->select('realms', ['name' => $realm]);
  $realmExist = @count($realmExist) === 1 ? true : false;
  if (!$realmExist)
    throw new Exception(
      'realm not found (' . $realm . ')'
    );
}

if ($requires['item']) {
  $itemExist = file_exists('../assets/data/items/' . $item . '.dat');
  if (!$itemExist)
    throw new Exception(
      'item not found (' . $item . ')'
    );
}

if ($requires['time']) {
  $timeAllowed = $time == 'now' || $time == 'today'  || is_numeric($time)
    ? true
    : false;
  if (!$timeAllowed)
    throw new Exception(
      'time only accepts "now", "today", and integers (' . $time . ')'
    );
}