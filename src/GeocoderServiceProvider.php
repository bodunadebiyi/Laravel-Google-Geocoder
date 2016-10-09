<?php
namespace Bodunde\GoogleGeocoder;

use illuminate\Support\ServiceProvider;

class GeocoderServiceProvider extends ServiceProvider {
  public function register()
  {
    $this->app->bind('geocoder', function($app)
    {
      return new Geocoder;
    });

    $this->mergeConfigFrom(__DIR__.'/../config/geocoder.php', 'geocoder');
  }

  public function boot()
  {
    $this->publishes([
      __DIR__.'/../config/geocoder.php' => config_path('geocoder.php')
    ]);
  }
}