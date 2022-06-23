<?php

namespace Webkul\API\Http\Resources\UserSurvey;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSurveySetDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
  /*  public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'question_id'         => $this->question_id,
            'survey_set_id'    => $this->survey_set_id
        ];
    }*/
    public function toArray($request)
    {
        $surveysetdetail = $this->surveysetdetail ? $this->surveysetdetail : $this;
        if(!isset($surveysetdetail->answer_options))$surveysetdetail->answer_options=Array();
        
        return [
            "question_id" => $this->question_id,
            "survey_set_id" => $this->survey_set_id,
            "cate_id" => $this->cate_id,
            "question_text" => $this->question_text,
            "answer_options" => UserSurveyAnswer::collection($surveysetdetail->answer_options),
        ];
    }
}