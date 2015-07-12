<?php

$parameters = '';
if (isset($realm)) $parameters .= 'realm:' . $realm . '|';
if (isset($item)) $parameters .= 'item:' . $item . '|';
if (isset($time)) $parameters .= 'time:' . $time . '|';
$parameters = substr($parameters, 0, -1);

$token = isset($token) ? $token : 'none';

$controller->insert(
  'apiaccess',
  [
    'endpoint' => $endpoint,
    'parameters' => $parameters,
    'got' => $got,
    'token' => $token,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'time' => time()
  ]
);