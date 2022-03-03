<?php
#SKP
return [
    [
      'key'        => 'usersurvey',
      'name'       => 'User Survey',
      'route'      => 'usersurvey.admin.index',
      'sort'       => 2,
      'icon-class' => 'dashboard-icon',
    ],
    [
      'key'        => 'usersurvey.surveylist',
      'name'       => 'Survey List',
      'route'      => 'usersurvey.admin.index',
      'sort'       => 1,
      'icon-class' => 'dashboard-icon',
    ],
    [
      'key'        => 'usersurvey.category',
      'name'       => 'Survey Category',
      'route'      => 'usersurvey.admin.categories.index',
      'sort'       => 2,
      'icon-class' => 'dashboard-icon',
    ],
    [
      'key'        => 'usersurvey.questions',
      'name'       => 'Survey Questions',
      'route'      => 'usersurvey.admin.questions',
      'sort'       => 3,
      'icon-class' => 'dashboard-icon',
    ],
    [
      'key'        => 'usersurvey.questionsets',
      'name'       => 'Survey Question Sets',
      'route'      => 'usersurvey.admin.questionsets',
      'sort'       => 4,
      'icon-class' => 'dashboard-icon',
    ]
];