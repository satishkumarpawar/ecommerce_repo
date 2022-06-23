<?php

namespace ACME\UserSurvey\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use ACME\UserSurvey\Models\UserSurveySet;
use ACME\UserSurvey\Models\UserSurveySetDetail;
use ACME\UserSurvey\Models\UserSurveyQuestion;
use ACME\UserSurvey\Models\UserSurveyAnswer;
use ACME\UserSurvey\Repositories\UserSurveySetDetailRepository;
use ACME\UserSurvey\Repositories\UserSurveyQuestionRepository;
use ACME\UserSurvey\Repositories\UserSurveyAnswerRepository;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;




class UserSurveySetSearchRepository extends Repository
{
 
   

     /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveySet';
    }

    

}