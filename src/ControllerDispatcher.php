<?php

namespace Luminee\Escalator;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class ControllerDispatcher extends \Illuminate\Routing\ControllerDispatcher
{
    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Illuminate\Routing\Route $route
     * @param  \Illuminate\Http\Request $request
     * @param  string $controller
     * @param  string $method
     * @return mixed
     */
    public function dispatch(Route $route, Request $request, $controller, $method)
    {
        if (config('escalator.enable', false)) {
            $version    = $request->header(config('escalator.header'));
            $controller = $this->getAvailableController($version, $controller, $method);
        }
        return parent::dispatch($route, $request, $controller, $method);
    }
    
    protected function getAvailableController($version, $controller, $method)
    {
        $versions = $this->listVersionsArray($controller);
        
        $r_versions = array_reverse($versions);
        if (!$version) {
            $version = $r_versions[0];
        }
        for ($i = 0; $i < count($r_versions); $i++) {
            if (version_compare($version, $r_versions[$i], '<')) {
                continue;
            }
            $version   = $r_versions[$i];
            $new_space = str_replace('.', '_', $version);
            if (!method_exists(preg_replace('/[0-9_]+/', $new_space, $controller), $method)) {
                continue;
            }
            break;
        }
        if (!in_array($version, $versions)) {
            $version = $versions[0];
        }
        $version = str_replace('.', '_', $version);
        return preg_replace('/[0-9_]+/', $version, $controller);
    }
    
    protected function listVersionsArray($controller)
    {
        $module = explode('\\', $controller)[config('escalator.module.index')];
        return config('escalator.versions.'.strtolower($module), []);
    }
}
