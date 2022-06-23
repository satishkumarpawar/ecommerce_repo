<?php

namespace Webkul\API\Http\Resources\UserSurvey;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSurveyCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $surveycategory = $this->surveycategory ? $this->surveycategory : $this;
        return [
            'id'            => $surveycategory->id,
            'cate_name'         => $surveycategory->cate_name,
            'cate_desc'    => $surveycategory->cate_desc,
            'cate_order'     => $surveycategory->cate_order,
            'status'          => $surveycategory->status,
            'created_at'          => $surveycategory->created_at,
            'updated_at'    => $surveycategory->updated_at
           
        ];
    }
}