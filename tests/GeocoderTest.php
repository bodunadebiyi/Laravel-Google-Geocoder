<?php
namespace Bodunde\GoogleGeocoder\Tests;

use Bodunde\GoogleGeocoder\Geocoder;

class GeocoderTest extends \PHPUnit_Framework_TestCase
{
  private $geocoder;

  public function setUp()
  {
    $this->geocoder = new Geocoder;
  }

  public function testSample()
  {
    $this->assertEquals(1, 1);
  }

  public function testGetCoordinates()
  {
    $results = $this->geocoder->getCoordinates("shomolu");

    $this->assertInternalType('array', $results);
    $this->assertTrue(is_float($results['lat']));
    $this->assertTrue(is_float($results['lng']));
  }

  public function testGetAddress()
  {
    $longitude = 3.3892894;
    $latitude = 6.5349646;
    $results = $this->geocoder->getAddress($longitude, $latitude);

    $this->assertInternalType('string', $results);
  }

  public function testGetDistanceBetween()
  {
    $point1 = [
      "lat" => 6.5349646,
      "lng" => 3.3892894
    ];

    $point2 = [
      "lat" => 6.601838,
      "lng" => 3.3514863
    ];

    $result = $this->geocoder->getDistanceBetween($point1, $point2);

    $this->assertInternalType("float", $result);
    $this->assertTrue(!empty($result));
  }
}