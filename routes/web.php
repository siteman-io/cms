<?php

use Illuminate\Support\Facades\Route;
use Siteman\Cms\Http\Middleware\InjectAdminBar;
use Siteman\Cms\Http\SitemanController;

Route::middleware(['web', InjectAdminBar::class])->group(function () {
    Route::get('/{slug?}', SitemanController::class)->where('slug', '.*')->name('siteman.page')->fallback();
});
