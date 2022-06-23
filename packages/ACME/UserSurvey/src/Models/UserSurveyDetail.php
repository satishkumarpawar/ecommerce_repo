<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyDetail as UserSurveyDetailContract;

class UserSurveyDetail extends Model implements UserSurveyDetailContract
{
    protected $fillable = [];
}