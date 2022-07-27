<?php

namespace Webkul\API\Http\Resources\Pages;

use Illuminate\Http\Resources\Json\JsonResource;

class CmsPage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $cmspage = $this->cmspage ? $this->cmspage : $this;
         return [
            'id'            => $cmspage->id,
            'page_title'         => $cmspage->page_title,
            'url_key'    => $cmspage->url_key,
            'cate_name'    => $cmspage->cate_name,
            'html_content'     => $cmspage->html_content,
            'meta_title'          => $cmspage->meta_title,
            'meta_description'          => $cmspage->meta_description,
            'meta_keywords'    => $cmspage->meta_keywords,
            'locale'    => $cmspage->locale,
            'cms_page_id'    => $cmspage->cms_page_id,
            'category_id'    => $cmspage->category_id,
            
        ];
    }
}