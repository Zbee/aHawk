<?php

$fileExists = file_exists(
  __DIR__ . '/../assets/data/items/' . $_POST['item'] . '.dat'
);

echo json_encode(
  ['actualItem' => ($fileExists ? true : false)]
);