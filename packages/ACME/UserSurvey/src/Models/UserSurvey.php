<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurvey as UserSurveyContract;

class UserSurvey extends Model implements UserSurveyContract
{
    protected $fillable = [];
}