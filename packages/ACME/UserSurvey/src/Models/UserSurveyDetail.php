<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyDetail as UserSurveyDetailContract;

class UserSurveyDetail extends Model implements UserSurveyDetailContract
{
    protected $fillable = [
        'survey_id',
        'question_id',
        'answer_id',
        'answer_text'
        
    ];
}