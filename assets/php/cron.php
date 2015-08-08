<?php

/*******************************************************************************
Setting up
*******************************************************************************/

require_once(__DIR__ . '/config.php');

$force = false;
if (isset($_GET["force"])) $force = true;

$dataDir = "../data";

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

if ($force || $jC || filemtime($checks . '/check1.dat') < time()-checkEvery*60) {
  $command = escapeshellcmd('python ../py/checkers.py');
  $result = shell_exec($command);
  $return['checkList'] = 'updating';
} else {
  $return['checkList'] = 'up to date';
}

/*******************************************************************************
Finishing up
*******************************************************************************/

echo json_encode($return);