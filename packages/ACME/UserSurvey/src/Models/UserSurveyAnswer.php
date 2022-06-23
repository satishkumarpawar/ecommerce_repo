<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyAnswer as UserSurveyAnswerContract;

class UserSurveyAnswer extends Model implements UserSurveyAnswerContract
{
    protected $fillable = [];
}