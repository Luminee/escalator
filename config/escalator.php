<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Custom header for version
    |--------------------------------------------------------------------------
    |
    | This is the marker for version in the header, you should keep unanimous
    | between frontend header and this. And the version must be number and
    | dot like: 1.0 or 1.0.5, so that the escalator can recognize it.
    |
    */
    
    'header' => 'version',
    
    /*
    |--------------------------------------------------------------------------
    | Option of escalator
    |--------------------------------------------------------------------------
    |
    | You should define your controller namespace with module and version like:
    | 'namespace App\Http\Controllers\User\v1_0;' so the escalator can get
    | the module, then fetch versions by the module through array below.
    |
    */
    
    'option' => [
        
        'module_index' => 3,
        
        'version_index' => 4,
        
        'version_prefix' => 'v',
        
        'min_version' => '1.0'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Module versions list
    |--------------------------------------------------------------------------
    |
    | This store the list of modules, you must write
    | down the most complete versions and sort
    | them into a version order.
    |
    */
    
    'versions' => [
    
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Enable version router
    |--------------------------------------------------------------------------
    |
    | If this is false, it will use laravel router.
    |
    */
    
    'enable' => true

];
