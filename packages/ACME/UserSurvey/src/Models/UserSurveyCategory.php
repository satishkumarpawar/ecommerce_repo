<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveyCategory as UserSurveyCategoryContract;

class UserSurveyCategory extends Model implements UserSurveyCategoryContract
{
    protected $fillable = [
        'cate_name',
        'cate_desc',
        'cate_order',
        'status'
    ];

  

}