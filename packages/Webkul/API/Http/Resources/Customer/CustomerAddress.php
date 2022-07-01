<?php

namespace Webkul\API\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddress extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $address1=explode('-', $this->address1);
        if(isset($address1[1]))$address2=explode(',', $address1[1]);
        return [
            'id'           => $this->id,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'company_name' => $this->company_name,
            'vat_id'       => $this->vat_id,
            'address1'     => explode(PHP_EOL, $this->address1),
            "tower"        => (isset($address1[0])?$address1[0]:''),
            "flat"         => (isset($address2[0])?$address2[0]:''),
            "socity"         => (isset($address2[1])?$address2[1]:''),
            'country'      => $this->country,
            'country_name' => core()->country_name($this->country),
            'state'        => $this->state,
            'city'         => $this->city,
            'postcode'     => $this->postcode,
            'phone'        => $this->phone,
            'is_default'   => $this->default_address,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}