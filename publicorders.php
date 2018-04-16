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
      //not needed for public requests
      //need to specify $character access_token
      'token'   => $character->access_token
    ]
  ];


//Public region Orders


try {
  //specify typeids
  //do i need to have tranquility specified in this url if it is in $auth_headers??
  //$regionorders_url = "/markets/10000002/orders/?datasource=tranquility&order_type=all&page=1type_id=???";
  $regionorders_url = "/markets/10000002/orders/";
        $content = $client->get(regionorders_url, $auth_headers);
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
      $order_id = $json['order_id'];
      $type_id = $json['type_id'];
      $location_id = $json['location_id'];
      $system_id = $json['system_id'];
      $volume_total = $json['volume_total'];
      $volume_remain = $json['volume_remain'];
      $min_volume = $json['min_volume'];
      $price = $json['price'];
      $is_buy_order = $json['is_buy_order'];
      $duration = $json['duration'];
      $issued = $json['issued'];
      $range = $json['range'];

//update or create entry into DB
publicorders::updateOrCreate(
          ['order_id' =>  $order_id],
          ['type_id' => $type_id,
           'location_id' => $location_id,
           'system_id' => $system_id,
           'volume_total' => $volume_total,
           'volume_remain' => $volume_remain,
           'min_volume' => $min_volume,
           'price' => $price,
           'is_buy_order' => $is_buy_order,
           'duration' => $duration,
           'issued' => $issued,
           'range' => $range,
           ]
        )->touch();




?>
