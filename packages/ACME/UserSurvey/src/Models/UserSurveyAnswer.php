<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyAnswer as UserSurveyAnswerContract;

class UserSurveyAnswer extends Model implements UserSurveyAnswerContract
{
    public $timestamps = false;
    
    protected $fillable = [
        'question_id',
        'answer_text',
        'answer_order',
        'default_ans_flag'
    ];

    public function question() {
        return $this->belongsTo(UserSurveyQuestionProxy::modelClass(), 'id');
    }

}