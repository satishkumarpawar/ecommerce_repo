<?php

namespace Webkul\API\Http\Resources\UserSurvey;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSurveySet extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $surveyset = $this->surveyset ? $this->surveyset : $this;
        if(!isset($surveyset->question_set))$surveyset->question_set=Array();
        return [
            'id'            => $surveyset->id,
            'survey_name'         => $surveyset->survey_name,
            'survey_desc'    => $surveyset->survey_desc,
            'survey_level'     => $surveyset->survey_level,
            'cash_back'  =>  $surveyset->cash_back,
            'notification_id'  =>  $surveyset->notification_id,
            'status'  =>  $surveyset->status,
            'start_date'          => $surveyset->start_date,
            'end_date'          => $surveyset->end_date,
            'created_at'    => $surveyset->created_at,
            'updated_at'    => $surveyset->updated_at,
             /* survey set details */
           'question_set'  => UserSurveySetDetail::collection($surveyset->question_set),
        ];
    }
}