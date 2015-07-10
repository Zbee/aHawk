<?php

/*******************************************************************************
Setting up
*******************************************************************************/

require_once(__DIR__ . '/config.php');

$dataDir = explode('/', __DIR__);
unset($dataDir[array_search(end($dataDir), $dataDir)]);
$dataDir = implode('/', $dataDir);
$pyDir = $dataDir . '/py';
$dataDir .= '/data';

$return = [];

/*******************************************************************************
Realm updating (weekly)
*******************************************************************************/

$realmFile = $dataDir . '/realms.dat';

$jC = false;
if (!file_exists($realmFile)) {
  $ourFileHandle = fopen($realmFile, 'w')
    or die('can\'t create file');
  fclose($ourFileHandle);
  if (!file_exists($realmFile)) die('can\'t create file');
  $jC = true;
}

if ($jC || filemtime($realmFile) < time()-60*60*24*7) {
  $realmURL = 'https://us.api.battle.net/wow/realm/status?locale=en_US&apikey='
    . blizzKey;
  file_put_contents($realmFile, 'id,name');
  $realms = json_decode(file_get_contents($realmURL));
  $realms = $realms->realms;
  foreach ($realms as $id => $realm) {
    $add = "\n" . $id . ',' . $realm->name;
    file_put_contents(
      $realmFile,
      file_get_contents($realmFile) . $add
    );
  }
  $return['realms'] = 'updated';
} else {
  $return['realms'] = 'up to date';
}

/*******************************************************************************
Item list updating (weekly)
*******************************************************************************/

$items = $dataDir . '/items';

$jC = false;
if (!is_dir($items)) {
  $mkdir = mkdir($items)
    or die('can\'t create directory');
  if (!is_dir($items)) die('can\'t create file');
  $jC = true;
}

if ($jC || stat($items)['mtime'] < time()-60*60*24*7
  || stat($items)['size'] < 5000) {
  $command = 'python ' . $pyDir . '/itemListGenerator.py > /dev/null 2>/dev/null &';
  $result = exec($command);
  echo $result;
  $return['itemList'] = 'updating (~6hrs)';
} else {
  $return['itemList'] = 'up to date';
}

/*******************************************************************************
Availability / other data checking (`checkEvery` minutes)
*******************************************************************************/

$checks = $dataDir . '/checks';

$jC = false;
if (!is_dir($checks)) {
  $mkdir = mkdir($checks)
    or die('can\'t create directory');
  if (!is_dir($checks)) die('can\'t create file');
  $jC = true;
}

if ($jC || filemtime($checks . '/check1.dat') < time()-checkEvery*60) {
  $command = 'python ' . $pyDir . '/checkers.py > /dev/null 2>/dev/null &';
  $result = exec($command);
  echo $result;
  $return['checkList'] = 'updating (~10sec per realm)';
} else {
  $return['checkList'] = 'up to date';
}

/*******************************************************************************
Finishing up
*******************************************************************************/

echo json_encode($return);