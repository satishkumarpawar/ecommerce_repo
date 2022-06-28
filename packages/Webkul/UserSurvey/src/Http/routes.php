<?php
Route::view('/user-survey', 'usersurvey::usersurvey.usersurvey');
Route::get('survey-dashboard', 
'Webkul\Http\Controllers\UserSurveyController@index')->defaults('_config', 
['view' => 'usersurvey::usersurvey.index'])->name('usersurvey.index');