<?php

class controller {

  #$controller->select('subscriptions', ['email'=>'bob@ex.com', 'check'=>18])
  #Select everything from the row in subscriptions where the email is bob@ex.com
  # and the subscription is for check #18
  public function select ($table, $search, $debug = false) {
    if (!is_array($search) && $search != '*') return '$search must be an array';

    $data = explode('/', __DIR__);
    array_pop($data);
    $data = implode('/', $data);
    $data .= '/data';
    $data = $data . '/' . strtolower($table) . '.dat';

    if (!file_exists($data)) return 'no such table';

    $data = file_get_contents($data);
    $data = explode("\n", $data);
    array_pop($data);

    if (count($data) < 2) return 'there is no data in that table';

    $cols = explode(',', $data[0]);
    array_shift($data);

    if (is_array($search))
      foreach ($search as $col => $match)
        if (!in_array($col, $cols, true))
          return '$search key not a column in table';

    $return = [];

    if (is_array($search)) {
      #if ($debug) var_dump($search);
      foreach ($data as $row) {
        $cells = explode(',', $row);
        $yes = false;
        foreach ($search as $col => $match) {
          if ($debug) echo '<br>' . $col . '=>' . $match ;
          if (!in_array($match, $cells, true)) {
            $yes = false;
            if ($debug) echo $yes == true ? ' true' : ' false';
            continue;
          }
          if (array_search($match, $cells) === array_search($col, $cols))
            $yes = true;
          if ($debug) echo $yes == true ? ' true' : ' false';
        }
        if ($yes)
          array_push($return, $row);
      }
    } else
      foreach ($data as $row)
        array_push($return, $row);

    return $return;
  }

  #$controller->curID('subscriptions')
  #Returns what the latest ID of the subscriptions table is
  public function curID ($table) {
    $data = explode('/', __DIR__);
    array_pop($data);
    $data = implode('/', $data);
    $data .= '/data';
    $data = $data . '/' . strtolower($table) . '.dat';

    if (!file_exists($data)) return 'no such table';

    $curID = file_get_contents($data, null, null, -512, 512);
    $curID = end(explode("\n", $curID));
    $curID = intval(explode(',', $curID)[0]);

    return $curID;
  }

  #$controller->insert('subscriptions', ['email'=>'bob@ex.com', 'check'=>18])
  #Inserts a subscription where the email is bob@ex.com, and the check is #18
  public function insert ($table, $insert) {
    if (!is_array($insert)) return '$insert must be an array';

    $data = explode('/', __DIR__);
    array_pop($data);
    $data = implode('/', $data);
    $data .= '/data';
    $data = $data . '/' . strtolower($table) . '.dat';

    if (!file_exists($data)) return 'no such table';

    $cols = file_get_contents($data);
    $cols = explode("\n", $cols);
    $cols = explode(',', $cols[0]);
    array_shift($cols);

    foreach ($cols as $key => $col)
      $cols[$key] = trim($col);

    foreach ($insert as $col => $match)
      if (!in_array($col, $cols, true))
        return '$insert key not a column in table: ' . $col;

    $append = [];

    foreach ($cols as $col) {
      if (!isset($insert[$col]))
        return '$insert must have all the same keys as the table';
      $append[$col] = $insert[$col];
    }

    $curID = file_get_contents($data, null, null, -512, 512);
    $curID = explode("\n", $curID);
    array_pop($curID);
    $curID = explode(',', end($curID));
    $curID = intval($curID[0]);

    $append = $curID+1 . ',' . implode(',', $append) . "\n";

    file_put_contents($data, $append, FILE_APPEND | LOCK_EX);

    return $curID+1;
  }

  public function availabilityOf ($item, $realm) {
    $realm = $this->select('realms', ['name' => $realm]);

    if (count($realm) === 0)
      return '$realm is not an actual realm (case sensitive, no encoding)';

    $itemExists = file_exists(
      __DIR__ . '/../data/items/' . intval($item) . '.dat'
    );

    if (!$itemExists) return '$item is is not an actual item';

    $realm = preg_replace('/[^a-zA-Z\'\-_ ]+/i', '', $realm)[0];
    $item = $item + '';

    $check = file_get_contents(__DIR__ . '/../data/checks/check1.dat');
    $check = json_decode($check);
    
    if (isset($check->$realm->$item)) {
      return $check->$realm->$item;
    } else {
      return 'not being tracked';
    }
  }

}