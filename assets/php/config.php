<?php

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];

require_once(__DIR__ . '/controller.php');

#Your website, no trailing slash (<website>/assets/php/cron.pp should be valid)
define('website', 'http://example.com');

#Your blizzard key (https://dev.battle.net/)
define('blizzKey', '');

#Whether or not users can be notified by email
define('emailNotifications', true);

#Whether or not users can be notified using IFTTT's Maker
define('iftttNotifications', true);

#Whether or not the API can be accessed
define('apiAccess', true);

#How often you have '/assets/php/checkers.php' being run (in minutes)
define('checkEvery', 15);

$controller = new Controller;

if ($_SERVER["HTTP_X_FORWARDED_PROTO"] != "https"
  && json_decode($_SERVER["HTTP_CF_VISITOR"])->scheme != "https") {
  $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: $redirect");
}
