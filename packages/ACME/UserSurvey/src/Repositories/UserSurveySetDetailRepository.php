<?php

namespace ACME\UserSurvey\Repositories;

use Webkul\Core\Eloquent\Repository;

class UserSurveySetDetailRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveySetDetail';
    }

    public function getSurveySetDetail($id = null)
    {
        $qb = $this->model
            ->leftJoin('user_survey_questions', 'user_survey_set_details.question_id', '=', 'user_survey_questions.id');

        if ($id) {
            $qb->where('user_survey_set_details.survey_set_id', $id);
            $qb->where('user_survey_questions.status', 1);
        }

        return $qb->get();
    }

}