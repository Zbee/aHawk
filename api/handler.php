<?php
require(__DIR__ . '/../assets/php/config.php');
header("Content-Type: application/json");

$got = 'data';
$endpoint = '';

$explodes = count(explode('/', website));
$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = $url = explode('/', $url);

for ($x = 0; $x < $explodes+1; $x++)
  array_shift($url);

$urlO = $url = strip_tags(str_replace('\'', '&#39;', implode('/', $url)));
$urL = strtolower($url);

$argValues = [
  'json' => null,
  'availabilityOf' => 'available',
  'lowestPricePer' => 'lowestPricePer',
  'quantityOf' => 'quantity'
];

$keys = [
  'json' => 'available',
  'availabilityOf' => 'available',
  'lowestPricePer' => 'lowestPricePer',
  'quantityOf' => 'quantity'
];

$requires = [
  "subscription" => true,
  "realm" => true,
  "item" => true,
  "time" => true
];

if (strpos($urL, '.json') !== false) {
  $endpoint = 'json';
  $requires["subscription"] = $requires['time'] = false;
}
if (strpos($urL, 'availabilityof/') !== false) $endpoint = 'availabilityOf';
if (strpos($urL, 'lowestpriceper/') !== false) $endpoint = 'lowestPricePer';
if (strpos($urL, 'quantityof/') !== false) $endpoint = 'quantityOf';

try {
  if ($endpoint == '')
    throw new Exception(
      'rss endpoint not yet supported (' . $urlO . ')'
    );

  if ($requires['subscription']) {
    $url = explode($endpoint . '/', $url)[1];
    $url = explode('/', $url);
  } else {
    $url = explode('.' . strtoupper($endpoint), $url)[0];
    $url = explode('/', $url);
  }

  $item = $realm = $time = $key = null;

  require 'checkRequired.php';

  $args = [$item, $realm, $time, $argValues[$endpoint]];
  $availability = $controller->availabilityOf(
    $args[0], $args[1], $args[2], $args[3]
  );
  if (!is_object($availability))
    throw new Exception(
      'item not being tracked (' . $item . ')'
    );

  http_response_code(200);

  $echo = [
    $keys[$endpoint] => $time == 'today'
      ? $availability->return
      : $availability->$keys[$endpoint]
  ];

  if ($endpoint === 'quantityOf') {
    if (count($echo['quantity']) == 2) $echo['time'] = $availability->time;
  } else {
    if (!is_array($echo[$keys[$endpoint]])) $echo['time'] = $availability->time;
  }

  echo json_encode($echo);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
  $got = 'error';
}

require 'reporter.php';