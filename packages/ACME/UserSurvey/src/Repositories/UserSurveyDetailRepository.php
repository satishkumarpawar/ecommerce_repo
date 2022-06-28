<?php

namespace ACME\UserSurvey\Repositories;

use Webkul\Core\Eloquent\Repository;

class UserSurveyDetailRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveyDetail';
    }

    public function getSurveyDetail($id = null)
    {
        $qb = $this->model
            ->leftJoin('user_surveys', 'user_survey_details.survey_id', '=', 'user_surveys.id')
            ->leftJoin('user_survey_questions', 'user_survey_details.question_id', '=', 'user_survey_questions.id')
            ->leftJoin('user_survey_answers', 'user_survey_details.answer_id', '=', 'user_survey_answers.id');

        if ($id) {
            $qb->where('user_survey_details.survey_id', $id);
            
        }

        return $qb->get();
    }
}