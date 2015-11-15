<?php
require_once('../assets/php/config.php');

#Accounting for form fields
if (!isset($_POST['itemID'])) 
  throw new Exception('Form was tampered with ' . __LINE__);
if (!isset($_POST['realm'])) 
  throw new Exception('Form was tampered with ' . __LINE__);
if (!isset($_POST['subEmail'])) 
  throw new Exception('Form was tampered with ' . __LINE__);
if (!isset($_POST['subIFTTTName'])) 
  throw new Exception('Form was tampered with ' . __LINE__);
if (!isset($_POST['subIFTTTKey'])) 
  throw new Exception('Form was tampered with ' . __LINE__);
if (!isset($_POST['subAPIEmail'])) 
  throw new Exception('Form was tampered with ' . __LINE__);

#Can't let either of these be empty for sure
if ($_POST['itemID'] == '') 
  throw new Exception('Item ID cannot be empty ' . __LINE__);
if ($_POST['realm'] == '') 
  throw new Exception('Realm name cannot be empty ' . __LINE__);

#There needs to be some sort of subscription
if (!isset($_POST['subEmailYes']) && !isset($_POST['subIFTTTYes']) 
  && !isset($_POST['subAPIYes']))
    throw new Exception('You must subscribe in some way ' . __LINE__);

#Can't let certain fields be empty if their respective subscription is ticked
if (isset($_POST['subEmailYes'])) {
  if ($_POST['subEmail'] == '')
    throw new Exception(
      'Email address cannot be empty for Email subscription ' .  __LINE__
    );
}
if (isset($_POST['subIFTTTYes'])) {
  if ($_POST['subIFTTTName'] == '')
    throw new Exception(
      'IFTTT Name cannot be empty for IFTTT subscription ' . __LINE__
    );
  if ($_POST['subIFTTTKeyF'] == '')
    throw new Exception(
      'IFTTT Key cannot be empty for IFTTT subscription ' . __LINE__
    );
}
if (isset($_POST['subAPIYes'])) {
  if ($_POST['subAPIEmail'] == '')
    
  throw new Exception(
    'Email address cannot be empty for API subscription ' . __LINE__
  );
}

#Verifying that the item ID matches an item
$item = intval($_POST['itemID']);
if ($item != $_POST['itemID'])
  throw new Exception('Only integers in item ID ' . __LINE__);
$fileExists = file_exists('../assets/data/items/' . $item . '.dat');
if (!$fileExists) throw new Exception('That is not a real item ' . __LINE__);
$itemName = file_get_contents('../assets/data/items/' . $item . '.dat');

#Verifying realm name
$realm = preg_replace('/^[^(\w\-\' )]+$/i', '', $_POST['realm']);
if ($realm != $_POST['realm']) 
  throw new Exception('Not a valid realm name ' . __LINE__);
$search = $controller->select('realms', ['name' => $realm]);
if (!is_array($search) && count($search) === 1) 
  throw new Exception('Not a realm ' . __LINE__);

#Verifying data on a per-subscription basis
if (isset($_POST['subEmailYes'])) {
  if (!filter_var($_POST['subEmail'], FILTER_VALIDATE_EMAIL))
    throw new Exception('This is not a valid email address' . __LINE__);
}
if (isset($_POST['subIFTTTYes'])) {
  $iftttName = preg_replace('/^[^(\w\-)]+$/i', '', $_POST['subIFTTTName']);
  if ($iftttName != $_POST['subIFTTTName'])
    throw new Exception('This is not a valid string ' . __LINE__);
  $iftttKey = preg_replace('/^[^(\w\-)]+$/i', '', $_POST['subIFTTTKey']);
  if ($iftttKey != $_POST['subIFTTTKey'])
    throw new Exception('This is not a valid string ' . __LINE__);
  $url = 'https://maker.ifttt.com/trigger/' . $iftttName . '/with/key/'
    . $iftttKey;
  $return = substr(
    file_get_contents($url),
    0,
    15
  );
  if ($return !== 'Congratulations')
    throw new Exception('IFTTT Name and Key could not be used ' . __LINE__);
}
if (isset($_POST['subAPIYes'])) {
  if (!filter_var($_POST['subAPIEmail'], FILTER_VALIDATE_EMAIL))
    throw new Exception('This is not a valid email address' . __LINE__);
}

#Record the check if it doesn't already exist
$search = $controller->select('checks', ['realm' => $realm, 'item' => $item]);
if (is_array($search) && count($search) === 1)
  throw new Exception('This item is already being tracked ' . __LINE__);
$controller->insert('checks', ['realm' => $realm, 'item' => $item]);
throw new Exception('This item is now being tracked');