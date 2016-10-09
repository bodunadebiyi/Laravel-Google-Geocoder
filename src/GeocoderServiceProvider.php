<?php
namespace Bodunde\Geocoder;

use illuminate\Support\ServiceProvider;

class GeocoderServiceProvider extends ServiceProvider {
  public function register()
  {
    $this->app->bind('geocoder', function($app)
    {
      return new Geocoder;
    });
  }

  public function boot()
  {

  }
}