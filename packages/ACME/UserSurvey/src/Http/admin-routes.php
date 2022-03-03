<?php
#SKP
Route::group([
    'prefix'        => 'admin/usersurvey',
    'middleware'    => ['web', 'admin']
], function () {

    Route::get('/', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.index',
    ])->name('usersurvey.admin.index');

    // Catalog Category Routes
    Route::get('/categories', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.categories.index',
    ])->name('usersurvey.admin.categories.index');

    Route::get('/categories/create', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@create')->defaults('_config', [
        'view' => 'usersurvey::admin.categories.create',
    ])->name('usersurvey.admin.categories.create');

    Route::post('/categories/create', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@store')->defaults('_config', [
        'redirect' => 'usersurvey.admin.categories.index',
    ])->name('usersurvey.admin.categories.store');

    Route::get('/categories/edit/{id}', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@edit')->defaults('_config', [
        'view' => 'usersurvey::admin.categories.edit',
    ])->name('usersurvey.admin.categories.edit');

    Route::put('/categories/edit/{id}', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@update')->defaults('_config', [
        'redirect' => 'usersurvey.admin.categories.index',
    ])->name('usersurvey.admin.categories.update');

    Route::post('/categories/delete/{id}', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@destroy')->name('usersurvey.admin.categories.delete');

    //category massdelete
    Route::post('categories/massdelete', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@massDestroy')->defaults('_config', [
        'redirect' => 'usersurvey.admin.categories.index',
    ])->name('usersurvey.admin.categories.massdelete');

    Route::post('/categories/question/count', 'ACME\UserSurvey\Http\Controllers\Admin\SurveyCategoryController@categoryQuestionCount')->name('usersurvey.admin.categories.question.count');


     
    Route::get('/questions', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.questions',
    ])->name('usersurvey.admin.questions');

    Route::get('/questionsets', 'ACME\UserSurvey\Http\Controllers\Admin\UserSurveyController@index')->defaults('_config', [
        'view' => 'usersurvey::admin.questionsets',
    ])->name('usersurvey.admin.questionsets');

});
