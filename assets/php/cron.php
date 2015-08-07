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