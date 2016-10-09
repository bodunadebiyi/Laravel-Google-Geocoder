<?php
namespace Bodunde\GoogleGeocoder;

use GuzzleHttp\Client;

class Geocoder {
  /**
   * holds google api key
   * @var string
   */
  private $googleApiKey;

  /**
   * guzzle client
   * @var Client
   */
  private $client;

  /**
   * base URL
   */
  const API_BASE_URL = "https://maps.googleapis.com/maps/api/geocode/";

  /**
   * saves google api key and initializes guzzle client
   */
  public function __construct()
  {
    if (function_exists('config')) {
      $this->googleApiKey = config('pusher.google_api_key');
    }
    $this->client = new Client($this->guzzleClientOptions());
  }

  /**
   * makes request to google maps api to fetch coordinates of location
   * @param  String $location - location to be converted to coordinates
   * @return array            - longitude and latitude of location
   */
  public function getCoordinates($location)
  {
    $queryString = $this->constructQueryString($location, "geocoding");
    $response = $this->client->get($queryString);
    $results = json_decode($response->getBody(), true, 512);

    if (isset($results['error_message'])) {
      return null;
    }

    return $results['results'][0]['geometry']['location'];
  }

  /**
   * makes request to googlemaps api to fetch address of
   * coordinates using reverse geocoding
   * @param  float $longitude   - longitudinal coordinate
   * @param  float $latitude    - latitudinal coordinate
   * @return String             - plain address of coordinates
   */
  public function getAddress($longitude, $latitude)
  {
    $queryString = $this->constructQueryString([
      'lng' => $longitude,
      'lat' => $latitude
    ], "reverse_geocoding");

    $response = $this->client->get($queryString);
    $results = json_decode($response->getBody(), true, 512);

    if (isset($results['error_message'])) {
      return null;
    }

    return $results['results'][0]['formatted_address'];
  }

  /**
   * fetches the distance between to locations (points)
   * @param  array  $point1     - coordinates of first location
   * @param  array  $point2     - coordinates of second location
   * @param  string  $unit      - unit of location (km/mi/nmi)
   * @param  integer $decimals  - precision
   * @return string             - distance
   */
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

  /**
   * calculates the distance between two points using
   * Haversine formula
   * @param  float $point1  - coordinates of first point
   * @param  float $point2  - coordinates of second point
   * @return float          - distance between both points
   */
  private function calcDistance($point1, $point2)
  {
    return rad2deg(acos((sin(deg2rad($point1['lat'])) *
      sin(deg2rad($point2['lat']))) +
      (cos(deg2rad($point1['lat'])) *
      cos(deg2rad($point2['lat'])) *
      cos(deg2rad($point1['lng'] - $point2['lng'])))));
  }

  /**
   * constructs the query string used in the google api request
   * @param  string/array $locationOrCoordinates  - location or coordinates
   * @param  string $type                         - geocoding request or a reverse geocoding request
   * @return string
   */
  private function constructQueryString($locationOrCoordinates, $type)
  {
    switch($type) {
      case "geocoding":
        $queryString = 'json?address='.urlencode($locationOrCoordinates).'&key='.$this->googleApiKey;
        break;
      case "reverse_geocoding":
        $lat = $locationOrCoordinates['lat'];
        $lng = $locationOrCoordinates['lng'];
        $queryString = 'json?latlng='.$lat.','.$lng.'&sensor=true&key='.$this->googleApiKey;
    }

    return $queryString;
  }

  /**
   * returns options for the guzzle client
   * @return array
   */
  private function guzzleClientOptions()
  {
    return [
      'base_uri' => self::API_BASE_URL,
      'defaults' => [
        'verify' => 'true',
      ]
    ];
  }
}