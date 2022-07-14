<?php

namespace Webkul\API\Http\Resources\Core;

use Illuminate\Http\Resources\Json\JsonResource;

class Society extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'     => $this->name,
            'sector' => $this->sector,
            'city'   => $this->city,
            'district'   => $this->district,
            'state'   => $this->state,
            'postcode'   => $this->postcode,
            'description'   => $this->description,
            'status'   => $this->status,
        ];
    }
}