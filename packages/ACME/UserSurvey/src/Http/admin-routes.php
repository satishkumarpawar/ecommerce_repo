<?php
#SKP
Route::group([
    'prefix'        => 'admin/usersurvey',
    'middleware'    => ['web', 'admin']
], function () {

    Route::get('/', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.index',
    ])->name('usersurvey.admin.index');

    Route::get('/categories', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.categories',
    ])->name('usersurvey.admin.categories');
     
    Route::get('/questions', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.questions',
    ])->name('usersurvey.admin.questions');

    Route::get('/questionsets', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.questionsets',
    ])->name('usersurvey.admin.questionsets');

});
