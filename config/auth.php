<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'admins',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'customers',
        ],

        'web' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'customer' => [
            'driver' => 'session',
            'provider' => 'customers'
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins'
        ],

        'admin-api' => [
            'driver' => 'jwt',
            'provider' => 'admins',
        ]
    ],

    'providers' => [
        'customers' => [
            'driver' => 'eloquent',
            'model' => Webkul\Customer\Models\Customer::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => Webkul\User\Models\Admin::class,
        ]
    ],

    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => 'admin_password_resets',
            'expire' => 60,
        ],
        'customers' => [
            'provider' => 'customers',
            'table' => 'customer_password_resets',
            'expire' => 60,
        ],
    ],
];
