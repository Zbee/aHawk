<?php
require(__DIR__ . '/../assets/php/config.php');

function wowCur(int $amount, $type = false, $pad = false, $nfat = false) {
  //Currencies
  $g = floor($amount / 1e4); //Gold
  $s = floor($amount / 100); while ($s >= 100) { $s = ($s >= 100) ? $s - 100 : $s; } //Silver
  $c = $amount; while ($c >= 100) { $c -= 100; } //Copper
  
  //Optional Padding
  if ($pad) {
    $g = sprintf("%02s", $g);
    $s = sprintf("%02s", $s);
    $c = sprintf("%02s", $c);
  }
  
  //Optional formatting
  if ($nfat === true)
    $g = number_format($g, 0);
  
  //Returning formatted result
  if ($type === "g")
    return "{$g}g";
  elseif ($type === "s")
    return "{$s}s";
  elseif ($type === "c")
    return "{$c}c";
  else {
    $a = "{$g}g {$s}s {$c}c";
    return $a;
  }
}

$bgColor = file_get_contents('../assets/css/style.styl', null, null, 95, 7);

$itemID = intval($_POST['item']);
$item = strip_tags(str_replace('\'', '&#39;', $_POST['itemName']));
$realm = strip_tags(str_replace('\'', '&#39;', $_POST['realm']));

$availability = $controller->availabilityOf($itemID, $realm);

if (is_string($availability)) {
  $availability = new stdClass;
  $availability->available = false;
  $availability->lowestPricePer = 0;
  $availability->quantity = [0, ''];
  $availability->owner = "Unknown";
  $availability->owns = 0;
}

echo '<div class="left">Available</div><div class="right">';
echo $availability->available === true ? "Yes" : "No";
echo '<span class="right">';
echo '<a href="/items?item=' . $itemID . '&realm=' . $realm . '">'
  . 'Link</a>, ';
echo '<a href="/sub?to=' . $itemID . '&realm=' . $realm . '">'
  . 'Subscribe</a>';
echo '</span></div><br>';

echo '<div class="left">Lowest Price Per Item</div><div class="right">';
echo wowCur($availability->lowestPricePer, false, true, true);
echo '</div><br>';

echo '<div class="left">Quantity Available</div><div class="right">';
echo $availability->quantity[0];
echo '</div><br>';

$empty = 0;
$chartData = '';
for ($i = 1; $i <= (60/checkEvery-1)*24; $i++) {
  $check = file_get_contents('../assets/data/checks/check' . $i . '.dat');
  $check = json_decode($check);
  if (isset($check->$realm->$itemID)) {
    $quantity = $check->$realm->$itemID->quantity[0];
  } else {
    $quantity = 0;
    $empty += 1;
  }
  $chartData .= ', ' . $quantity;
}
$chartData = substr($chartData, 2);

echo '<div class="left hidden-xs">Quantity Histogram</div><div class="right hidden-xs">';
echo '<div id="ch' . $itemID . '" style="height: 150px; margin: 0"></div>
<script>
$("#ch' . $itemID . '").width(((window.innerWidth*.4-80)*.95-8)*.74)

$("#ch' . $itemID . '").highcharts({
  chart: {
    backgroundColor: "' . $bgColor . '",
    type: "area"
  },
  colors: ["#734834"],
  title: {
    text: ""
  },
  legend: {
    enabled: false
  },
  xAxis: {
    allowDecimals: false,
    labels: {
      formatter: function () {
        return this.value * ' . checkEvery . ' + "min ago"
      }
    }
  },
  yAxis: {
    allowDecimals: false,
    title: {
      text: "Available"
    },
    labels: {
      formatter: function () {
        return this.value
      }
    }
  },
  tooltip: {
    pointFormat: "{point.y} {series.name} available"
  },
  plotOptions: {
    area: {
      pointStart: 1,
      marker: {
        enabled: false,
        symbol: "circle",
        radius: 2,
        states: {
          hover: {
            enabled: true
          }
        }
      }
    }
  },
  series: [{
    name: "' . $item . '",
    data: [' . $chartData . ']
  }]
})
</script>';
echo '</div><br class="hidden-xs">';

$ownerP = $availability->quantity[0] > 0
  ? number_format($availability->owns/$availability->quantity[0]*100, 0)
  : 0;
if ($availability->quantity[0] > 0) {
  echo '<div class="left">Controller</div><div class="right">';
  echo '<a href="https://theunderminejournal.com/#us/' . $availability->ownerRealm
    . '/seller/' . $availability->owner . '" target="_blank">'
    . $availability->owner . '</a> with ' . $ownerP . '% '
    . '<abbr title="Player who owns the most of this item that are for sale">?'
    . '</abbr></div>';
}

echo '<div class="left">Coverage</div><div class="right">';
echo (100-round($empty/72/100)*100) . '%'
  . '<abbr title="Percentage of checks today that included this item">?'
  . '</abbr></div>';

echo '<br>Find more info on ';
echo '<a href="https://theunderminejournal.com/#us/'
  . $realm . '/item/' . $itemID . '" target="_blank">The Undermine '
  . 'Journal</a> and ';
echo '<a href="http://www.wowhead.com/item='. $itemID . '"'
  . ' target="_blank">Wowhead</a>; ';
echo 'and, buy it on <a href="https://us.battle.net/wow/en/vault/'
  . 'character/auction/browse?itemId=' . $itemID . '"'
  . ' target="_blank">Battle.net</a>.';