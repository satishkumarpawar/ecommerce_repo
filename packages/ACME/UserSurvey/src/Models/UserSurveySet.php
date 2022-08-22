<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveySet as UserSurveySetContract;

class UserSurveySet extends Model implements UserSurveySetContract
{
    protected $fillable = [
    'survey_name',
    'survey_desc',
    'survey_level',
    'cash_back',
    'notification_id',
    'status',
    'start_date',
    'end_date'
    ];
  
    
   public function surveysetdetails()
    {
        return $this->hasMany(UserSurveySetDetailProxy::modelClass(), 'survey_set_id');
    }
}

