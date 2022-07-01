<?php

namespace Webkul\API\Http\Resources\UserSurvey;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSurvey extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $survey = $this->survey ? $this->survey : $this;
        if(!isset($survey->answer_set))$survey->answer_set=Array();
        return [
            'id'            => $this->id,
            'user_id'         => $this->user_id,
            'survey_set_id'    => $this->survey_set_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            //'answer_set'  => UserSurveyDetail::collection($survey->answer_set)
        ];
    }
}