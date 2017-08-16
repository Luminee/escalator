<?php

namespace Luminee\Escalator;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class LumenServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        app()->configure('escalator');
        $dispatcher = new LumenDispatcher();
        $routes     = app()->getRoutes();
        $method     = app('request')->method();
        $pathInfo   = app('request')->path();
        $action     = $routes[$method.$pathInfo]['action'];
        $action     = $dispatcher->dispatchAction(app('request'), $action['uses']);
        
        $routes[$method.$pathInfo]['action']['uses'] = $action;
        app()->setRoutes($routes);
    }
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/escalator.php'), 'escalator');
    }
    
}
