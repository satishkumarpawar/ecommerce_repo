<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


use Webkul\CMS\Repositories\CmsRepository;
use Webkul\API\Http\Resources\Pages\CmsPage as CmsPageResource;


use Webkul\CMS\Repositories\CategoryRepository;
use Webkul\API\Http\Resources\Pages\PagesCategory as PagesCategoryResource;


use JWTAuth; #SKP



class CmsPageController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    protected $CmsRepository;
    protected $CategoryRepository;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( 
        CmsRepository $CmsRepository,
        CategoryRepository $CategoryRepository
        )
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        //$this->middleware('auth:' . $this->guard);

        $this->_config = request('_config');
       
        $this->CmsRepository = $CmsRepository;
        $this->CategoryRepository = $CategoryRepository;
        
       
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return response()->json([
            'message' => 'Page no found.',
        ]);
               
    }


    public function getList()
{
    if (!is_null(request()->input('id'))) {
        return CmsPageResource::collection($this->CmsRepository->getAll(request()->input('id')));
    } else {
       return CmsPageResource::collection($this->CmsRepository->getAll());
    }
           
}

   
public function get()
{
    
    if (!is_null(request()->input('id'))) {
        return CmsPageResource::collection($this->CmsRepository->get(request()->input('id')));
    } else {
       return CmsPageResource::collection($this->CmsRepository->get());
    }
    
}

public function create()
{
    
   
    $this->validate(request(), [
        'page_title' => 'required'
    ]);

    $CmsPage=$this->CmsRepository->create(request());

    return response()->json([
        'message' => 'CmsPage  created successfully.',
        'data'    => new CmsPageResource($CmsPage),
    ]);
    
}

public function update()
{
    
   
    $this->validate(request(), [
        'page_title' => 'required'
    ]);

    $CmsPage=$this->CmsRepository->update(request());

    return response()->json([
        'message' => 'CmsPage  updated successfully.',
        'data'    => new CmsPageResource($CmsPage),
    ]);
    
}

public function delete()
{
    $CmsPage = $this->CmsRepository->findOrFail(request()->id);

    try {
       
        $this->CmsRepository->delete(request()->id);

        return response()->json(['message' => true], 200);
    } catch (\Exception $e) {
        report($e);
    }

    return response()->json(['message' => false], 400);

}


    public function getCategoryList()
{
    if (!is_null(request()->input('id'))) {
        return PagesCategoryResource::collection($this->CategoryRepository->getAll(request()->input('id')));
    } else {
       return PagesCategoryResource::collection($this->CategoryRepository->getAll());
    }
           
}

   
public function getCategory()
{
    
    if (!is_null(request()->input('id'))) {
        return PagesCategoryResource::collection($this->CategoryRepository->get(request()->input('id')));
    } else {
       return PagesCategoryResource::collection($this->CategoryRepository->get());
    }
    
}

public function createCategory()
{
    
   
    $this->validate(request(), [
        'cate_name' => 'required'
    ]);

    $PageCategory=$this->CategoryRepository->create(request());

    return response()->json([
        'message' => 'Page Category created successfully.',
        'data'    => new PagesCategoryResource($PageCategory),
    ]);
    
}

public function updateCategory()
{
    
    $this->validate(request(), [
        'cate_name' => 'required'
    ]);

    $PageCategory=$this->CategoryRepository->update(request());

    return response()->json([
        'message' => 'Page Category updated successfully.',
        'data'    => new PagesCategoryResource($PageCategory),
    ]);
    
}

public function deleteCategory()
{
    $PageCategory = $this->CategoryRepository->findOrFail(request()->id);

    try {
       
        $this->CategoryRepository->delete(request()->id);

        return response()->json(['message' => true], 200);
    } catch (\Exception $e) {
        report($e);
    }

    return response()->json(['message' => false], 400);

}




}
