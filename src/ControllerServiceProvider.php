<?php

namespace Luminee\Escalator;

use Illuminate\Support\ServiceProvider;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([realpath(__DIR__.'/../config/escalator.php') => config_path('escalator.php')]);
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('illuminate.route.dispatcher', function ($app) {
            return new ControllerDispatcher($app['router'], $app);
        });
        
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/escalator.php'), 'escalator');
    }
}
