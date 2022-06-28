<?php

namespace Webkul\API\Http\Resources\UserSurvey;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSurveyQuestion extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $surveyquestion = $this->surveyquestion ? $this->surveyquestion : $this;
        if(!isset($surveyquestion->answer_options))$surveyquestion->answer_options=Array();
        return [
            'id'            => $surveyquestion->id,
            'cate_id'         => $surveyquestion->cate_id,
            'question_text'    => $surveyquestion->question_text,
            'question_order'     => $surveyquestion->question_order,
            'question_lock'          => $surveyquestion->question_lock,
            'status'          => $surveyquestion->status,
            'created_at'    => $surveyquestion->created_at,
            'updated_at'    => $surveyquestion->updated_at,
            'answer_options'  => UserSurveyAnswer::collection($surveyquestion->answer_options),
        ];
    }
}