<?php

namespace ACME\UserSurvey\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\UserSurvey\Contracts\UserSurveySetDetail as UserSurveySetDetailContract;

class UserSurveySetDetail extends Model implements UserSurveySetDetailContract
{
    public $timestamps = false;
    protected $fillable = [
        "question_id",
        "survey_set_id"
    ];

    public function surveyset() {
        return $this->belongsTo(UserSurveySetProxy::modelClass(), 'id');
    }
}