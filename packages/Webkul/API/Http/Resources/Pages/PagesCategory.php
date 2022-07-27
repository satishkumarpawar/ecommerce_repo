<?php

namespace Webkul\API\Http\Resources\Pages;

use Illuminate\Http\Resources\Json\JsonResource;

class PagesCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $pagecategory = $this->pagecategory ? $this->pagecategory : $this;
        return [
            'id'            => $pagecategory->id,
            'cate_name'         => $pagecategory->cate_name,
            'cate_desc'    => $pagecategory->cate_desc,
            'cate_order'     => $pagecategory->cate_order,
            'status'          => $pagecategory->status,
            'restrict'          => $pagecategory->restrict,
            'created_at'          => $pagecategory->created_at,
            'updated_at'    => $pagecategory->updated_at
           
        ];
    }
}