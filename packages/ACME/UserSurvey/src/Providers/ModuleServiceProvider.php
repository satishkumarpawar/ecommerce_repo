<?php

namespace ACME\UserSurvey\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \ACME\UserSurvey\Models\UserSurveySet::class,
        \ACME\UserSurvey\Models\UserSurveySetDetail::class,
        \ACME\UserSurvey\Models\UserSurveyQuestion::class,
        \ACME\UserSurvey\Models\UserSurveyAnswer::class,
        \ACME\UserSurvey\Models\UserSurvey::class,
        \ACME\UserSurvey\Models\UserSurveyDetail::class,
        \ACME\UserSurvey\Models\UserSurveyCategory::class
    ];
}