<?php

namespace Webkul\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\CMS\Contracts\CmsPagesCategory as CmsPagesCategoryContract;

class CmsPagesCategory extends Model implements CmsPagesCategoryContract
{
    protected $fillable = [
        'cate_name',
        'cate_desc',
        'cate_order',
        'status',
        'restrict'
    ];
}