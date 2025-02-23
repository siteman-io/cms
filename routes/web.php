<?php

use Illuminate\Support\Facades\Route;
use Siteman\Cms\Http\SitemanController;

Route::middleware(config('siteman.middleware', []))->group(function () {
    Route::get('/{slug?}', SitemanController::class)
        ->where('slug', '.*')
        ->name('siteman')
        ->fallback();
});
