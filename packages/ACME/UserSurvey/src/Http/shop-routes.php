<?php

Route::group([
        'prefix'     => 'usersurvey',
        'middleware' => ['web', 'theme', 'locale', 'currency']
    ], function () {

        Route::get('/', 'ACME\UserSurvey\Http\Controllers\Shop\UserSurveyController@index')->defaults('_config', [
            'view' => 'usersurvey::shop.index',
        ])->name('shop.usersurvey.index');

});