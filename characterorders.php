<?php

//do i need to have namespace?

use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Extractions;
use Log;
use App\CharacterInfo;


//what do these lines do?
//trait StructureGet {
//public function getStructures($character) {
//  $alert = new \stdClass();
//  $tz = array("T", "Z");

//create client base URI
$client = new Client(['base_uri' => 'https://esi.tech.ccp.is/latest/']);

//set headers
$auth_headers = [
    'headers' => [
    'User-Agent' => env('USERAGENT'),
    ],
    'query' => [
      //choose tranquility server as datasource
      'datasource' => 'tranquility',
      //specify order type
      'order_type' => 'all',
      //need to specify $character access_token
      'token'   => $character->access_token
    ]
  ];


//Character orders


try {

  $characterorders_url = "/characters/92497102/orders/";
        $content = $client->get(characterorders_url, $auth_headers);
        //decode JSON data to PHP array
        $json = json_decode($content, true);

   } catch (ServerException $e ) {
     //5xx error, usually and issue with ESI
     Log::error("ServerException thrown on Order fetch: " . $e->getMessage());
     $msg = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
     $alert->{'exception'} = $msg;
     return $alert;
   } catch (\Exception $e) {
     //Everything else
     Log::error("Exception thrown on Order fetch: " . $e->getMessage());
     $msg = "We failed to pull your rolls, please try again later.";
     $alert->{'exception'} = $msg;
     return $alert;
   }

//Fetch the details of orders
      $is_corporation = $json['is_corporation'];
      $duration = $json['duration'];
      $order_id = $json['order_id'];
      $type_id = $json['type_id'];
      $region_id = $json['region_id'];
      $location_id = $json['location_id'];
      $range = $json['range'];
      $price = $json['price'];
      $volume_total = $json['volume_total'];
      $volume_remain = $json['volume_remain'];
      $issued = $json['issued'];
      $is_buy_order = $json['is_buy_order'];
      $min_volume = $json['min_volume'];
      $escrow = $json['escrow'];



//update or create entry into DB
publicorders::updateOrCreate(
          ['order_id' =>  $order_id],
          ['is_corporation' =>  $is_corporation,
           'duration' => $duration,
           'type_id' => $type_id,
           'region_id' => $region_id,
           'location_id' => $location_id,
           'range' => $range,
           'price' => $price,
           'volume_total' => $volume_total,
           'volume_remain' => $volume_remain,
           'issued' => $issued,
           'is_buy_order' => $is_buy_order,
           'min_volume' => $min_volume,
           'escrow' => $escrow,
           ]
        )->touch();

?>
