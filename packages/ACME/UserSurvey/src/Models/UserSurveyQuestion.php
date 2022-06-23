<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyQuestion as UserSurveyQuestionContract;

class UserSurveyQuestion extends Model implements UserSurveyQuestionContract
{
    protected $fillable = [];
}