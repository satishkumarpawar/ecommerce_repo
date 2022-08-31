<?php

namespace Webkul\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Notification\Contracts\Notification as NotificationContract;

class Notification extends Model implements NotificationContract
{
    protected $table = 'notifictions';

    protected $fillable = [
        'notification_id', 
        'customer_id', 
        'notification_titile', 
        'notification_message', 
        'customer_group_id', 
        'notification_type', 
        'notification_times', 
        'notification_interval', 
        'status',
    ];
}