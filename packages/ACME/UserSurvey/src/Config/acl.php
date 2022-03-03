<?php
return [
    [
        'key'        => 'usersurvey',
        'name'       => 'User Survey',
        'route'      => 'usersurvey.admin.index',
        'sort'       => 2
      ],
      [
        'key'        => 'usersurvey.surveylist',
        'name'       => 'Survey List',
        'route'      => 'usersurvey.admin.index',
        'sort'       => 1
      ],
      [
        'key'        => 'usersurvey.category',
        'name'       => 'Survey Category',
        'route'      => 'usersurvey.admin.category',
        'sort'       => 2
      ],
      [
        'key'        => 'usersurvey.questions',
        'name'       => 'Survey Questions',
        'route'      => 'usersurvey.admin.questions',
        'sort'       => 3
      ],
      [
        'key'        => 'usersurvey.questionsets',
        'name'       => 'Survey Question Sets',
        'route'      => 'usersurvey.admin.questionsets',
        'sort'       => 4
      ]
];
