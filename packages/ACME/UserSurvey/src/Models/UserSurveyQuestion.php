<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyQuestion as UserSurveyQuestionContract;

class UserSurveyQuestion extends Model implements UserSurveyQuestionContract
{
    protected $fillable = [
        'cate_id',
        'question_text',
        'question_order',
        'question_lock',
        'status'
    ];

    public function answeroptions()
    {
        return $this->hasMany(UserSurveyAnswerProxy::modelClass(), 'question_id');
    }
}