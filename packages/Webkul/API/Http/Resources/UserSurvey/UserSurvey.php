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
        return [
            'id'            => $this->id,
            'question_id'         => $this->question_id,
            'answer_text'    => $this->answer_text,
            'answer_order'     => $this->answer_order,
            'default_ans_flag'          => $this->default_ans_flag,
            'user_id'          => $this->user_id,
            'survey_set_id'          => $this->survey_set_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}