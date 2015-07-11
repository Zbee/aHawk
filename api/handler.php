<?php
require(__DIR__ . '/../assets/php/config.php');

$endpoint = '';

$explodes = count(explode('/', website));
$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = $url = explode('/', $url);

for ($x = 0; $x < $explodes+1; $x++)
  array_shift($url);

$urlO = $url = strip_tags(str_replace('\'', '&#39;', implode('/', $url)));

if (strpos($url, '.JSON') !== false) $endpoint = 'json';
if (strpos($url, 'availabilityOf/') !== false) $endpoint = 'availOf';
if (strpos($url, 'lowestPricePer/') !== false) $endpoint = 'lpp';
if (strpos($url, 'quantityOf/') !== false) $endpoint = 'quantOf';

if ($endpoint == '')
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

require $endpoint . '.php';