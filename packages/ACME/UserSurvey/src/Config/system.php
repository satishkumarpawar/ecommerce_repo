<?php

return [
    [
        'key' => 'usersurvey',
        'name' => 'User Survey',
        'sort' => 2
    ], [
        'key' => 'usersurvey.settings',
        'name' => 'Custom Settings',
        'sort' => 2,
    ], [
        'key' => 'usersurvey.settings.settings',
        'name' => 'Custom Groupings',
        'sort' => 2,
        'fields' => [
            [
                'name' => 'status',
                'title' => 'Status',
                'type' => 'boolean',
                'channel_based' => true,
                'locale_based' => false
            ]
        ]
    ]
];
