<?php
require('../assets/php/config.php');

if (!isset($_GET['token'])) {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: /');
}

$search = $controller->select('email_subscriptions', ['hash' => $_GET['token']]);

$emails = file_get_contents('../assets/data/email_subscriptions.dat');
$emails = explode("\n", $emails);

unset($emails[array_search($search[0], $emails)]);

$newEmails = implode("\n", $emails);
file_put_contents('../assets/data/email_subscriptions.dat', $newEmails);

header('HTTP/1.1 301 Moved Permanently');
header('Location: /?unsubscribed');