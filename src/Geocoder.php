<?php
namespace Bodunde\Geocoder;

use GuzzleHttp\Client;

class Geocoder {
  private $googleApiKey;
  private $client;
  const API_BASE_URL = "https://maps.googleapis.com/maps/api/geocode/";


  public function __construct($config=null)
  {
    if ($config) {
      $this->$googleApiKey = $config['google_api_key'];
    }

    $this->client = new Client([
      'base_uri' => self::API_BASE_URL,
      'defaults' => [
        'verify' => 'true',
      ]
    ]);
  }

  public function getCoordinates($location)
  {
    $queryString = $this->constructQueryString($location, "geocoding");
    $response = $this->client->get($$queryString);
    $results = json_decode($response->getBody(), true, 512);

    if (isset($results['error_message'])) {
      return null;
    }

    return $results['results'][0]['geometry']['location'];
  }

  public function getStreetAddress($longitude, $latitude)
  {
    $queryString = $this->constructQueryString([
      'lng' => $longitude,
      'lat' => $latitude
    ], "reverse_geocoding");
  }

  public function getDistanceBetween($point1, $point2, $unit = 'km', $decimals = 2)
  {
    // Calculate the distance in degrees using Hervasine formula
    $degrees = $this->calcDistance($point1, $point2);

    // Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
    switch ($unit) {
      case 'km':
      // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
      $distance = $degrees * 111.13384;
      break;
      case 'mi':
      // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
      $distance = $degrees * 69.05482;
      break;
      case 'nmi':
      // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
      $distance = $degrees * 59.97662;
    }

    return round($distance, $decimals);
  }

  private function calcDistance($point1, $point2)
  {
    return rad2deg(acos((sin(deg2rad($point1['lat'])) *
      sin(deg2rad($point2['lat']))) +
      (cos(deg2rad($point1['lat'])) *
      cos(deg2rad($point2['lat'])) *
      cos(deg2rad($point1['lng'] - $point2['lng'])))));
  }

  private function constructQueryString($locationOrCoordinates, $type)
  {
    switch($type) {
      case "geocoding":
        $queryString = 'json?address='.urlencode($locationOrCoordinates).'&key='.$this->googleApiKey;
        break;
      case "reverse_geocoding":
        $lat = $locationOrCoordinates['lat'];
        $lng = $locationOrCoordinates['lng']
        $queryString = 'json?latlng='.$lat.','.$lng.'&sensor=true&key='.$this->googleApiKey;
    }

    return $queryString;
  }
}