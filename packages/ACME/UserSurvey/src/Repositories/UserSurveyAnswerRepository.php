<?php

namespace ACME\UserSurvey\Repositories;

use Webkul\Core\Eloquent\Repository;
use ACME\UserSurvey\Models\UserSurveyAnswer;


class UserSurveyAnswerRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveyAnswer';
    }


    public  function getSurveyAnswers($id = null)
    {
        $qb = $this->model
        ->distinct()
        ->addSelect('user_survey_answers.*');
           
        if ($id) {
            $qb->where('user_survey_answers.question_id', $id);
           
        }

        return $qb->get();
    }

    
}