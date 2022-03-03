<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\API\Http\Resources\Catalog\Product as ProductResource;

class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!is_null(request()->input('featured'))) {
            return ProductResource::collection($this->productRepository->getFeaturedProducts(!is_null(request()->input('limit'))?request()->input('limit'):0));
        } else if(!is_null(request()->input('new'))) {
            return ProductResource::collection($this->productRepository->getNewProducts(!is_null(request()->input('limit'))?request()->input('limit'):0));
        } else if(!is_null(request()->input('specials'))) {
            return ProductResource::collection($this->productRepository->getSpecialsProducts(!is_null(request()->input('limit'))?request()->input('limit'):0));
        } else if(!is_null(request()->input('bestselling'))) {
            return ProductResource::collection($this->productRepository->getBestSellingProducts(!is_null(request()->input('limit'))?request()->input('limit'):0));
        } else {
           return ProductResource::collection($this->productRepository->getAll(request()->input('category_id')));
        }
        
        
    }

    /**
     * Returns a individual resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        return new ProductResource(
            $this->productRepository->findOrFail($id)
        );
    }

    /**
     * Returns product's additional information.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function additionalInformation($id)
    {
        return response()->json([
            'data' => app('Webkul\Product\Helpers\View')->getAdditionalData($this->productRepository->findOrFail($id)),
        ]);
    }

    /**
     * Returns product's additional information.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configurableConfig($id)
    {
        return response()->json([
            'data' => app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($this->productRepository->findOrFail($id)),
        ]);
    }
}
