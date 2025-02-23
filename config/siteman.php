<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the used models in Siteman. This includes
    | the user model, which is used for authentication, authorization and as author
    |
    */

    'models' => [
        'user' => 'App\Models\User',
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | Siteman uses themes to render content. Your available themes are defined
    | here. Installed packages can add themes to this configuration as well.
    |
    */

    'themes' => [
        \Siteman\Cms\Theme\BlankTheme::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware are applied to all Siteman routes in the Frontend.
    |
    */

    'middleware' => [
        'web',
        \Siteman\Cms\Http\Middleware\InjectAdminBar::class,
    ],
];
