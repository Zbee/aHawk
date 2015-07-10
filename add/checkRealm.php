<?php
require(__DIR__ . '/../assets/php/config.php');

$search = $controller->select('realms', ['name' => $_POST['realm']]);

echo json_encode(
  ['actualRealm' => (is_array($search) && count($search) === 1 ? true : false)]
);