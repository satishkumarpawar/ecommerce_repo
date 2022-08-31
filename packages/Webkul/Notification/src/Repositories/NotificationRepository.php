<?php

namespace Webkul\Notification\Repositories;

use Webkul\Core\Eloquent\Repository;

class NotificationRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Notification\Contracts\Notification';
    }
}