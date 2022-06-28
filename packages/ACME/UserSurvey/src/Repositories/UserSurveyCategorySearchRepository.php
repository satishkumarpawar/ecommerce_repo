<?php

namespace ACME\UserSurvey\Repositories;

use Webkul\Core\Eloquent\Repository;

class UserSurveyCategorySearchRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveyCategory';
    }
}