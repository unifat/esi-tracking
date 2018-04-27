<?php
//set variables
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
//set variables
$page = "1";
$thistime = time();
$source_id = "10000002";

while (true) {
  $ins = [];
// make esi call to public order endpoint and get json data
$content = file_get_contents('https://esi.tech.ccp.is/latest/markets/'.$source_id.'/orders/?datasource=tranquility&order_type=all&page='.$page);
//decode JSON data to PHP array
$orders = json_decode($content,true);
$response_code = explode(' ', $http_response_header[0])[1];
if ($response_code == 200) {
  foreach ($orders as $0) {
    $ins[] = "($source_id,{$o['duration']},0{$o['is_buy_order']},UNIX_TIMESTAMP(STR_TO_DATE('{$o['issued']}','%Y-%m-%dT%H:%i:%sZ')),{$o['location_id']},{$o['min_volume']},{$o['order_id']},{$o['price']},{$o['range']},{$o['type_id']},{$o['volume_remain']},{$o['volume_total']},$thistime)";
  }
  if (count($ins) > 0) {
          $page++;
          while (count($ins) > 0) {
            $thisins = array_slice($ins, 0, 1000);
            $ins = array_slice($ins, 1000);
            $conn->query("INSERT INTO markets.orders VALUES " . implode(',', $thisins) . " ON DUPLICATE KEY UPDATE issued=VALUES(issued),price=VALUES(price),volume_remain=VALUES(volume_remain),last_seen=$thistime");
          }
        } else {
          $conn->query("DELETE FROM markets.orders WHERE source=$source_id AND last_seen < ($thistime-60)");
          return;
        }
      } else {
        return;
      }
    }
  ?>
