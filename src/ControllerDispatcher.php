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
            $version    = $request->header(config('escalator.header', 'version'));
            $controller = $this->insertVersionIntoSpace($controller);
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
            if (method_exists(preg_replace('/[0-9_]+/', $new_space, $controller), $method)) {
                break;
            }
        }
        if (!in_array($version, $versions)) {
            $version = $versions[0];
        }
        return preg_replace('/[0-9_]+/', str_replace('.', '_', $version), $controller);
    }
    
    protected function listVersionsArray($controller)
    {
        $module   = explode('\\', $controller)[config('escalator.option.module_index', 3)];
        $versions = config('escalator.versions.'.strtolower($module), []);
        
        if (empty($versions)) {
            $exception = 'There is no version array in module: '.$module.'. '.
                'Please define version list in '.config_path().'\escalator.php';
            throw new \Exception($exception);
        }
        $older = 0;
        foreach ($versions as $version) {
            if (!version_compare($older, $version, '<')) {
                $exception = 'You have wrong version order: ['.implode(', ', $versions).'], '.
                    'please sort it by version!';
                throw new \Exception($exception);
            }
            $older = $version;
        }
        return $versions;
    }
    
    protected function insertVersionIntoSpace($controller)
    {
        $prefix      = config('escalator.option.version_prefix', 'v');
        $min_version = config('escalator.option.min_version', '1.0');
        $space       = $prefix.str_replace('.', '_', $min_version);
        $spaces      = explode('\\', $controller);
        array_splice($spaces, config('escalator.option.version_index', 4), 0, $space);
        return implode('\\', $spaces);
    }
}
