# Laravel-Geocoder
A Laravel Package that interfaces with the google maps API to help convert a plain address to longitude and latitude coordinates and vice-versa. It can also calculate the distance (in kilometers or miles) between two locations using their coordinates.

## Installation
- include the package in your `composer.json` file and run `composer update` or run `composer require bodunde/geocoder` or you can do it oldschool and just clone the repo and include it in your application.
- navigate to your `config` directory in your laravel application and locate the `app.php` file
- add this line to your service provide `Bodunde\GoogleGeocoder\GeocoderServiceProvider::class`
- publish its config file by running `php artisan vendor:publish`
- include your `google_api_key` in `geocoder.php` file located in the config directory
Voila!!! You are done

## Usage
```php
<?php
namespace Your\Namespace;

use Bodunde\GoogleGeocoder\Geocoder;
...
...
// using dependency injection
public function index(Geocoder $geocoder)
{
  // get coordinates
  $coordinates = $geocoder->getCoordinates('55 Moleye Street, Yaba');

  // get address via reverse geocoding
  $addressCoordinates = [
    "lat" => 6.5349646,
    "lng" => 3.3892894
  ];
  $address = $geocoder->getAddress($addressCoordinates);

  // get distance between two locations
  $location1 = [
    "lat" => 6.5349646,
    "lng" => 3.3892894
  ];

  $location2 = [
    "lat" => 6.601838,
    "lng" => 3.3514863
  ];
  $distance = $geocoder->getDistanceBetween($location1, $location2);

  /** geocoder can also be instantiated normally without DI */
  //e.g $geocoder = new Geocoder;
}
```

## Tests
In the package root run `phpunit`
