<?php

namespace AscentCreative\Lectionary;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Routing\Router;

class LectionaryServiceProvider extends ServiceProvider
{
    public function register()
    {
        //

        // Register the helpers php file which includes convenience functions:
        require_once (__DIR__.'/helpers.php');
    
        $this->mergeConfigFrom(
            __DIR__.'/../config/lectionary.php', 'lectionary'
        );

    }

    public function boot()
    {

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lectionary');

        $this->loadRoutesFrom(__DIR__.'/../routes/lectionary-web.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->commands([
            \AscentCreative\Lectionary\Commands\CreateLectionary::class,
        ]);
        
    }

  

    // register the components
    public function bootComponents() {

    }




  

    public function bootPublishes() {

      $this->publishes([
        __DIR__.'/../assets' => public_path('vendor/ascentcreative/lectionary'),
    
      ], 'public');

      $this->publishes([
        __DIR__.'/../config/lectionary.php' => config_path('lectionary.php'),
      ]);

    }



}