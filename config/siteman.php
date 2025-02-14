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
    | Siteman uses a theme to render content. You can specify the theme here.
    |
    */

    'theme' => \Siteman\Cms\Theme\BlankTheme::class,
];
