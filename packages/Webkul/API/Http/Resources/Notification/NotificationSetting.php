<?php

namespace Webkul\API\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSetting extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $notification_setting = $this->notification_setting ? $this->notification_setting : $this;
       
        return [
            'id'            => $this->id,
            'notification_titile'         => $this->notification_titile,
            'notification_message'    => $this->notification_message,
            'customer_group_id'         => $this->customer_group_id,
            'notification_type'    => $this->notification_type,
            'notification_times'         => $this->notification_times,
            'notification_interval'    => $this->notification_interval,
            'status'    => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
            ];
    }
}