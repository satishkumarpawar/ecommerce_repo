<?php

namespace Webkul\Notification\Repositories;

use Webkul\Core\Eloquent\Repository;

use Webkul\Notification\Models\NotificationSetting;

class NotificationSettingSearchRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Notification\Contracts\NotificationSetting';
    }
}