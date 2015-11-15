<?php
require_once('../../assets/php/config.php');

#Accounting for form fields
if (!isset($_POST['hash'])) 
  throw new Exception('Form was tampered with ' . __LINE__);

#Replace their data (without any scripts) if the form wasn't tampered with
$repForm = [];
foreach ($_POST as $key => $post)
  $repForm[$key] = strip_tags(str_replace('\'', '&#39;', $post));

#Can't let this be empty for sure
if ($_POST['hash'] == '')
  throw new Exception('Hash cannot be empty ' . __LINE__);

#Verifying that the hash matches the id
$search = $controller->select(
  'api_subscriptions',
  ['token' => $repForm['hash'], 'id' => $id]
);
if (!is_array($search) || count($search) !== 1)
  throw new Exception('Hash does not match your link ' . __LINE__);

#Set a cookie with the hash
setcookie('aHash', $repForm['hash'], time()+3600);
$error = '';
