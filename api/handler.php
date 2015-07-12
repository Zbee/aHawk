<?php
require(__DIR__ . '/../assets/php/config.php');

$got = 'data';
$endpoint = '';

$explodes = count(explode('/', website));
$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = $url = explode('/', $url);

for ($x = 0; $x < $explodes+1; $x++)
  array_shift($url);

$urlO = $url = strip_tags(str_replace('\'', '&#39;', implode('/', $url)));

if (strpos($url, '.JSON') !== false) $endpoint = 'json';
if (strpos($url, 'availabilityOf/') !== false) $endpoint = 'availabilityOf';
if (strpos($url, 'lowestPricePer/') !== false) $endpoint = 'lowestPricePer';
if (strpos($url, 'quantityOf/') !== false) $endpoint = 'quantityOf';

try {
  if ($endpoint == '')
    throw new Exception(
      'rss endpoint not yet supported (' . $urlO . ')'
    );

  require $endpoint . '.php';
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
  $got = 'error';
}

require 'reporter.php';