<?php

namespace Webkul\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Notification\Contracts\NotificationSetting as NotificationSettingContract;

class NotificationSetting extends Model implements NotificationSettingContract
{
    protected $table = 'notification_settings';

    protected $fillable = [
        'notification_titile', 
        'notification_message', 
        'customer_group_id', 
        'notification_type', 
        'notification_times', 
        'notification_interval', 
        'status',
    ];
}