<?php

namespace  ACME\UserSurvey\Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use  ACME\UserSurvey\Contracts\Category as CategoryContract;
use  ACME\UserSurvey\Repositories\CategoryRepository;
use Webkul\Product\Models\ProductProxy;

/**
 * Class Category
 *
 * @package Webkul\Category\Models
 *
 * @property-read string $url_path maintained by database triggers
 */
class Category extends Model implements CategoryContract{

    use NodeTrait;

    

    protected $fillable = [
        'cate_order',
        'status',
        'cate_desc',
        'cate_name',
        'id',
    ];

 


    /**
     * The products that belong to the category.
     */
    public function questions()
    {
        //return $this->belongsToMany(ProductProxy::modelClass(), 'product_categories');
    }
}